<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit();
}
?>

<h1>Welcome to TimeSteal</h1>
<p>Hello, <?php echo $_SESSION['user']; ?></p>

<a href="logout.php">Logout</a>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TimeSteal Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins&display=swap" rel="stylesheet">

    <style>
    /* ===== NAVBAR ===== */
    .navbar {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        background: rgba(0,0,0,0.95);
        backdrop-filter: blur(10px);
        z-index: 9999;
        padding: 15px 60px;
    }

    .nav-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        max-width: 1300px;
        margin: auto;
    }

    .logo {
        color: #c6a45a;
        font-family: 'Playfair Display', serif;
        font-size: 24px;
        font-weight: 600;
        letter-spacing: 4px;
    }

    nav {
        display: flex;
        gap: 35px;
        align-items: center;
    }

    nav a {
        position: relative;
        color: white;
        text-decoration: none;
        font-size: 14px;
        transition: 0.3s;
    }

    nav a::after {
        content: "";
        position: absolute;
        left: 0;
        bottom: -4px;
        width: 0%;
        height: 2px;
        background: #c6a45a;
        transition: 0.4s ease;
    }

    nav a:hover {
        color: #c6a45a;
    }

    nav a:hover::after {
        width: 100%;
    }

    /* Body padding for fixed navbar */
    body {
        padding-top: 90px;
    }
    </style>
</head>

<body>

<header class="navbar">
<div class="nav-container">
<div class="logo">TIME STEAL</div>
<nav>
<a href="home.php">Home</a>
<a href="cart.php">Cart</a>
<a href="logout.php">Logout</a>
</nav>
</div>
</header>

<h1>Welcome, <?php echo $_SESSION['user']; ?></h1>

<!-- 👇 IMPORTANT (TRANSITION SCRIPT) -->
<script src="transition.js"></script>

</body>
</html>