<?php
header("Access-Control-Allow-Origin: *");
try{
    include "dbconfig.php";
    openDbConnection();
    $user_id=$_GET["user_id"];
    $company_id="2";
    $query="SELECT SUM(pc.point_value) AS total_points FROM points_count pc WHERE pc.client_id=? AND pc.purpose<>'Promo product claimed'";
    $pt=R::getRow($query,array($user_id));
    $added_points=0;
    if ($pt!=FALSE){
        $tp=$pt["total_points"];
        if ($tp!=""){
            $added_points=intval($tp);
        }
    }
    $query="SELECT SUM(pc.point_value) AS total_points FROM points_count pc WHERE pc.client_id=? AND pc.purpose='Promo product claimed'";
    $pt=R::getRow($query,array($user_id));
    $redeemed_points=0;
    if ($pt!=FALSE){
        $tp=$pt["total_points"];
        if ($tp!=""){
            $redeemed_points=intval($tp);
        }
    }
    $total_points=$added_points-$redeemed_points;
    $points=array(array("total_points"=>$total_points,"company_id"=>$company_id,"company_logo"=>""));
    for ($a=0;$a<count($points);$a++){
        $pt=$points[$a];
        //$query="SELECT * FROM rewards WHERE company_id=? ORDER BY `name`";
        //$rewards=R::getAll($query,array($pt["company_id"]));
        $query="SELECT point_value AS points,purpose,product_name,visited FROM points_count pc WHERE client_id=?";
        $rewards=R::getAll($query,array($user_id));
        for ($b=0;$b<count($rewards);$b++){
            $r=$rewards[$b];
            $purpose=$r["purpose"];
            $r["sign"]="+";
            $r["name"]=$purpose;
            if ($purpose=="Promo product claimed"){
                $r["sign"]="-";
                $r["name"]="Claimed - ".$r["product_name"];
            }else if ($purpose=="A visit"){
                $r["name"]="Visit to our Store";
            }else if ($purpose=="Friend Sign up"){
                $r["name"]="Friend Sign Up - ".$r["product_name"];
            }
            $rewards[$b]=$r;
        }
        $pt["rewards"]=$rewards;
        $points[$a]=$pt;
    }
    $result=array("status"=>200,"points"=>$points,"added_points"=>$added_points,"redeemed_points"=>$redeemed_points);
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