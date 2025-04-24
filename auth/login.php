<?php

include "../connection.php";

$password = sha1($_POST['password']);
$email = filterRequest("email"); 

// $stmt = $con->prepare("SELECT * FROM users WHERE users_email = ? AND  users_password = ?");
// $stmt->execute(array($email, $password));
// $count = $stmt->rowCount();
// result($count) ; 


getData("users" , "users_email = ? AND  users_password = ?" , array($email , $password)) ; 
