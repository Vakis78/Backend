<?php

header("Access-Control-Allow-Origin: *");
try{
    include "dbconfig.php";
    openDbConnection();
    $user_id=$_GET["user_id"];
    $query="SELECT co.*,cl.client_name,cl.client_phone,b.branch_name,b.branch_phone,b.branch_latitude,b.branch_longitude,b.branch_address,u.company_name,u.company_logo FROM clientorder co,branches b,`user` u,clients cl";
    $query.=" WHERE co.branch_id=b.branch_id AND u.user_id=b.company_id";
    $query.=" AND cl.client_id=co.client_id";
    $query.=" AND co.client_id=?";
    $query.=" ORDER BY co.order_dt DESC";
    $orders=R::getAll($query,array($user_id));
    $result=array("status"=>200,"orders"=>$orders);
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