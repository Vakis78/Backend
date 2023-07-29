<?php
header("Access-Control-Allow-Origin: *");
try{
    include "dbconfig.php";
    openDbConnection();
    $user_id=$_GET["user_id"];
    //$query="SELECT SUM(pc.point_value) AS total_points,br.branch_name,u.company_name,u.company_logo,pc.company_id,pc.branch_id FROM points_count pc,branches br,user u WHERE pc.client_id=? AND br.branch_id=pc.branch_id AND u.user_id=pc.company_id GROUP BY pc.branch_id";
    $query="SELECT SUM(pc.point_value) AS total_points,u.company_name,u.company_logo,pc.company_id,pc.branch_id FROM points_count pc,user u WHERE pc.client_id=? AND u.user_id=pc.company_id AND pc.purpose<>'Promo product claimed' GROUP BY pc.company_id";
    $points=R::getAll($query,array($user_id));
    for ($a=0;$a<count($points);$a++){
        $pt=$points[$a];
        $query="SELECT * FROM rewards WHERE company_id=? ORDER BY `name`";
        $pt["rewards"]=R::getAll($query,array($pt["company_id"]));
        $points[$a]=$pt;
    }
    $result=array("status"=>200,"points"=>$points);
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