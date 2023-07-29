<?php
header('Access-Control-Allow-Origin: *');
require_once "dbconfig.php";
$result=array("status"=>200);
try{
    openDbConnection();
    if ($_SERVER["REQUEST_METHOD"]=="POST"){
        $user_id=$_POST["user_id"];
        $card_name=$_POST["name"];
        $card_number=$_POST["card_number"];
        $expiry=$_POST["expiry"];
        $cvc=$_POST["cvc"];

        $vals=explode("/",$expiry);
        if (count($vals)!=2){
            $result=array("status"=>500,"msg"=>"Please enter valid card expiry (MM/YY)");
            goto output;
        }

        //validate fields and card number
        $cd=R::getRow("SELECT id,expiry,cvc FROM clientcard WHERE client_id=? AND card_number=?",array($user_id,$card_number));
        if ($cd!=FALSE && $cd["expiry"]==$expiry && $cd["cvc"]==$cvc){
            $result=array("status"=>403,"msg"=>"You already have a card saved with these details");
            goto output;
        }
    
        $sp=R::dispense('clientcard');
        $sp->client_id=$user_id;
        $sp->card_name=$card_name;
        $sp->card_number=$card_number;
        $sp->expiry=$expiry;
        $sp->cvc=$cvc;
        $card_id=R::store($sp);
        $result=array("status"=>200,"card_id"=>$card_id);
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