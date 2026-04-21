<?php
session_start();
include "config.php";

if(!isset($_SESSION['user_id'])){
    if(isset($_GET['ajax'])) {
        header("Content-Type: application/json");
        echo json_encode(["success" => false, "message" => "Login required"]);
        exit();
    }

    die("Login required");
}

$user_id = $_SESSION['user_id'];
$product_id = (int) $_GET['id'];

// Check if already in cart
$check = mysqli_query($conn, "SELECT * FROM cart WHERE user_id='$user_id' AND product_id='$product_id'");

if(mysqli_num_rows($check) > 0){
    mysqli_query($conn, "UPDATE cart SET quantity = quantity + 1 WHERE user_id='$user_id' AND product_id='$product_id'");
} else {
    mysqli_query($conn, "INSERT INTO cart(user_id, product_id, quantity) VALUES('$user_id','$product_id',1)");
}

// ✅ TOAST MESSAGE
$_SESSION['cart_msg'] = "Item added to cart ✔";

if(isset($_GET['ajax'])) {
    $count_q = mysqli_query($conn, "SELECT SUM(quantity) as total FROM cart WHERE user_id='$user_id'");
    $count = mysqli_fetch_assoc($count_q)['total'] ?? 0;

    header("Content-Type: application/json");
    echo json_encode([
        "success" => true,
        "message" => "Added to cart",
        "cartCount" => (int) $count
    ]);
    exit();
}

$redirect = $_SERVER['HTTP_REFERER'] ?? "home.php";
if (strpos($redirect, "timesteal") === false) {
    $redirect = "home.php";
}

// Redirect
header("Location: " . $redirect);
exit();
?>
