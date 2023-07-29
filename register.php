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
    $name=$_POST["name"];
    $email=$_POST["email"];
    $password=$_POST["password"];
    $phone=$_POST["phone"];
    $birthday=$_POST["birthday"];
    $refer_code=isset($_POST["refer_code"])?$_POST["refer_code"]:"";
    $company_id=2;
    $uniquecode=md5($email.time());
    $user=R::getRow("SELECT * FROM `clients` WHERE client_email=?",array($email));
    if ($user!=FALSE){
        $result=array("status"=>404,"msg"=>"Email already registered");
        goto output;
    }
    $cmp=R::getRow("SELECT * FROM `user` WHERE user_id=?",array($company_id));
    if ($refer_code!=""){
        $rf=R::getRow("SELECT * FROM friendrefer WHERE code=? AND email=? AND is_used=0",array($refer_code,$email));
        if ($rf==FALSE){
            $result=array("status"=>404,"msg"=>"Invalid refer code");
            goto output;
        }
        //now update to be is_used=1
        R::exec("UPDATE friendrefer SET is_used=1 WHERE id=?",array($rf["id"]));
        //insert into points_count table
        $points_to_insert=$cmp["friend_refer_points"];
        $purpose="Friend Sign up";
        $query="INSERT INTO points_count(client_id,company_id,branch_id,purpose,point_value,product_name)";
        $query.=" VALUES(?,?,?,?,?,?)";
        $params=array($rf["user_id"],$company_id,0,$purpose,$points_to_insert,$name);
        R::exec($query,$params);
        
    }
    $query="INSERT INTO clients(client_password,client_name,client_email,client_phone,client_birthdate,client_register,company_id,uniquecode)";
    $query.=" VALUES(MD5(?),?,?,?,?,NOW(),?,?)";
    R::exec($query,array($password,$name,$email,$phone,$birthday,$company_id,$uniquecode));
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