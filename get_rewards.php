<?php
header("Access-Control-Allow-Origin: *");
try{
    include "dbconfig.php";
    openDbConnection();
    $user_id=$_GET["user_id"];
    $company_id=$_GET["company_id"];
    $query="SELECT * FROM rewards WHERE company_id=? ORDER BY `name`";
    $rewards=R::getAll($query,array($company_id));
    $result=array("status"=>200,"rewards"=>$rewards);
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