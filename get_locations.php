<?php

function compareBranchDistances($a,$b){
    if ($a["distance"]>$b["distance"]){
        return 1;
    }else if ($a["distance"]<$b["distance"]){
        return -1;
    }
    return 0;
}

function calculateDistance($lat1, $lon1, $lat2, $lon2, $unit) {
    if (($lat1 == $lat2) && ($lon1 == $lon2)) {
        return 0;
    }else {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515; //convert degress to miles
        if ($unit=='mi'){
            return $miles;
        }
        $kms=$miles * 1.609344; //miles to KMs
        if ($unit == 'km') {
            return $kms;
        }else if ($unit == 'nm') {
            return ($miles * 0.8684); //miles to Nautical miles
        }else if ($unit == 'm') {
            return $kms*1000; //KM to meters
        }
        //invalid unit
        return FALSE;
    }
}

header("Access-Control-Allow-Origin: *");
try{
    include "dbconfig.php";
    openDbConnection();
    $user_id=$_GET["user_id"];
    $address_id=$_GET["address_id"];
    $latitude=isset($_GET["latitude"])?$_GET["latitude"]:"0";
    $longitude=isset($_GET["longitude"])?$_GET["longitude"]:"0";
    $query="SELECT br.*,u.company_name,u.user_id AS company_id,u.company_logo,ac.currency_code,ac.currency_symbol FROM branches br,`user` u,app_currency ac WHERE u.user_id=br.company_id AND u.currency_id=ac.currency_id ORDER BY u.company_name,br.branch_name";
    $locations=R::getAll($query,array());
    $addr=FALSE;
    if ($address_id!='0'){
        $addr=R::getRow("SELECT * FROM clientaddress WHERE id=?",array($address_id));
    }
    if ($addr==FALSE && $latitude!="0" && $longitude!="0"){
        $addr=array("latitude"=>$latitude,"longitude"=>$longitude);
    }
    for ($a=0;$a<count($locations);$a++){
        $loc=$locations[$a];
        $loc["distance"]=0;
        $loc["distance_unit"]="";
        $loc["distance_text"]="";
        if ($addr!=FALSE && $loc["branch_latitude"]!="" && $loc["branch_longitude"]!="" && $addr["latitude"]!="" && $addr["longitude"]!=""){
            $dst=calculateDistance($loc["branch_latitude"],$loc["branch_longitude"],$addr["latitude"],$addr["longitude"],"km");
            $dst=round($dst,2);
            $loc["distance"]=$dst;
            $loc["distance_unit"]="km";
            $loc["distance_text"]=$dst." km";
        }
        $locations[$a]=$loc;
    }
    usort($locations,"compareBranchDistances");
    $result=array("status"=>200,"locations"=>$locations);
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