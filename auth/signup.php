<?php

include "../connection.php";

$username = filterRequest("username");
$email = filterRequest("email");
$password = sha1($_POST['password']);
$phone = filterRequest("phone");
$verifycode = rand(10000,99999);


$stmt = $con->prepare("SELECT * FROM users WHERE users_email = ? OR users_phone = ?");
$stmt->execute(array($email, $phone));

$count = $stmt->rowCount();

if ($count > 0) {
    printFailure("Email or Phone already exists");
} else {
    $data = array(
        "users_name" => $username,
        "users_email" => $email,
        "users_phone" => $phone,
        "users_password" => $password,
        "users_verifycode" => $verifycode,
    );

    insertData("users", $data);
}
