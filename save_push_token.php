<?php
header('Access-Control-Allow-Origin: *');
require_once "dbconfig.php";
$result=array("status"=>200);
try{
    openDbConnection();
    if ($_SERVER["REQUEST_METHOD"]=="POST"){
        $user_id=$_POST["user_id"];
        $token=$_POST["token"];
        $user=R::getRow("SELECT id FROM `pushtoken` WHERE `user_id`=? AND `token`=?",array($user_id,$token));
        if ($user!=FALSE){
            $result=array("status"=>200);
            goto output;
        }
        $user=R::dispense('pushtoken');
        $user->user_id=$user_id;
        $user->token=$token;
        $user->token_dt=date('Y-m-d H:i:s');
        R::store($user);
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