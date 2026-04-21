<?php
session_start();
include "config.php";

// Optional login - users can access without logging in
$count = 0;
if (isset($_SESSION['loggedin'])) {
    $user_id = $_SESSION['user_id'];
    $count_q = mysqli_query($conn, "SELECT SUM(quantity) as total FROM cart WHERE user_id='$user_id'");
    $count = mysqli_fetch_assoc($count_q)['total'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact - TimeSteal</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">

<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    padding-top: 90px;
    font-family: 'Poppins', sans-serif;
    background: #050505;
    color: white;
    overflow-x: hidden;
}

.navbar {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    background: rgba(0, 0, 0, 0.95);
    backdrop-filter: blur(10px);
    z-index: 9999;
    padding: 15px 60px;
}

.nav-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    max-width: 1400px;
    margin: 0 auto;
}

.logo {
    color: #c6a45a;
    font-family: 'Playfair Display', serif;
    font-size: 24px;
    font-weight: 600;
    letter-spacing: 4px;
}

.nav-links {
    display: flex;
    flex-direction: row;
    align-items: center;
    list-style: none;
    gap: 30px;
    margin: 0;
    padding: 0;
}

.nav-links li {
    list-style: none;
}

.nav-links a {
    position: relative;
    color: white;
    text-decoration: none;
    font-size: 14px;
    transition: 0.3s;
}

.nav-links a::after {
    content: "";
    position: absolute;
    left: 0;
    bottom: -4px;
    width: 0;
    height: 2px;
    background: #c6a45a;
    transition: 0.4s ease;
}

.nav-links a:hover {
    color: #c6a45a;
}

.nav-links a:hover::after {
    width: 100%;
}

.cart-icon {
    position: relative;
}

.cart-count {
    position: absolute;
    top: -10px;
    right: -12px;
    background: #c6a45a;
    color: black;
    font-size: 11px;
    padding: 3px 7px;
    border-radius: 50%;
    font-weight: 600;
}

.auth-buttons {
    display: flex;
    align-items: center;
    gap: 12px;
}

.btn-login,
.btn-register {
    padding: 10px 18px;
    border-radius: 30px;
    text-decoration: none;
    font-size: 13px;
    transition: 0.3s ease;
}

.btn-login {
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.btn-register {
    background: #c6a45a;
    color: black;
    font-weight: 600;
}

.btn-login:hover {
    border-color: #c6a45a;
    color: #c6a45a;
}

.btn-register:hover {
    background: #d4b46a;
}

.user-menu {
    position: relative;
}

.user-btn {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    color: #ddd;
    font-size: 14px;
    transition: 0.3s;
}

.user-btn:hover {
    color: #c6a45a;
}

.avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, #c6a45a, #d4af37);
    display: flex;
    align-items: center;
    justify-content: center;
    color: black;
    font-weight: bold;
    font-size: 14px;
}

.avatar.small {
    width: 28px;
    height: 28px;
    font-size: 12px;
}

.arrow {
    font-size: 10px;
}

.dropdown {
    position: absolute;
    top: 50px;
    right: 0;
    width: 250px;
    background: rgba(15, 15, 15, 0.95);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(198, 164, 90, 0.2);
    border-radius: 12px;
    padding: 15px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(10px);
    transition: 0.3s ease;
    z-index: 10000;
}

.dropdown.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.dropdown-header {
    font-size: 12px;
    color: #888;
    margin-bottom: 10px;
}

.account {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px;
    border-radius: 8px;
}

.dropdown-divider {
    height: 1px;
    background: rgba(255, 255, 255, 0.1);
    margin: 10px 0;
}

.dropdown a {
    display: block;
    padding: 10px;
    color: #ddd;
    text-decoration: none;
    border-radius: 8px;
    transition: 0.3s;
}

.dropdown a:hover {
    background: rgba(198, 164, 90, 0.1);
    color: #c6a45a;
}

#cursor-glow {
    position: fixed;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(198, 164, 90, 0.2), transparent);
    pointer-events: none;
    transform: translate(-50%, -50%);
    filter: blur(80px);
    z-index: 9998;
}

.header {
    text-align: center;
    padding: 100px 20px 40px;
}

.header h1 {
    margin: 0 0 10px;
    font-family: 'Playfair Display', serif;
    letter-spacing: 5px;
    color: #c6a45a;
    font-size: 40px;
}

.header p {
    margin: 0;
    color: #aaa;
}

.section {
    max-width: 1200px;
    margin: auto;
    padding: 40px;
}

#map {
    height: 450px;
    border-radius: 20px;
    margin-bottom: 50px;
    box-shadow: 0 0 40px rgba(198, 164, 90, 0.2);
}

.stores {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
}

.store-card {
    background: rgba(255, 255, 255, 0.05);
    padding: 25px;
    border-radius: 20px;
    backdrop-filter: blur(15px);
    transition: 0.5s;
    border: 1px solid transparent;
}

.store-card:hover {
    border: 1px solid #c6a45a;
    transform: translateY(-10px);
    box-shadow: 0 0 30px rgba(198, 164, 90, 0.5);
}

.store-card h3 {
    color: #c6a45a;
}

.contact-box {
    max-width: 500px;
    margin: 80px auto;
    background: rgba(255, 255, 255, 0.05);
    padding: 40px;
    border-radius: 20px;
    backdrop-filter: blur(15px);
}

.inputBox {
    position: relative;
    margin: 20px 0;
}

.inputBox input,
.inputBox textarea {
    width: 100%;
    padding: 12px 0;
    background: transparent;
    border: none;
    border-bottom: 1px solid #444;
    color: white;
    outline: none;
    font-family: inherit;
    resize: vertical;
}

.inputBox label {
    position: absolute;
    top: 10px;
    left: 0;
    color: #777;
    transition: 0.3s;
    pointer-events: none;
}

.inputBox input:focus ~ label,
.inputBox input:valid ~ label,
.inputBox textarea:focus ~ label,
.inputBox textarea:valid ~ label {
    top: -10px;
    font-size: 12px;
    color: #c6a45a;
}

.btn {
    width: 100%;
    padding: 12px;
    border: 1px solid #c6a45a;
    background: transparent;
    color: #c6a45a;
    border-radius: 30px;
    cursor: pointer;
    transition: 0.4s;
    position: relative;
    overflow: hidden;
    font-family: inherit;
}

.btn::before {
    content: "";
    position: absolute;
    width: 100%;
    height: 100%;
    background: #c6a45a;
    left: -100%;
    top: 0;
    transition: 0.4s;
    z-index: -1;
}

.btn:hover::before {
    left: 0;
}

.btn:hover {
    color: black;
    box-shadow: 0 0 20px rgba(198, 164, 90, 0.6);
}

.fade {
    opacity: 0;
    transform: translateY(40px);
    transition: 0.8s ease;
}

.fade.show {
    opacity: 1;
    transform: translateY(0);
}

@media (max-width: 900px) {
    .navbar {
        padding: 15px 20px;
    }

    .nav-container {
        flex-direction: column;
        gap: 15px;
    }

    .nav-links {
        flex-wrap: wrap;
        justify-content: center;
        gap: 16px;
    }

    .section {
        padding: 24px;
    }

    .header {
        padding-top: 70px;
    }
}
</style>
</head>

<body>

<header class="navbar">
    <div class="nav-container">
        <div class="logo">TIME STEAL</div>
        <ul class="nav-links">
            <li><a href="home.php">Home</a></li>
            <li><a href="home.php#collection">Collection</a></li>
            <li><a href="about.html">About</a></li>
            <li><a href="contact.php">Contact</a></li>
            <li class="cart-icon">
                <a href="cart.php">Cart</a>
                <?php if ($count > 0): ?>
                    <span class="cart-count"><?php echo $count; ?></span>
                <?php endif; ?>
            </li>

            <?php if (isset($_SESSION['loggedin'])): ?>
                <li class="user-menu">
                    <div class="user-btn" onclick="toggleDropdown()">
                        <div class="avatar">
                            <?php echo strtoupper($_SESSION['user'][0]); ?>
                        </div>
                        <span><?php echo $_SESSION['user']; ?></span>
                        <span class="arrow">▼</span>
                    </div>
                    <div id="dropdown" class="dropdown">
                        <div class="dropdown-header">Accounts</div>
                        <div class="account">
                            <div class="avatar small">
                                <?php echo strtoupper($_SESSION['user'][0]); ?>
                            </div>
                            <span><?php echo $_SESSION['user']; ?></span>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a href="logout.php" class="logout">Logout</a>
                    </div>
                </li>
            <?php else: ?>
                <li class="auth-buttons">
                    <a href="login.php" class="btn-login">Login</a>
                    <a href="register.php" class="btn-register">Register</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</header>

<div id="cursor-glow"></div>

<div class="header fade">
    <h1>TIME STEAL</h1>
    <p>Visit our luxury showrooms across India</p>
</div>

<div class="section">
    <div id="map" class="fade"></div>

    <div class="stores">
        <div class="store-card fade">
            <h3>Mumbai Flagship</h3>
            <p>Jio World Plaza, BKC</p>
            <p>+91 98765 43210</p>
        </div>

        <div class="store-card fade">
            <h3>Delhi Showroom</h3>
            <p>DLF Emporio, Vasant Kunj</p>
            <p>+91 91234 56789</p>
        </div>

        <div class="store-card fade">
            <h3>Bangalore Studio</h3>
            <p>UB City Mall</p>
            <p>+91 99887 77665</p>
        </div>
    </div>

    <br><br>

    <button onclick="findStore()" class="btn fade">Find Nearest Store</button>
</div>

<div class="contact-box fade">
    <h2 style="text-align:center;color:#c6a45a;">Get in Touch</h2>

    <form action="send_mail.php" method="POST">
        <div class="inputBox">
            <input type="text" name="name" required>
            <label>Name</label>
        </div>

        <div class="inputBox">
            <input type="email" name="email" required>
            <label>Email</label>
        </div>

        <div class="inputBox">
            <textarea name="message" rows="4" required></textarea>
            <label>Message</label>
        </div>

        <button class="btn" type="submit">Send Message</button>
    </form>
</div>

<script>
document.addEventListener("mousemove", function(e) {
    const glow = document.getElementById("cursor-glow");
    glow.style.left = e.clientX + "px";
    glow.style.top = e.clientY + "px";
});

const observer = new IntersectionObserver(function(entries) {
    entries.forEach(function(entry) {
        if (entry.isIntersecting) {
            entry.target.classList.add("show");
        }
    });
});

document.querySelectorAll(".fade").forEach(function(el) {
    observer.observe(el);
});

function findStore() {
    navigator.geolocation.getCurrentPosition(function(pos) {
        const userLat = pos.coords.latitude;
        const userLng = pos.coords.longitude;

        const stores = [
            { name: "Mumbai", lat: 19.0760, lng: 72.8777 },
            { name: "Delhi", lat: 28.6139, lng: 77.2090 },
            { name: "Bangalore", lat: 12.9716, lng: 77.5946 }
        ];

        let nearest = stores[0];
        let min = Number.MAX_VALUE;

        stores.forEach(function(store) {
            const distance = Math.hypot(userLat - store.lat, userLng - store.lng);
            if (distance < min) {
                min = distance;
                nearest = store;
            }
        });

        alert("Nearest Store: " + nearest.name);
    });
}

function initMap() {
    const india = { lat: 20.5937, lng: 78.9629 };

    const map = new google.maps.Map(document.getElementById("map"), {
        zoom: 5,
        center: india,
        styles: [
            { elementType: "geometry", stylers: [{ color: "#1d1d1d" }] },
            { elementType: "labels.text.fill", stylers: [{ color: "#c6a45a" }] }
        ]
    });

    const locations = [
        { lat: 19.0760, lng: 72.8777, title: "Mumbai" },
        { lat: 28.6139, lng: 77.2090, title: "Delhi" },
        { lat: 12.9716, lng: 77.5946, title: "Bangalore" }
    ];

    locations.forEach(function(loc) {
        new google.maps.Marker({
            position: loc,
            map: map,
            title: loc.title
        });
    });
}

function toggleDropdown() {
    const dropdown = document.getElementById("dropdown");
    if (dropdown) {
        dropdown.classList.toggle("show");
    }
}

window.addEventListener("click", function(e) {
    if (!e.target.closest(".user-menu")) {
        const dropdown = document.getElementById("dropdown");
        if (dropdown) {
            dropdown.classList.remove("show");
        }
    }
});
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCTNnJwVs8TqfMBW4EzRRu5W1c75-yjygI&callback=initMap" async defer></script>

</body>
</html>
