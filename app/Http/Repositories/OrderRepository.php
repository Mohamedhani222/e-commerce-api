<?php

namespace App\Http\Repositories;

use App\Http\interfaces\OrderInterface;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use function App\check_user_has_order_item;
use function App\confirm_price;
use function App\getOrdersForSuperAdmin;
use function App\getOrdersForUser;
use function App\updateOrderAndOrderItem;

class OrderRepository implements OrderInterface
{

    public function index($request): \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $user = Auth::guard('sanctum')->user();

        if ($user->hasRole('SuperAdmin')) {

            return getOrdersForSuperAdmin($request);

        } elseif ($user->hasRole(["user"])) {

            return getOrdersForUser($request);
        }

        return response()->json([
            'message' => 'error'
        ], 500);

    }

    // add_to_cart
    public function store($request): \Illuminate\Http\JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = Auth::guard('sanctum')->user();
            $product = Product::findorFail($request->product_id);
            $qty = $request->input('qty');

            $order = $user->orders()->Status('IN_CART')->firstOrCreate(['user' => $user->id]);
            updateOrderAndOrderItem($order, $product, $qty);

            DB::commit();
            return response()->json([
                'message' => 'Added To Cart Successfully !'
            ]);

        } catch (\Throwable|ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], 403);

        }
    }


    public function show($id): \Illuminate\Http\JsonResponse|OrderResource
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if ($user->hasRole('user')) {
                if (!$user->orders()->pluck('id')->contains($id)) {
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

    public function destroy($order): \Illuminate\Http\JsonResponse
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

    public function add_qty($request): \Illuminate\Http\JsonResponse
    {
        DB::beginTransaction();
        try {

            $user = Auth::guard('sanctum')->user();


            if (!check_user_has_order_item($request)) {
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

    public function sub_qty($request): \Illuminate\Http\JsonResponse
    {
        DB::beginTransaction();
        try {

            if (!check_user_has_order_item($request)) {
                throw new \Exception('Invalid order_id');

            }

            $order_item = OrderItem::findorFail($request->item_id);

            $order_item->order()->total_price -= $order_item->unit_price;
            $order_item->order()->save();

            $order_item->quantity -= 1;
            $order_item->total_price -= $order_item->unit_price;
            $order_item->save();
            DB::commit();

            return response()->json([
                'message' => 'quantity decreased Successfully !'
            ], 200);

        } catch (ModelNotFoundException) {
            DB::rollBack();
            return response()->json([
                'message' => 'Something Wrong happened Please try again'
            ], 404);

        }


    }

    public function remove_from_cart($request): \Illuminate\Http\JsonResponse
    {
        DB::beginTransaction();
        try {

            $user = Auth::guard('sanctum')->user();

            $order_item = OrderItem::where([
                'id' => $request->item_id,
                'order_id' => $user->orders()->Status('IN_CART')->pluck('id'),
            ])->firstorFail();

            $order = $user->orders()->Status('IN_CART')->first();
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


    public function cart(): \Illuminate\Http\JsonResponse|OrderResource
    {
        try {
            $user = Auth::guard('sanctum')->user();
            $order = $user->orders()->Status('IN_CART')->first();
            $order->load('order_items.product');
            return new OrderResource($order);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 403);

        }
    }


    public function confirm_order($id): \Illuminate\Http\JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        $order = $user->orders()->Status('IN_CART')->find($id);

        if (!$order) {
            return response()->json([
                'message' => 'Order Not Found !'
            ], 403);

        }
        confirm_price($id);
        $order->status = 'PENDING';
        $order->save();
        return response()->json([
            'message' => 'Order confirmed successfully '
        ], 200);

    }

//    public function order_coupon()
//    {
//
//    }
}




