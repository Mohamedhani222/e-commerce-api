<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\interfaces\OrderInterface;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    public $order_interface;

    public function __construct(OrderInterface $order_interface)
    {
        $this->order_interface = $order_interface;
        $this->middleware(['auth:sanctum', 'permission:list order'])->only('index', 'show', 'cart');
        $this->middleware(['auth:sanctum', 'permission:create order|create orderitem'])->only('create', 'store');
        $this->middleware(['auth:sanctum', 'permission:update order'])->only('update', 'edit');
        $this->middleware(['auth:sanctum', 'permission:delete order'])->only('destroy');
        $this->middleware(['auth:sanctum', 'permission:delete orderitem'])->only('remove_from_cart');
    }


    public function index(Request $request)
    {
        return $this->order_interface->index($request);
    }


    public function store(Request $request)
    {
        return $this->order_interface->store($request);
    }


    public function show($id)
    {
        return $this->order_interface->show($id);
    }

    public function cart()
    {
        return $this->order_interface->cart();
    }

    public function remove_from_cart(Request $request)
    {
        return $this->order_interface->remove_from_cart($request);
    }

    public function destroy(Order $order)
    {
        return $this->order_interface->destroy($order);
    }
}
