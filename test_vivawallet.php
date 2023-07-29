<?php

$vivawallet_merchant_id="332tqe4m8sdgxp4vrs5mo8tvabk09f3wnoykeasm6hjx0.apps.vivapayments.com";
$vivawallet_api_key="kP5zLjSa4b02808Rzx0P5i704g918G";

include "viva_wallet.php";
// $vals=vivawallet_get_access_token(array("grant_type"=>"client_credentials"));
// print_r($vals);
// echo "<br /><br />";
// $accessToken=$vals["access_token"];
// echo "Access token: ".$accessToken."<br /><br />";
// $vals=vivawallet_create_payment_order(array("amount"=>100),$accessToken);
// print_r($vals);
// echo "<br /><br />";
// $orderCode=$vals["orderCode"];

// $orderCode="3746497704692491"; //this is the order code it will cancel
// echo "Order code: ".$orderCode."<br /><br />";
// $vivawallet_merchant_id="d4d523f5-b991-ed11-aad1-000d3adea3b4";
// $vivawallet_api_key="bCKznvvULf7Y76em5ANeK9xxPAor2V";
// $vals=vivawallet_cancel_payment_order(array("order_code"=>$orderCode),"");
// print_r($vals);
// echo "<br /><br />";

$transactionId="f89683ff-ed3e-4ac3-9442-37512231c921"; //this is the transaction id it will cancel
$orderTotal="3";
$amount=floatval($orderTotal)*100;
echo "Transaction Id: ".$transactionId."<br /><br />";
$vivawallet_merchant_id="d4d523f5-b991-ed11-aad1-000d3adea3b4";
$vivawallet_api_key="bCKznvvULf7Y76em5ANeK9xxPAor2V";
$vals=vivawallet_cancel_transaction(array("transaction_id"=>$transactionId,"amount"=>$amount),"");
print_r($vals);
echo "<br /><br />";

// $vals=vivawallet_get_webhook_token();
// print_r($vals);
// echo "<br /><br />";
?>