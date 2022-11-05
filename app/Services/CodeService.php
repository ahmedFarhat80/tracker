<?php

namespace App\Services;

use App\Models\UserApiCode;
use Illuminate\Support\Facades\Session;
use MessageBird\Client;
use MessageBird\Common\HttpClient;
use MessageBird\Objects\Message;

class CodeService
{
  protected function cURL($url , $data)
  {
    $header = array(
      "POST /SmsWebService.asmx/send HTTP/1.1",
      "Host: server.smson.com",
      "Content-Type: application/x-www-form-urlencoded",
      "Content-Length: ".strlen($data),
    );
    
    $soap_do = curl_init();
    curl_setopt($soap_do, CURLOPT_URL, $url );
    curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($soap_do, CURLOPT_TIMEOUT,        10);
    curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
    curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($soap_do, CURLOPT_POST,           true );
    curl_setopt($soap_do, CURLOPT_POSTFIELDS,     $data);
    curl_setopt($soap_do, CURLOPT_HTTPHEADER,     $header);
  
    $result = curl_exec ($soap_do);

    // Close curl resource to free up system resources
    curl_close($soap_do);

    return $result;
  }

  public static function send($mobile, $message , $api = false)
  {

    if (!$mobile) {
      return false;
    }
    $dst            = $mobile; // to 55372333
    $url            = "server.smson.com/SmsWebService.asmx/send";
    // $code           = rand(100000,999999);
    $code           = '123456';
    // $message        = "verification code are ". $code;
    $soap_request   = "username=Easy Media&password=jKKk8fY9&token=m4Bz3CzjqA1a16N3pJphABvK&sender=albarqah&message=".$message."&dst=".$dst."&type=text&coding=default&datetime=now";
    
    // create object from Class to calling func inside class
    $CodeService = new CodeService;  

    // Send curl
    $output = $CodeService->cURL(
        $url,
        $soap_request
    );

    return true;
  }


  public static function sendMessageBird($user){
    try{
      
      if (!$user) {
        return false;
      }

      $dst                  = $user->mobile; // to 55372333
    //   $code                 = rand(100000,999999);
          $code           = '123456';

      $message              = "verification code are ". $code;

      $MessageBird          = new \MessageBird\Client('Rdr79nMgH7pKF6cBusAlgmVnN');
      $Message              = new \MessageBird\Objects\Message();
      $Message->originator  = 'Verify';
      // $Message->recipients  = array(+970592036504);
      $Message->recipients  = array($dst);
      $Message->body        = $message;

      $MessageBird->messages->create($Message);  
      return $code;

    }catch (\MessageBirdException $ex) {
      return errorMessage($ex->getMessage(), 500);
    }

  }
}
