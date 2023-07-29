<?php

header("Access-Control-Allow-Origin: *");
try{
    include "dbconfig.php";
    openDbConnection();
    $email=$_POST["email"];
    $user=R::getRow("SELECT * FROM `user` WHERE email=?",array($email));
    if ($user==FALSE){
        $result=array("status"=>404,"msg"=>"Email address not found");
        goto output;
    }
    $result=array("status"=>200);
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