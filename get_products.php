<?php
header("Access-Control-Allow-Origin: *");
try{
    include "dbconfig.php";
    openDbConnection();
    $user_id=$_GET["user_id"];
    $company_id=$_GET["company_id"];
    $category_id=$_GET["category_id"];
    $sub_category_id=$_GET["sub_category_id"];

    $products=R::getAll("SELECT * FROM products WHERE company_id=? AND category_id=? AND sub_category_id=? ORDER BY `product_name`",array($company_id,$category_id,$sub_category_id));
    //$products=R::getAll("SELECT * FROM products WHERE company_id=? ORDER BY `product_name`",array($company_id));
    $result=array("status"=>200,"products"=>$products);
    //sleep(1);
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