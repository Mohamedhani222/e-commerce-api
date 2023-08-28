<?php

namespace App\Http\Repositories;

use App\Http\interfaces\OrderInterface;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use function App\check_user_has_order;
use function App\search_model;

class OrderRepository implements OrderInterface
{

    public function index($request)
    {
        $user = Auth::guard('sanctum')->user();

        if ($user->hasRole('SuperAdmin')) {

            return $this->getOrdersForSuperAdmin($request);

        } elseif ($user->hasRole(["user"])) {

            return $this->getOrdersForUser($request);
        }

        return response()->json([
            'message' => 'error'
        ], 500);

    }

    // add_to_cart
    public function store($request)
    {
        DB::beginTransaction();
        try {
            $user = Auth::guard('sanctum')->user();
            $product = Product::findorFail($request->product_id);
            $qty = $request->input('qty');

            $order = $user->cart()->firstOrCreate(['user' => $user->id]);
            $this->updateOrderAndOrderItem($order, $product, $qty);

            DB::commit();
            return response()->json([
                'message' => 'Added To Cart Successfully'
            ], 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], 403);

        }
    }


    public function show($id)
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if ($user->hasRole('user')) {
                if (!$user->all_orders()->pluck('id')->contains($id)) {
                    return response()->json([
                        'message' => 'you dont have order with this id'
                    ], 404);
                }
                $order = Order::findorFail($id);
                $order->load('order_items.product');
                return new OrderResource($order);

            } elseif ($user->hasRole('SuperAdmin|admin')) {
                $order = Order::findorFail($id);
                $order->load('order_items.product');
                return new OrderResource($order);

            }
            return response()->json([
                'message' => 'error'
            ], 403);


        } catch (ModelNotFoundException) {
            return response()->json([
                'message' => 'not found '
            ], 404);

        }

    }

    public function destroy($order)
    {
        DB::beginTransaction();
        try {
            $order->order_items()->delete();
            $order->delete();

            DB::commit();
            return response()->json([
                'message' => 'removed from cart successfully'
            ], 200);

        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Item Not Found !'
            ], 403);

        }
    }

    public function add_qty($request)
    {
        DB::beginTransaction();
        try {

            $user = Auth::guard('sanctum')->user();


            if (!check_user_has_order($request)) {
                throw new \Exception('Invalid order_id');

            }

            $order_item = OrderItem::findorFail($request->item_id);

            $order_item->order->total_price += $order_item->unit_price;
            $order_item->order->save();

            $order_item->quantity += 1;
            $order_item->total_price += $order_item->unit_price;
            $order_item->save();
            DB::commit();

            return response()->json([
                'message' => ' quantity Added Successfully !'
            ], 200);


        } catch (ModelNotFoundException|\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], 404);

        }


    }

    public function sub_qty($request)
    {
        DB::beginTransaction();
        try {

            if (!check_user_has_order($request)) {
                throw new \Exception('Invalid order_id');

            }


            $order_item = OrderItem::findorFail($request->item_id);

            $order_item->order()->total_price -= $order_item->unit_price;
            $order_item->order()->save();

            $order_item->quantity += 1;
            $order_item->total_price -= $order_item->unit_price;
            $order_item->save();
            DB::commit();

            return response()->json([
                'message' => ' quantity decreased Successfully !'
            ], 200);

        } catch (ModelNotFoundException) {
            DB::rollBack();
            return response()->json([
                'message' => 'Something Wrong happened Please try again'
            ], 404);

        }


    }

    public function remove_from_cart($request)
    {
        DB::beginTransaction();
        try {

            $user = Auth::guard('sanctum')->user();

            $order_item = OrderItem::where([
                'id' => $request->item_id,
                'order_id' => $user->cart()->pluck('id'),
            ])->firstorFail();

            $order = $user->cart()->first();
            $order->total_price -= $order_item->unit_price * $order_item->quantity;
            $order->save();

            $order_item->delete();

            DB::commit();
            return response()->json([
                'message' => 'removed from cart successfully'
            ], 200);


        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Item Not Found !'
            ], 403);

        }
    }


    public function cart()
    {
        $user = Auth::guard('sanctum')->user();
        $order = $user->cart()->first();
        $order->load('order_items.product');
        return new OrderResource($order);
    }


    public function getOrdersForSuperAdmin($request)
    {
        if ($request->query('s')) {
            $orders = search_model(Order::with(['user_order', 'order_items.product']), ['status'], $request->query('s'), ['user_order', 'name']);
        } else {
            $orders = Cache::remember(
                'orders_for_Super_Admin',
                now()->addMinute(150),
                function () {
                    return Order::with(['user_order', 'order_items.product'])->get();
                }
            );
        }
        return OrderResource::collection($orders);

    }

    public function getOrdersForUser($request)
    {
        $user = Auth::guard('sanctum')->user();

        if ($request->query('s')) {
            $orders = search_model(Order::where('user', $user->id), ['status'], $request->query('s'), ['user_order', 'name']);
        } else {
            $orders = $user->all_orders()->get();
        }
        $orders->load('order_items.product');
        return OrderResource::collection($orders);

    }

    private function updateOrderAndOrderItem($order, $product, $qty)
    {

        $order->total_price += $qty * $product->price;
        $order->save();

        // check if item exist in user cart and update if not create new one
        $order_item = OrderItem::where(['order_id' => $order->id, 'product_id' => $product->id])->first();

        if ($order_item) {
            $order_item->total_price += $qty * $product->price;
            $order_item->quantity += $qty;
            $order_item->save();
        } else {
            $cart = OrderItem::create([
                'product_id' => $product->id,
                'order_id' => $order->id,
                'quantity' => $qty,
                'unit_price' => $product->price
            ]);
            $cart->total_price = $qty * $product->price;
            $cart->save();
        }


    }

}
