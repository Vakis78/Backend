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
        $card_id=$_POST["card_id"];
        $cart_total=isset($_POST["cart_total"])?$_POST["cart_total"]:"0";
        $tax_percentage=isset($_POST["tax_percentage"])?$_POST["tax_percentage"]:"0";
        $tax=isset($_POST["tax"])?$_POST["tax"]:"0";
        $discount_percentage=isset($_POST["discount_percentage"])?$_POST["discount_percentage"]:"0";
        $discount=isset($_POST["discount"])?$_POST["discount"]:"0";
        $coupon_code=isset($_POST["coupon_code"])?$_POST["coupon_code"]:"";

        //get user from user_id
        $user=R::getRow("SELECT * FROM `user` WHERE id=?",array($user_id));
        if ($user==FALSE){
            $result=array("status"=>404,"msg"=>"User not found");
            goto output;
        }

        //get card from card_id
        $card=R::getRow("SELECT * FROM usercard WHERE id=? AND user_id=?",array($card_id,$user_id));
        if ($card==FALSE){
            $result=array("status"=>404,"msg"=>"Card not found");
            goto output;
        }
    
        $sp=R::dispense('order');
        $sp->user_id=$user_id;
        $sp->order_dt=date('Y-m-d H:i:s');
        //$sp->status='active';
        $sp->status='payment_pending';
        $sp->order_number=time();
        $sp->destination=$address;
        $sp->order_total=$total;
        $sp->order_name=trim($user["first_name"]." ".$user["last_name"]);
        $sp->order_email=$user["email"];
        $sp->cart_total=$cart_total;
        $sp->tax_percentage=$tax_percentage;
        $sp->tax=$tax;
        $sp->discount_percentage=$discount_percentage;
        $sp->discount=$discount;
        $sp->coupon_code=$coupon_code;
        $order_id=R::store($sp);
        $products=json_decode($products,TRUE);
        foreach ($products as $p){
            $op=R::dispense('orderproduct');
            $op->order_id=$order_id;
            $op->product_id=$p["product_id"];
            $op->quantity=$p["quantity"];
            $op->total=$p["total"];
            if (isset($p["variants"])){
                $op->variants=json_encode($p["variants"]);
            }else{
                $op->variants="";
            }
            R::store($op);
        }

        // //charge the card
        // try{
        //     $cardData=array("card_number"=>$card["card_number"],"cvc"=>$card["cvc"],"exp_month"=>"","exp_year"=>"");
        //     $vals=explode("/",$card["expiry"]);
        //     $cardData["exp_month"]=trim($vals[0]);
        //     $cardData["exp_year"]='20'.trim($vals[1]);
        //     include "stripe.php";
        //     $response=stripe_create_card_token($cardData);
        //     $token=$response["id"];
        //     $stripe_total=number_format((floatval($total)*100), 0,'','');
        //     $chargeData=array("amount"=>$stripe_total,"currency"=>"USD","description"=>"Order:".$order_id,"source"=>$token);
        //     $response=stripe_charge_token($chargeData);
        //     $sp->status='processing';
        //     R::store($sp);

        //     //send email
        //     if ($sp->order_email!=null && $sp->order_email!=""){
        //         $email=$sp->order_email;
        //         $name=$sp->order_name;
        //         //$order_number=$sp->id;
        //         $order_number=$sp->order_number;
        //         $order_tracking_number=$sp->order_number;
        //         try{
        //             include "email.php";
        //             email_send_order_receipt(array("email"=>$email,"name"=>$name,"order_number"=>$order_number,"order_tracking_number"=>$order_tracking_number));
        //             $extra_body="<b>Address</b><br />".$sp->destination."<br /><br />";
        //             $extra_body.="<b>Products</b><br />";
        //             $extra_body.="<table border='1' style='width:100%;'><thead><tr><th>Name</th><th>Quantity</th><th>Total</th></tr></thead>";
        //             $extra_body.="<tbody>";
        //             foreach ($products as $p){
        //                 $g=R::getRow("SELECT * FROM `gear` WHERE id=?",array($p["product_id"]));
        //                 $pname="";
        //                 if ($g!=FALSE){
        //                     $pname=$g["name"];
        //                 }
        //                 $extra_body.="<tr>";
        //                 $extra_body.="<td>".$pname."</td>";
        //                 $extra_body.="<td>".$p["quantity"]."</td>";
        //                 $extra_body.="<td>$".$p["total"]."</td>";
        //                 $extra_body.="</tr>";
        //                 if (isset($p["variants"]) && is_array($p["variants"])){
        //                     foreach ($p["variants"] as $pv){
        //                         $extra_body.="<tr><td>&nbsp;</td>";
        //                         $extra_body.="<td colspan='2'>".$pv["title"].": ".$pv["option"]."</td>";
        //                         $extra_body.="</tr>";
        //                     }
        //                 }
        //             }
        //             $extra_body.="</tbody>";
        //             $extra_body.="</table>";
        //             $admin_email="info@footballissexy.com;abraham@tepia.co";
        //             //$admin_email="rameez@tepia.co";
        //             $admin_email_vars=array("email"=>$admin_email,"customer_email"=>$email,"customer_name"=>$name,"order_number"=>$order_number,"order_tracking_number"=>$order_tracking_number,"extra_body"=>$extra_body);
        //             email_send_order_admin($admin_email_vars);
        //         }catch(Exception $ex){  
        //         }
        //     }
        // }catch(Exception $ex){
        //     $result=array("status"=>404,"msg"=>$ex->getMessage());
        //     goto output;
        // }
        $result=array("status"=>200);
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