<?php

namespace App\Http\Controllers\Api\Restaurant;

use Illuminate\Contracts\Session\Session;
use App\Http\Controllers\Controller;
use App\Services\FatooorahServices;
use App\Http\Requests\Shipping\WalletRequest;
use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\Wallet;

class WalletController extends Controller
{
    private $fatoorahServices;
    
    public function __construct(FatooorahServices $fatoorahServices)
    {
        $this->fatoorahServices = $fatoorahServices;
    }

    public function pay(WalletRequest $request)
    {
        $rand               = rand(100000000000, 999999999999);
        $restaurant         = \Auth::guard('restaurant-api')->user();

        $companyname        = $restaurant->user->en_name ?? "Triple zero" ;
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
        $success_url        = "https://tracking2.000itkw.com/wallet/callback";
        $error_url          = "https://tracking2.000itkw.com/wallet/callback_error";
        $data = [
            "DBRqst" => "PY_ECom",
            "Do_Appinfo" => [
                "APIVer" => "1.6",
                "APPTyp" => "WEB",
                "AppVer" => "1"
            ],
            "Do_MerchDtl" => [
                "BKY_PRDENUM"       => "ECom",
                "FURL"              => $error_url,//env('error_url'),
                "MerchUID"          => "$MerchUID",
                "SURL"              => $success_url,//env('success_url'),
                'setSecretKey'      => "$setSecretKey",
            ],
            "Do_PyrDtl" => [
                "Pyr_MPhone"        => "98037942",
                "Pyr_Name"          => "Ahmed",
                "ISDNCD"            => "965"
            ],
            "Do_TxnDtl" => [
                [
                    "SubMerchUID"   => "$SubMerchUID",
                    "Txn_AMT"       => "$paymentCase"
                ]
            ],
            "Do_TxnHdr" => [
                'Merch_Txn_UID'     => "$rand",
                "PayFor"            => "ECom",
                "PayMethod"         => "knet",
                "Txn_HDR"           => "2987228884280325",
                "hashMac" => "8B95BEED1BDAAA0B0672D28BFA7F0C08408EFD7AAACA6C78582242A1348ABAB0542C0CD43BC9AD9DD906B001C1B220557011D8E0770DDFB45CE70C8D7D069C7F",
                "emailAddress"      => "$email",
                "phoneAddress"      => "+965" . "$phonenumber",
                "address"           => "Kwite",
                "ISDNCode"          => "123",
                "merchantIBanNo"    => $merchantIBanNo,
                "accountTitleName"  => $accountTitleName,
                "swiftCode"         => $swiftCode,
                "merchantName"      => "$companyname",
            ]
        ];

        session()->regenerate();
        session(['MIR' => "$MerchUID"]);

        $ldate = date('m-d-Y');
        $response = $this->fatoorahServices->sendPayment($data);

        return $response;
        if ($response['PayUrl'] != null) {
            $MerchantTxnRefNo = "$rand";
            $rand2 = rand(10000000, 99999999);

            $num = "B$rand2";

            $wallet                    = new Wallet();
            $wallet->MerchantTxnRefNo  = $MerchantTxnRefNo;
            $wallet->user_id           = $restaurant->user_id;
            $wallet->restaurant_id     = $restaurant->id;
            $wallet->budget            = $paymentCase;
            $wallet->date              = $ldate;

            $issave = $wallet->save();
            if ($issave) {
                // return Redirect()->to($response['PayUrl']);
                return response()->json([
                    'message'   => "Redirect to",
                    'PayUrl'     => $response['PayUrl'],
                ]);
            } else {
                return response()->json([
                    'status'   => "error",
                    'message'     => $response['ErrorMessage'],
                ]);
            }
        } else {
            return response()->json([
                'status'   => "error",
                'message'     => $response['ErrorMessage'],
            ]);
        }
    }

    public function callback(Request $request)
    {
        $nump   = $request->merchantTxnId;
        $value  = $request->session()->get('MIR');

        $dataStats = [
            "Mid" => "$value",
            "MerchantTxnRefNo" => [
                "$nump"
            ],
            "HashMac" => "fa1ffd7655312b7d54bb284d4515e4f02aeec4617ba06fa2eff793d1d2de01f3df703ace1e3d8b0b9ae5a2273e79351543152ce2f69f8470300784fb4a8022b9"
        ];

        $response           = $this->fatoorahServices->getPayneltstatus($dataStats);
        $MerchantTxnRefNo   = $response['PaymentStatus']['0']['MerchantTxnRefNo'];
        $finalStatus        = $response['PaymentStatus']['0']['finalStatus'];
        $gatewayMsg         = $response['PaymentStatus']['0']['StatusDescription'];
        $wallet             = Wallet::where('MerchantTxnRefNo', '=', $MerchantTxnRefNo)->first();
        $wallet->status     = $finalStatus;
        $wallet->save();
        
        // update wallet for restaurant
        if($finalStatus == 'success'){
            $restaurant         = Restaurant::where('id' , $wallet->restaurant_id)->first();
            $restaurant->wallet += $wallet->budget;
            $restaurant->save();
        }
        
        return Redirect()->to("https://tracking.000itkw.com/wallet?MerchantTxnRefNo=".$MerchantTxnRefNo."&success=".$gatewayMsg);
    }

    public function callbackError(Request $request)
    {
        $nump   = $request->merchantTxnId;
        $value  = $request->session()->get('MIR');

        $dataStats = [
            "Mid" => "$value",
            "MerchantTxnRefNo" => [
                "$nump"
            ],
            "HashMac" => "fa1ffd7655312b7d54bb284d4515e4f02aeec4617ba06fa2eff793d1d2de01f3df703ace1e3d8b0b9ae5a2273e79351543152ce2f69f8470300784fb4a8022b9"
        ];

        $response           = $this->fatoorahServices->getPayneltstatus($dataStats);
        $gatewayMsg         = $response['PaymentStatus']['0']['StatusDescription'];

        return Redirect()->to("https://tracking.000itkw.com/wallet?error=".$gatewayMsg);
    }
}
