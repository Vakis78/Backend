<?php
header('Access-Control-Allow-Origin: *');
require_once "dbconfig.php";
$result=array("status"=>200);
try{
    openDbConnection();
    if ($_SERVER["REQUEST_METHOD"]=="POST"){
        $user_id=$_POST["user_id"];
        $usr=R::getRow("SELECT * FROM clients WHERE client_id=?",array($user_id));
        $invite_code="190257";
        $digits=5;
        $invite_code=str_pad(rand(10528, pow(10, $digits)-1), $digits, '0', STR_PAD_RIGHT);
        $friends=json_decode($_POST["friends"],TRUE);
        foreach ($friends as $f){
            $rf=R::dispense('friendrefer');
            $rf->user_id=$user_id;
            $rf->email=$f;
            $rf->code=$invite_code;
            R::store($rf);
        }
        include "email.php";
        foreach ($friends as $f){
            $body="You are invited by ".$usr["client_name"]." to download and use RewardLoyalty app";
            $body.="<br />Please use <b>".$invite_code."</b> as refer code when signing up"; 
            $body.="<br /><br />regards,<br />RewardLoyalty Team";
            email_send($f,"Invitation to RewardLoyalty",$body);
        }
        $result=array("status"=>200,"friends"=>count($friends));
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