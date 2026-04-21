<?php
session_start();
include "config.php";

if(isset($_GET['id'])){
    $id = $_GET['id'];

    // 🔥 Prevent deleting yourself
    if($id != $_SESSION['user_id']){
        mysqli_query($conn, "DELETE FROM users WHERE id='$id'");
    }
}

header("Location: home.php");
exit();
?>