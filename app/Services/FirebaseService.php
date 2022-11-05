<?php

namespace App\Services;

use Illuminate\Support\Str;

class FirebaseService
{
    public static function sendNotification($token, $title) 
    {  
        $data = [
        "registration_ids" => $token,
        "notification" => [
                'title'         => $title,
                "sound"         => "default",
            ],
        ];

        return self::send(json_encode($data));
    }

    private static function send($data)
    {
        $url = "https://fcm.googleapis.com/fcm/send";            
        // $key = "AAAAWAAAAkbbLhVg:APA91bHxDXFxJoSySmpNm_QBQP_ReGNhXdH7u07SS91cmO--0f5G2RKIkXeHCVaX0bwVIv8-Yyebgq0jrKfFy3N2C1GT_AegSNk3X30_WhnaebwA68MK9ocfFG1_MKFi41f_rW1eGOfX";            
        $key = env('FIREBASE_SERVER_KEY');
        $header = [
        'authorization: key=' . $key,
            'content-type: application/json'
        ];    

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        $result = curl_exec($ch);    
        curl_close($ch);

        return $result;
    }
}

