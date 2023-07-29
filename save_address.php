<?php
header('Access-Control-Allow-Origin: *');
require_once "dbconfig.php";
$result=array("status"=>200);
try{
    openDbConnection();
    if ($_SERVER["REQUEST_METHOD"]=="POST"){
        $user_id=$_POST["user_id"];
        $address_title=$_POST["name"];
        $full_address=$_POST["full_address"];
        $latitude=$_POST["latitude"];
        $longitude=$_POST["longitude"];
        // $city=$_POST["city"];
        // $state=$_POST["state"];
        // $zip=$_POST["zip"];
    
        //$sp=R::findOne('clientaddress','client_id=?',array($user_id));
        //if ($sp==null || $sp->id==0){
            $sp=R::dispense('clientaddress');
        //}
        $sp->client_id=$user_id;
        $sp->address_title=$address_title;
        $sp->full_address=$full_address;
        $sp->latitude=$latitude;
        $sp->longitude=$longitude;
        // $sp->city=$city;
        // $sp->state=$state;
        // $sp->zip=$zip;
        R::store($sp);
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