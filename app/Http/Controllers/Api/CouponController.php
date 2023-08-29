<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CopunRequest;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'role:SuperAdmin|admin']);
    }

    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function store(CopunRequest $request)
    {
        $coupon = Coupon::create(
            $request->validated()
        );
        return response()->json([
            'message' => "created successfully you can share it",
            'coupon' => $coupon
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Coupon $coupon)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Coupon $coupon)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CopunRequest $request, Coupon $coupon)
    {
        $coupon->update(
            $request->validated()
        );
        return response()->json([
            'message' => " Coupon updated successfully",
            'coupon' => $coupon
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return response()->json([
            'message' => " Coupon deleted successfully",
        ]);

    }
}
