<?php
//define('VIVAWALLET_BASE_URL',"https://demo-api.vivapayments.com/");
define('VIVAWALLET_BASE_URL',"https://api.vivapayments.com/");
//define('VIVAWALLET_ACCOUNT_URL',"https://demo-accounts.vivapayments.com/");
define('VIVAWALLET_ACCOUNT_URL',"https://accounts.vivapayments.com/");

function encodeURI($val){
    return urlencode($val);
}

function get_array_value($arr,$key,$default=""){
    return isset($arr[$key])?$arr[$key]:$default;
}

function vivawallet_create_payment_order($custData,$access_token){
    $bodyString=json_encode($custData);
    return vivawallet_make_request(VIVAWALLET_BASE_URL."checkout/v2/orders","POST",$bodyString,$access_token);
}

function vivawallet_cancel_payment_order($custData,$access_token){
    return vivawallet_make_request("https://www.vivapayments.com/api/orders/".$custData["order_code"]."?orderCode=null","DELETE",FALSE,$access_token);
}

function vivawallet_cancel_transaction($custData,$access_token){
    return vivawallet_make_request("https://www.vivapayments.com/api/transactions/".$custData["transaction_id"]."?amount=".$custData["amount"],"DELETE",FALSE,$access_token);
}

function vivawallet_get_access_token($custData){
    $bodyString=http_build_query($custData);
    return vivawallet_make_request(VIVAWALLET_ACCOUNT_URL."connect/token","POST",$bodyString,"","application/x-www-form-urlencoded");
}

function vivawallet_get_webhook_token(){
    return vivawallet_make_request("https://www.vivapayments.com/api/messages/config/token");
}

function vivawallet_get_auth_header(){
    global $vivawallet_merchant_id,$vivawallet_api_key;
    $h=base64_encode($vivawallet_merchant_id.":".$vivawallet_api_key);
    return "Authorization: Basic ".$h;
}

function vivawallet_throw_or_return($curl_info,$response){
    $status_code=$curl_info["http_code"];
    //echo "status_code: ".$status_code."<br />";
    $vals=json_decode($response,TRUE,512,JSON_BIGINT_AS_STRING);
    if ($vals==null){
        throw new Exception("Unknown error in VivaWallet API");
    }
    if ($status_code>=200 && $status_code<=299)
        return $vals;
    if (isset($vals["error"]))
        throw new Exception($vals["error"]);
    else if (isset($vals["Message"]))
        throw new Exception($vals["Message"]);
    else
        throw new Exception("Unknown error in VivaWallet API");
}

function vivawallet_make_request($url,$method="GET",$body_val=FALSE,$access_token="",$content_type="application/json"){
    //echo $url."<br />";
    //echo $body_val."<br />";
    $headers = [];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    if ($access_token==""){
        $headers[]=vivawallet_get_auth_header();
    }else{
        $headers[]="Authorization: Bearer ".$access_token;
    }
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
    // print_r($curl_info);
    // echo "<br /><Br />";
    // echo $server_output."<br /><br />";
    return vivawallet_throw_or_return($curl_info,$server_output);
}

?>