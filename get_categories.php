<?php

header("Access-Control-Allow-Origin: *");
try{
    include "dbconfig.php";
    openDbConnection();
    $user_id=$_GET["user_id"];
    $company_id=$_GET["company_id"];
    $categories=R::getAll("SELECT * FROM categories WHERE company_id=? ORDER BY `category_name`",array($company_id));
    $subcategories=R::getAll("SELECT * FROM subcategories WHERE company_id=? ORDER BY `sub_category_name`",array($company_id));
    $result=array("status"=>200,"categories"=>$categories,"sub_categories"=>$subcategories);
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