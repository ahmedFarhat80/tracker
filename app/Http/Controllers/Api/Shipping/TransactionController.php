<?php

namespace App\Http\Controllers\Api\Shipping;

use App\Services\FatooorahServices;
use App\Http\Controllers\Controller;
use App\Http\Resources\Shipping\TransactionResource;
use App\Http\Requests\Shipping\TransactionRequest;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Quote;

class TransactionController extends Controller
{
    private $fatoorahServices;

    public function __construct(FatooorahServices $fatoorahServices)
    {
        $this->fatoorahServices = $fatoorahServices;
    }

    public function pay(TransactionRequest $request)
    {
        $rand               = rand(100000000000, 999999999999);
        $shipping           = \Auth::guard('user-api')->user();
        $quote              = Quote::findOrFail($request->quote_id);
        $companyname        = $shipping->en_name;
        $phonenumber        = $shipping->mobile;
        $email              = $shipping->email;
        $money              = !empty($quote->cost) ? $quote->cost : 1;
        $paymentCase        = $money;
        $setSecretKey       = getPaymentInfo()->secretKey;
        $MerchUID           = getPaymentInfo()->MerchUID;
        $SubMerchUID        = getPaymentInfo()->SubMerchUID;
        $accountTitleName   = getPaymentInfo()->account_name;
        $swiftCode          = getPaymentInfo()->swift_code;
        $merchantIBanNo     = getPaymentInfo()->iban;
        $success_url        = "https://tracking.000itkw.com/users/transactions/pay/callback_success";
        $error_url          = "https://tracking.000itkw.com/users/transactions/pay/callback_error";

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

        if ($response['PayUrl'] != null) {
            $MerchantTxnRefNo = "$rand";
            $rand2 = rand(10000000, 99999999);

            $num = "B$rand2";

            $Transaction                    = new Transaction();
            $Transaction->MerchantTxnRefNo  = $MerchantTxnRefNo;
            $Transaction->user_id           = $shipping->id;
            $Transaction->quote_id          = $quote->id;
            $Transaction->paymentCase       = $paymentCase;
            $Transaction->num               = $num;
            $Transaction->date              = $ldate;

            $issave = $Transaction->save();
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
        $nump = $request->merchantTxnId;
        $value = $request->session()->get('MIR');

        $dataStats = [
            "Mid" => "$value",
            "MerchantTxnRefNo" => [
                "$nump"
            ],
            "HashMac" => "fa1ffd7655312b7d54bb284d4515e4f02aeec4617ba06fa2eff793d1d2de01f3df703ace1e3d8b0b9ae5a2273e79351543152ce2f69f8470300784fb4a8022b9"
        ];

        $response = $this->fatoorahServices->getPayneltstatus($dataStats);
        $MerchantTxnRefNo = $response['PaymentStatus']['0']['MerchantTxnRefNo'];
        $finalStatus = $response['PaymentStatus']['0']['finalStatus'];
        Transaction::where('MerchantTxnRefNo', '=', $MerchantTxnRefNo)->update(['finalStatus' => $finalStatus]);

        return Redirect()->to("https://tracking.000itkw.com/shipping/quotes?MerchantTxnRefNo=".$MerchantTxnRefNo);
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

    public function show($MerchantTxnRefNo){
        try{
            $data = Transaction::where('user_id', \Auth::guard('user-api')->id())
                                ->where('MerchantTxnRefNo', $MerchantTxnRefNo)
                                ->first();
            return new TransactionResource($data , 200);
        }catch(\Exception $ex){
            return errorMessage($ex->getMessage(), 500);
        }
    }
}
