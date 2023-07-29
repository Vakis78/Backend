<?php
header("Access-Control-Allow-Origin: *");
include "dbconfig.php";
openDbConnection();
if ($_SERVER["REQUEST_METHOD"]=="POST"){
    try{
        $json = file_get_contents('php://input');
        $data = json_decode($json,TRUE,512,JSON_BIGINT_AS_STRING);
        $event_type_id=$data["EventTypeId"];
        $order_number=$data["EventData"]["OrderCode"];
        $transaction_id=$data["EventData"]["TransactionId"];
        if ($event_type_id=="1796"){
            //success
            R::exec("UPDATE clientorder SET vivawallet_payment_status='success',vivawallet_transaction_id=? WHERE vivawallet_order_number=?",array($transaction_id,$order_number));
        }else if ($event_type_id=="1798"){
            //failed
            R::exec("UPDATE clientorder SET vivawallet_payment_status='failed',vivawallet_transaction_id=? WHERE vivawallet_order_number=?",array($transaction_id,$order_number));
        }
        $result=array("status"=>200,"event_type_id"=>$event_type_id,"order_number"=>$order_number,"transaction_id"=>$transaction_id);
    }catch(Exception $ex){
        $result=array("status"=>500,"msg"=>$ex->getMessage());
        http_response_code(500);
        goto output;
    }
}else if ($_SERVER["REQUEST_METHOD"]=="GET"){
    try{
        $vivawallet_merchant_id="d4d523f5-b991-ed11-aad1-000d3adea3b4";
        $vivawallet_api_key="bCKznvvULf7Y76em5ANeK9xxPAor2V";
        include "viva_wallet.php";
        $vals=vivawallet_get_webhook_token();
        $result=$vals;
    }catch(Exception $ex){
        $result=array("status"=>500,"msg"=>$ex->getMessage());
        http_response_code(500);
        goto output;
    }
}
output:
closeDbConnection();
echo json_encode($result);
exit();

?>