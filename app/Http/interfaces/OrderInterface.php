<?php

namespace App\Http\interfaces;

use App\Models\Order;

interface OrderInterface
{
    public function index($request);

    public function store($request);

    public function show($id);

    public function destroy($order);

    public function cart();

    public function remove_from_cart($request);

    public function add_qty($request);

    public function sub_qty($request);

    public function confirm_order($id);
}
