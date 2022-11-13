<?php

namespace App\Http\Controllers\Api\Shipping;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\Session\Session;
use App\Services\FatooorahServices;
use App\Http\Requests\Shipping\ForeignTransactionRequest;
use App\Models\ForeignTransaction;
use App\Services\CodeService;

class ForeignTransactionController extends Controller
{
    private $fatoorahServices;

    public function __construct(FatooorahServices $fatoorahServices)
    {
        $this->fatoorahServices = $fatoorahServices;
    }

    public function index(){

        $user_id = \Auth::guard('user-api')->id();

        $data = ForeignTransaction::with('user')->where('user_id' , $user_id)->paginate(PAGINATION_COUNT);
        return response()->json([
            'data'   => $data,
        ]);
    }

    public function pay(ForeignTransactionRequest $request)
    {
        $rand               = rand(100000000000, 999999999999);
        $shipping           = \Auth::guard('user-api')->user();

        $companyname        = $shipping->en_name;
        $phonenumber        = $shipping->mobile;
        $email              = $shipping->email;
        $paymentCase        = $request->amount;
        $setSecretKey       = getPaymentInfo()->secretKey;
        $MerchUID           = getPaymentInfo()->MerchUID;
        $SubMerchUID        = !empty($shipping->SubMerchUID) ? $shipping->SubMerchUID : getPaymentInfo()->SubMerchUID;
        $merchantIBanNo     = !empty($shipping->iban) ? $shipping->iban : getPaymentInfo()->iban;
        $accountTitleName   = !empty($shipping->account_name) ? $shipping->account_name : getPaymentInfo()->account_name;
        $swiftCode          = !empty($shipping->swift_code) ? $shipping->swift_code : getPaymentInfo()->swift_code;
        $success_url        = "https://tracking.000itkw.com/users/foreign_transactions/callback_success";
        $error_url          = "https://tracking.000itkw.com/users/foreign_transactions/callback_error";
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
                "Pyr_MPhone"        => $request->mobile,
                "Pyr_Name"          => $request->name,
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

        if ($response['PayUrl'] != null) {
            $MerchantTxnRefNo = "$rand";
            $rand2 = rand(10000000, 99999999);

            $num = "B$rand2";

            $foreignTransaction                    = new ForeignTransaction();
            $foreignTransaction->MerchantTxnRefNo  = $MerchantTxnRefNo;
            $foreignTransaction->user_id           = $shipping->id;
            $foreignTransaction->amount            = $paymentCase;
            $foreignTransaction->name              = $request->name;
            $foreignTransaction->email             = $request->email;
            $foreignTransaction->mobile            = $request->mobile;
            $foreignTransaction->date              = $ldate;

            $issave = $foreignTransaction->save();
            if ($issave) {
                $message = "Payemnt link from ". $shipping->en_name ."/n". $response['PayUrl'];
                CodeService::send($request->mobile , $message);
                return response()->json([
                    'status'   => "success",
                    'message'     => $response['PayUrl'],
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

        ForeignTransaction::where('MerchantTxnRefNo', '=', $MerchantTxnRefNo)->update(['status' => $finalStatus]);
        return Redirect()->to("https://tracking.000itkw.com/shipping/foreign_transaction?MerchantTxnRefNo=".$MerchantTxnRefNo);
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

        return Redirect()->to("https://tracking.000itkw.com/shipping/foreign_transaction?error=".$gatewayMsg);
    }
}
