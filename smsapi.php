<?php
define('SMS_BASE_URL',"https://api.smsapi.com/");
define('SMS_API_TOKEN',"jw555Z05iljUbnJ9BwDdRurul4eVcnkqkBemDZ98");

function smsapi_send_sms($custData){
    $bodyString=http_build_query($custData);
    return smsapi_make_request(SMS_BASE_URL."sms.do","POST",$bodyString,"application/x-www-form-urlencoded");
}

function smsapi_get_auth_header(){
    $h=SMS_API_TOKEN;
    return "Authorization: Bearer ".$h;
}

function smsapi_throw_or_return($curl_info,$response){
    $status_code=$curl_info["http_code"];
    $vals=json_decode($response,TRUE,512,JSON_BIGINT_AS_STRING);
    if ($vals==null){
        throw new Exception("Unknown error in SmsApi API");
    }
    if ($status_code>=200 && $status_code<=299)
        return $vals;
    if (isset($vals["error"]))
        throw new Exception($vals["message"]);
}

function smsapi_make_request($url,$method="GET",$body_val=FALSE,$content_type="application/json"){
    //echo $url."<br />";
    //echo $body_val."<br />";
    $headers = [];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    $headers[]=smsapi_get_auth_header();
    //print_r($headers);
    //echo "<br /><br />";
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST,strtoupper($method));
    if ($body_val!==FALSE){
        $headers[]='Content-Type: '.$content_type;
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body_val);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $server_output=curl_exec($ch);
    $curl_info=curl_getinfo($ch);
    curl_close($ch);
    //print_r($curl_info);
    //echo "<br /><Br />";
    //echo $server_output."<br /><br />";
    return smsapi_throw_or_return($curl_info,$server_output);
}

?>