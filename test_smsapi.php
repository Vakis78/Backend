<?php
include "smsapi.php";
$body="This is a test sms from API";
$vals=smsapi_send_sms(array("from"=>"ANALYZEFIT","to"=>"+35795122900","message"=>$body,"format"=>"json"));
print_r($vals);
?>