<?php
include "email.php";
$r=email_send("rameez_usm@hotmail.com","Doono order","Hello this it the body for Doono order.<br />Please check");
echo "mail return: ".$r;
?>