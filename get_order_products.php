<?php

header("Access-Control-Allow-Origin: *");
try{
    include "dbconfig.php";
    openDbConnection();
    $user_id=$_GET["user_id"];
    $order_id=$_GET["order_id"];
    $query="SELECT op.*,p.product_name,p.product_image FROM orderproduct op,products p";
    $query.=" WHERE op.product_id=p.product_id";
    $query.=" AND op.order_id=?";
    $query.=" ORDER BY p.product_name ASC";
    $products=R::getAll($query,array($order_id));
    $result=array("status"=>200,"products"=>$products);
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