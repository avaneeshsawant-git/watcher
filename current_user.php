<?php
session_start();
include "config.php";

header("Content-Type: application/json");

if (!isset($_SESSION['loggedin'], $_SESSION['user_id'])) {
    echo json_encode([
        "loggedIn" => false,
        "username" => "User"
    ]);
    exit();
}

$user_id = (int) $_SESSION['user_id'];
$username = $_SESSION['user'] ?? "User";

$stmt = $conn->prepare("SELECT username FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $username = $row['username'];
    $_SESSION['user'] = $username;
}

echo json_encode([
    "loggedIn" => true,
    "username" => $username
]);
