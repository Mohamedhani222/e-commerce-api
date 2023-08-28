<?php

namespace App\Http\interfaces;

interface OrderInterface
{
    public function index($request);

    public function store($request);

    public function show($id);

    public function destroy($order);

    public function cart();

    public function remove_from_cart($request);


}
