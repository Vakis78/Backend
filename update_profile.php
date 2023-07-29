<?php

function generate_motli_id(){
    $bytes = random_bytes(3);
    $token = bin2hex($bytes);
    return $token;
}

header("Access-Control-Allow-Origin: *");
try{
    include "dbconfig.php";
    openDbConnection();
    $user_id=$_POST["user_id"];
    $name=$_POST["name"];
    $email=$_POST["email"];
    $phone=$_POST["phone"];
    $birthday=$_POST["birthday"];
    $query="UPDATE clients SET client_name=?,client_email=?,client_phone=?,client_birthdate=?";
    $query.=" WHERE client_id=?";
    R::exec($query,array($name,$email,$phone,$birthday,$user_id));
    $user=R::getRow("SELECT * FROM `clients` WHERE client_email=?",array($email));
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