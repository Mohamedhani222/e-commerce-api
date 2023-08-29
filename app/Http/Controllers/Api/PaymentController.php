<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
//    public function pay($orderId)
//    {
//            $user = User::find(2);
////            if (!$user->orders()->find($orderId)) {
////                return response()->json([
////                    'message' => 'error'
////                ], 403);
////            }
//            $order = Order::find($orderId);
//
//            $pay_link = $user->charge($order->total_price,'charge user');
//
//            return view('pay.pay' ) ;
//
//
//
//    }

}
