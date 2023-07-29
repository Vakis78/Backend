<?php

header("Access-Control-Allow-Origin: *");
try{
    include "dbconfig.php";
    include "zoom.php";
    include "email.php";
    openDbConnection();
    $user_id=$_POST["user_id"];
    $provider_id=$_POST["provider_id"];
    $date_time=$_POST["date_time"];
    $duration=$_POST["duration"];
    $notes=$_POST["notes"];

    $provider=R::getRow("SELECT * FROM `user` WHERE id=? AND user_type='provider'",array($provider_id));
    $user=R::getRow("SELECT * FROM `user` WHERE id=?",array($user_id));
    
    $dtvals=explode("T",$date_time);
    $dvals=$dtvals[0];
    $tvals=explode(":",$dtvals[1]);

    $date_time=$dvals." ".$tvals[0].":".$tvals[1];
    $duration_mins=intval($duration)*60; //convert into minutes

    $subject="Motli Appointment";
    //generate zoom link here and then update appointment
    $meetingData=array(
        "agenda"=>$subject,
        "duration"=>$duration_mins,
        "topic"=>$subject,
    );
    $vals=zoom_create_meeting($meetingData);
    $zoom_link=$vals["join_url"];
    $zoom_password=$vals["password"];
    
    $appointment=R::dispense('appointment');
    $appointment->user_id=$user_id;
    $appointment->provider_id=$provider_id;
    $appointment->appointment_dt=$date_time;
    $appointment->hourly_rate=$provider["hourly_rate"];
    $appointment->duration=$duration;
    $appointment->notes=$notes;
    $appointment->booking_dt=date('Y-m-d H:i:s');
    $appointment->zoom_link=$zoom_link;
    $appointment->zoom_password=$zoom_password;
    R::store($appointment);

    //send email to both parties with zoom link
    $location="";
    $startTime=$date_time;
    $ts=strtotime($startTime);
    $ts+=(60*$duration_mins);
    $endTime=date('Y-m-d H:i',$ts);
    //description for user
    $description="Your appointment with ".$provider["name"]." is booked.<br /><br />";
    $description.="Zoom link: ".$zoom_link."<br />";
    $description.="Zoom password: ".$zoom_password."<br />";
    if ($notes!=""){
        $description.="<br /><b>Additional Details</b><br />".$notes."<br />";
    }
    mail_ical_event_send("Motli Appointments","motliappointments@upcite.net",$user["name"],$user["email"],$startTime,$endTime,$subject,$description,$location);
    //description for provider
    $description="Your appointment with ".$user["name"]." is booked.<br /><br />";
    $description.="Zoom link: ".$zoom_link."<br />";
    $description.="Zoom password: ".$zoom_password."<br />";
    if ($notes!=""){
        $description.="<br /><b>Additional Details</b><br />".$notes."<br />";
    }
    mail_ical_event_send("Motli Appointments","motliappointments@upcite.net",$provider["name"],$provider["email"],$startTime,$endTime,$subject,$description,$location);

    $result=array("status"=>200);
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