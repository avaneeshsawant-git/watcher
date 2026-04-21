<?php
session_start();
include "config.php";

if(isset($_GET['user'])){

    $username = $_GET['user'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if($row = $result->fetch_assoc()){
        $_SESSION['user'] = $row['username'];
        $_SESSION['user_id'] = $row['id'];

        header("Location: home.php");
        exit();
    }
}
?>