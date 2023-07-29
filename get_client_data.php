<?php

header("Access-Control-Allow-Origin: *");
try{
    include "dbconfig.php";
    openDbConnection();
    $user_id=$_GET["user_id"];
    $addresses=R::getAll("SELECT * FROM clientaddress WHERE client_id=? ORDER BY `address_title`",array($user_id));
    $cards=R::getAll("SELECT * FROM clientcard WHERE client_id=?",array($user_id));
    $result=array("status"=>200,"addresses"=>$addresses,"cards"=>$cards);
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