<?php
header('Access-Control-Allow-Origin: *');
require_once "dbconfig.php";
$result=array("status"=>200);
try{
    openDbConnection();
    if ($_SERVER["REQUEST_METHOD"]=="POST"){
        $user_id=$_POST["user_id"];
        $products=$_POST["products"];
        $address=$_POST["address"];
        $total=$_POST["total"];
        $cart_total=isset($_POST["cart_total"])?$_POST["cart_total"]:"0";
        $tax_percentage=isset($_POST["tax_percentage"])?$_POST["tax_percentage"]:"0";
        $tax=isset($_POST["tax"])?$_POST["tax"]:"0";
        $discount_percentage=isset($_POST["discount_percentage"])?$_POST["discount_percentage"]:"0";
        $discount=isset($_POST["discount"])?$_POST["discount"]:"0";
        $branch_id=$_POST["branch_id"];
        $payment_mode=$_POST["payment_mode"];
        $order_type=$_POST["order_type"];
        $card_id=$_POST["card_id"];
        $pickup_dt=$_POST["pickup_dt"];

        $br=R::getRow("SELECT * FROM branches WHERE branch_id=?",array($branch_id));
        if ($br==FALSE){
            $result=array("status"=>404,"msg"=>"Invalid branch");
            goto output;
        }
        $usr=R::getRow("SELECT * FROM `user` WHERE user_id=?",array($br["company_id"]));
        if ($usr==FALSE){
            $result=array("status"=>404,"msg"=>"Invalid company");
            goto output;
        }
        $vivawallet_merchant_id=$usr["vivawallet_merchant_id"];
        $vivawallet_api_key=$usr["vivawallet_api_key"];
    
        $sp=R::dispense('clientorder');
        $sp->client_id=$user_id;
        $script_tz=date_default_timezone_get();
        date_default_timezone_set('Europe/Athens');
        $sp->order_dt=date('Y-m-d H:i:s');
        date_default_timezone_set($script_tz);
        $sp->status='active';
        //$sp->status='payment_pending';
        $sp->order_number=time();
        $sp->destination=$address;
        $sp->order_total=$total;
        $sp->cart_total=$cart_total;
        $sp->tax_percentage=$tax_percentage;
        $sp->tax=$tax;
        $sp->discount_percentage=$discount_percentage;
        $sp->discount=$discount;
        $sp->branch_id=$branch_id;
        $sp->payment_mode=$payment_mode;
        $sp->order_type=$order_type;
        $sp->card_id=$card_id;
        $sp->pickup_dt=$pickup_dt;
        if ($payment_mode=="cash"){
            $sp->vivawallet_payment_status="";
        }
        $order_id=R::store($sp);
        $products=json_decode($products,TRUE);
        foreach ($products as $p){
            $op=R::dispense('orderproduct');
            $op->order_id=$order_id;
            $op->product_id=$p["product_id"];
            $op->quantity=$p["quantity"];
            $op->price=$p["price"];
            $op->total=$p["total"];
            if (isset($p["variants"])){
                $op->variants=json_encode($p["variants"]);
            }else{
                $op->variants="";
            }
            R::store($op);
        }
        $orderCode="";
        if ($payment_mode=="card"){
            //here call vivawallet and update with ordernumber
            include "viva_wallet.php";
            $vals=vivawallet_get_access_token(array("grant_type"=>"client_credentials"));
            $accessToken=$vals["access_token"];
            $vals=vivawallet_create_payment_order(array("amount"=>(floatval($total)*100)),$accessToken);
            $orderCode=$vals["orderCode"];
            $sp->vivawallet_order_number=$orderCode;
            R::store($sp);
        }
        try{
            include "smsapi.php";
            $body="New order is placed";
            $to="+35795122900";
            $vals=smsapi_send_sms(array("from"=>"ANALYZEFIT","to"=>$to,"message"=>$body,"format"=>"json"));
        }catch(Exception $ex){

        }
        $result=array("status"=>200,"order_code"=>$orderCode);
        goto output;
    }else{
        $result=array("status"=>500,"msg"=>"Method not supported");
        goto output;
    }
}catch(Exception $ex){
    $result=array("status"=>500,"msg"=>$ex->getMessage());
    goto output;
}
output:
closeDbConnection();
echo json_encode($result);
exit();

?>