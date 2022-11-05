<?php

namespace App\Services;

class PaymentService
{
  /**
   * Send POST cURL request to paymob servers.
   *
   * @param  string  $url
   * @param  array  $json
   * @return array
   */
  protected function cURL($url, $json = null)
  {
    // Create curl resource
    $headers = [
      "Content-Type: application/json",
      "Authorization: Bearer sk_test_nU6SipwX4MHC3xeY5LTAPro7",
    ];

    $ch   = curl_init();
    
    // Return the transfer as a string
    curl_setopt($ch, CURLOPT_URL, $url);
    
    if(!empty($json)){
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json));    
    }
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
    // $output contains the output string
    $output = curl_exec($ch);

    $err = curl_error($ch);

    // Close curl resource to free up system resources
    curl_close($ch);
  
    return json_decode($output);
  }

  public static function pay($data){

    $url  = "https://api.tap.company/v2/charges";

    // create object from Class to calling func inside class
    $pay = new PaymentService;  

    // Send curl
    $output = $pay->cURL(
      $url,
      $data
    );
    
    return $output; 
  }

  public static function callback($tap_id){
    $url  = "https://api.tap.company/v2/charges/".$tap_id;

    // create object from Class to calling func inside class
    $pay = new PaymentService;  

    // Send curl
    $output = $pay->cURL($url);
    
    return $output; 
  }
}
