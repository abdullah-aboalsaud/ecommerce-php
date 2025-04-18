<?php

include "connection.php";

$table = "users";
$data = array(
    "users_name"=> "abdullah",
    "users_email"=> "abdullah@gmail.com",
    "users_phone"=> "0123456789",   
    "users_verifycode"=> "0123456789",   
);

$count= insertData($table, $data);