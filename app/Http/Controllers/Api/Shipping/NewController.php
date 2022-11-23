<?php

namespace App\Http\Controllers\Api\Shipping;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\Setting;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class NewController extends Controller
{
    //
    public function get_Resturant_data($id)
    {
        # code...

        $data         = Restaurant::where('id', '=', $id)->with('orders')->first();
        $count_orders = $data->orders->count();
        $Wallet       = Wallet::where('restaurant_id', '=', $id)->get();
        $Balance      = Wallet::where('restaurant_id', '=', $id)->count();

        return response()->json([
            'data'          => $data,
            'Wallet'        => $Wallet,
            "Balance"       => $Balance,
            "count_orders"  =>   $count_orders
        ]);
    }


    public function get_driver_data($id)
    {
        $data  = Driver::where('id', '=', $id)->first();
        // $restaurant      =

        $Order_sum_price = Order::where('driver_id', '=', $id)->whereDate('created_at', Carbon::today())->where('paid_status', '=', "cash")->sum('price');
        $distance        = Order::where('driver_id', '=', $id)->whereDate('created_at', Carbon::today())->sum('distance');

        $Orders = Order::where('driver_id', '=', $id)->with('restaurant')->get();


        return response()->json([
            'data'  => $data,
            'Order_sum_price'  => $Order_sum_price,
            "distance" => $distance,
            "Orders" => $Orders
        ]);
    }

    public function new_get_Delivery_price()
    {
        $id = Auth::id();
        $data = Setting::where('user_id', '=', $id)->get();

        return response()->json([
            'data'  => $data,
        ]);
    }


    public function add_walit(Request $request, $id)
    {
        $validator = Validator($request->all(), [
            'budget'        => 'required|numeric',
        ]);


        if (!$validator->fails()) {
            $rand               = rand(100000000000, 999999999999);

            $restaurant         = Restaurant::where('id', '=', $id)->with('user')->first();

            $companyname        = $restaurant->user->en_name ?? "Triple zero";
            $phonenumber        = $restaurant->user->mobile ?? "553 72333";
            $email              = $restaurant->user->email ?? "info@me.com";
            $money              = $request->budget;
            $paymentCase        = $money;
            $setSecretKey       = getPaymentInfo()->secretKey;
            $MerchUID           = getPaymentInfo()->MerchUID;
            $SubMerchUID        = !empty($restaurant->user->SubMerchUID) ? $restaurant->user->SubMerchUID : getPaymentInfo()->SubMerchUID;
            $merchantIBanNo     = !empty($restaurant->user->iban) ? $restaurant->user->iban : getPaymentInfo()->iban;
            $accountTitleName   = !empty($restaurant->user->account_name) ? $restaurant->user->account_name : getPaymentInfo()->account_name;
            $swiftCode          = !empty($restaurant->user->swift_code) ? $restaurant->user->swift_code : getPaymentInfo()->swift_code;

            $MerchantTxnRefNo = "$rand";
            $ldate = date('m-d-Y');

            $wallet                    = new Wallet();
            $wallet->MerchantTxnRefNo  = $MerchantTxnRefNo;
            $wallet->user_id           = $restaurant->user_id;
            $wallet->restaurant_id     = $restaurant->id;
            $wallet->budget            = $paymentCase;
            $wallet->date              = $ldate;
            $wallet->status            = "Shipping Company";
            $wallet->save();

            $restaurant->wallet = $restaurant->wallet + $paymentCase;
            $restaurant->save();

            return response()->json(['message' => "successfully", 'Request' => Response::HTTP_OK]);
        } else {
            return response()->json(['message' => $validator->getMessageBag()->first(), 'Request' => Response::HTTP_BAD_REQUEST]);
        }
    }


}
