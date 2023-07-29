<?php

header("Access-Control-Allow-Origin: *");
try{
    include "dbconfig.php";
    openDbConnection();
    $email=$_POST["email"];
    $password=$_POST["password"];
    $user=R::getRow("SELECT * FROM `clients` WHERE client_email=? AND client_password=?",array($email,md5($password)));
    if ($user==FALSE){
        $result=array("status"=>404,"msg"=>"Invalid email/password");
        goto output;
    }
    unset($user["client_password"]);
    $user["id"]=$user["client_id"];
    $result=array("status"=>200,"user"=>$user);
    goto output;
}catch(Exception $ex){
    $result=array("status"=>500,"msg"=>$ex->getMessage());
    goto output;
}
output:
closeDbConnection();
echo json_encode($result);
exit();
?>