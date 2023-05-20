<?php

namespace App\Http\Controllers;

use App\Models\billingDetails;
use Carbon\Carbon;
use App\Models\CartList;
use App\Models\City;
use App\Models\Country;
use App\Models\CouponStore;
use App\Models\inventory;
use Illuminate\Http\Request;
use App\Models\customerlogin;
use App\Models\Order;
use App\Models\orderProduct;
use App\Models\State;
use App\Models\WishList;
use Illuminate\Support\Facades\Auth;
use PDF;

class customerController extends Controller
{
    function login_page(){
        return view('frontend.register&login.register');
    }

    function cart_page(Request $request){
        $coupon = $request->coupon_code;
        $discount_amount = 0;
        $err_msg = null;
        $discount_method = null;
        $discount_range = null;
        if ($coupon == '') {
            $discount_amount = 0;
            $err_msg = null;
        }
        else {
            if (CouponStore::where('coupon_code', $coupon)->exists()) {
                $all_info = CouponStore::where('coupon_code', $coupon)->get()->first();
                if (Carbon::now()->format('Y-m-d') < $all_info->validity_date) {
                    $discount_amount = $all_info->discount_amount;
                    $discount_method = $all_info->discount_method;
                    $discount_range = $all_info->discount_range;
                }
                else {
                    $discount_amount = 0;
                    $err_msg = 'This coupon has expired';
                }
            }
            else {
                $discount_amount = 0;
                $err_msg = 'This coupon does not exist';
            }
        }

        $customer_id = Auth::guard('customerlogin')->id();
        $cart = CartList::where('customer_id', $customer_id)->get();
        return view('frontend.profile_personal.cart_page', [
            'cart'=>$cart,
            'discount'=>$discount_amount,
            'method'=>$discount_method,
            'drange'=>$discount_range,
            'err_msg'=>$err_msg,
        ]);
    }

    function checkout_page(){
        $carts = CartList::where('customer_id', Auth::guard('customerlogin')->id())->get();
        $countries = Country::all();
        $states = State::all();
        $cities = City::all();
        return view('frontend.profile_personal.checkout', [
            'carts' => $carts,
            'countries' => $countries,
            'states' => $states,
            'cities' => $cities,
        ]);
    }

    function customer_profile_page(){
        return view('frontend.profile_personal.customerProfile');
    }

    function customer_order_page(){
        $orders = Order::where('customer_id', Auth::guard('customerlogin')->id())->where('delivery', null)->get();
        return view('frontend.profile_personal.my_order', [
            'orders' => $orders,
        ]);
    }

    function customer_order_success() {
        return view('frontend.message.success');
    }

    function error_page() {
        return view('frontend.message.404');
    }

    function customer_wish_page() {
        $wishes = WishList::where('customer_id', Auth::guard('customerlogin')->id())->get();
        return view('frontend.profile_personal.wishlist', [
            'wishes' => $wishes,
        ]);
    }

    function pass_reset_page() {
        return view('frontend.register&login.pass_reset');
    }

    function pass_reset_form_page($token) {
        return view('frontend.register&login.pass_reset_form', [
            'token' => $token,
        ]);
    }

    function invoice_download($order_id){
        $order_id = '#'.$order_id;
        $billing_info = billingDetails::where('order_id', $order_id)->get();
        $product_info = orderProduct::where('order_id', $order_id)->get();
        $order_info = Order::where('order_id', $order_id)->get();

        $pdf = PDF::loadView('frontend.profile_personal.invoice_download', [
            'order_id'=>$order_id,
            'billing_info'=>$billing_info,
            'product_info'=>$product_info ,
            'order_info'=>$order_info ,
        ]);
    
        return $pdf->stream('invoice.pdf');

    }

}
