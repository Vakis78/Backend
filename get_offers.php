<?php
header("Access-Control-Allow-Origin: *");
try{
    include "dbconfig.php";
    openDbConnection();
    $company_id=2;//$_GET["company_id"];
    $query="SELECT * FROM offers WHERE company_id=? ORDER BY offer_dt DESC";
    $points=R::getAll($query,array($company_id));
    $result=array("status"=>200,"offers"=>$points);
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