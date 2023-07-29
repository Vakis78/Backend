<?php

function email_send($email_to,$email_subject,$email_body){
    $smtp_email_from="notifications@rewardloyalty.eu";    	 
    $extra_headers='MIME-Version: 1.0'."\r\n";
    $extra_headers.='Content-type: text/html; charset=iso-8859-1'."\r\n";
    $extra_headers.='From: '.$smtp_email_from."\r\n";
    return mail($email_to,$email_subject,$email_body,$extra_headers);
}

?>