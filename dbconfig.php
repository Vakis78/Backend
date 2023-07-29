<?php

$deploy=FALSE;

$dbhost = 'localhost';
$dbport = '61000';
$dbuser = 'root';
$dbpass = 'kartoos';
$dbname = 'doono';

if ($deploy){
    //rewardloyalty
    $dbhost = 'localhost';
    $dbport = '3306';
    $dbuser = 'rewardloyalty_doono';
    $dbpass = 'loyaltydoono@123';
    $dbname = 'rewardloyalty_doono';
}

require('rb.php');

function openDbConnection(){
    global $dbhost,$dbuser,$dbpass,$dbname,$dbport;
    R::setup('mysql:host='.$dbhost.';port='.$dbport.';dbname='.$dbname,$dbuser,$dbpass);
    R::freeze(true);
}

function closeDbConnection($con=null){
    R::close();
}

?>