<?php
  
return [
    'easypayug_endpoint' => env('EASYPAYUG_ENDPOINT', "https://www.easypay.co.ug/api/"),
    'easypayug_username' => env('EASYPAYUG_USERNAME', ""),
    'easypayug_password' => env('EASYPAYUG_PASSWORD', ""),
    'easypayug_currency' => env('EASYPAYUG_CURRENCY', "UGX"),

    'sms_url_socnet' => env('SMS_URL_SOCNET', "http://138.197.230.10/socnet/web/projects/bulk/amfphp/services/blast.php"),
    'sms_username_socnet' => env('SMS_USERNAME_SOCNET', "zhuris"),
    'sms_password_socnet' => env('SMS_PASSWORD_SOCNET', ""),
    'sms_sender_socnet' => env('SMS_SENDER_SOCNET', "MGarden"),

    'base_url' => env('APP_BASE_URL', "https://zwlt.zhuris.com/2/public"),
    'access_code_zhuris' => env('ACCESS_CODE_ZHURIS', "ZWLT12312001"),

    'flutterwave_mm_endpoint' => env('FLUTTERWAVEUG_MM_ENDPOINT', "https://api.flutterwave.com/v3/charges?type=mobile_money_uganda"),
    'flutterwave_mm_tocken' => env('FLUTTERWAVEUG_MM_TOCKEN', "FLWSECK_TEST-eeea6eb23f646a9397a3dc2ac7341e20-X"),

    'relworks_mm_endpoint' => env('RELWORKS_MM_ENDPOINT', "https://payments.relworx.com/api/mobile-money"),
    'relworks_mm_tocken' => env('RELWORKS_MM_TOCKEN', "7bc3a04bbde109.XgKv-njELrpLbWoPddEsjA"),
    'relworks_mm_acc_no' => env('RELWORKS_MM_ACC_NO', "REL5A0F731C3A"),

    'email_admin' => env('EMAIL_ADMIN', "consult.zhuris@gmail.com"),
    'telephone_admin' => env('TELEPHONE_ADMIN', "+256701620575"),
    
    
    'sms_url_uganda' => env('SMS_URL_UGANDA', "https://sendsms.zhuris.com/api/index.php"),
    'sms_username_uganda' => env('SMS_USERNAME_UGANDA', "zmt"),
    'sms_key_uganda' => env('SMS_KEY_UGANDA', "saving"),
    'sms_sender_uganda' => env('SMS_SENDER_UGANDA', "ZHURIS"),

];

?>