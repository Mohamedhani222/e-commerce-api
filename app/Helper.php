<?php

namespace App;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use function Symfony\Component\String\u;


function search_model($model, $attrs, $query, $relation = null)
{

    $queryBuilder = $model;

    if ((!is_null($attrs))) {
        $queryBuilder->where($attrs[0], 'LIKE', '%' . $query . '%');
        for ($i = 1; $i < count($attrs); $i++) {
            $queryBuilder->orWhere($attrs[$i], 'LIKE', '%' . $query . '%');
        }

    }

    if (!is_null($relation)) {
        $queryBuilder->orwhereHas($relation[0], function ($q) use ($query, $relation) {
            $q->where($relation[1], 'LIKE', '%' . $query . '%');
        });
    }

    return $queryBuilder->get();

}


function check_user_has_order_item($request)
{
    $user = Auth::guard('sanctum')->user();

    $orderItemIds = $user->orders()->Status('IN_CART')
        ->with('order_items')
        ->get()
        ->pluck('order_items')
        ->flatten()
        ->pluck('id')
        ->toArray();

    return (in_array($request->item_id, $orderItemIds));


}

function getOrdersForSuperAdmin($request)
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

function getOrdersForUser($request)
{
    $user = Auth::guard('sanctum')->user();
    if ($request->query('s')) {
        $orders = search_model(Order::where('user' , $user->id)->where('status' , "!=" , 'IN_CART'), ['status'], $request->query('s'), ['user_order', 'name']);
    } else {
        $orders = $user->orders()->where('status' , "!=" , 'IN_CART')->get();
    }
    $orders->load('order_items.product');
    return OrderResource::collection($orders);

}

function updateOrderAndOrderItem($order, $product, $qty)
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


function confirm_price($orderId){

        $order =Order::findOrFail($orderId);
    if (!$order->has('coupon')){
        $items =array_sum($order->order_items()->pluck('total_price')->toArray());
        if ($order->total_price != $items){
            $order->total_price =$items;
            $order->save();
        }
    }
}

