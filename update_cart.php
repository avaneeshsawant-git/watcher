<?php
session_start();
include "config.php";

$id = $_POST['id'];
$action = $_POST['action'];

if($action == "increase"){
    mysqli_query($conn, "UPDATE cart SET quantity = quantity + 1 WHERE id='$id'");
}
else if($action == "decrease"){
    mysqli_query($conn, "UPDATE cart SET quantity = quantity - 1 WHERE id='$id' AND quantity > 1");
}

echo "updated";
?>