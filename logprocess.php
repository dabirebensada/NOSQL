<?php
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $db = getMongoDBConnection();
    $usersCollection = $db->users;

    $user = $usersCollection->findOne(["username" => $username]);

    if ($user && password_verify($password, $user["password"])) {
        session_start();
        $_SESSION["username"] = $user["username"];
        $_SESSION["role"] = $user["role"];

        if ($user["role"] === "admin") {
            header("Location: ../admin.php");
        } else {
            header("Location: ../index.php");
        }
        exit();
    } else {
        header("Location: ../login.php?error=invalid_credentials");
        exit();
    }
}
?>