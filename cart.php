<?php
session_start();
include "config.php";
include "products_catalog.php";

$user_id = $_SESSION['user_id'] ?? 0;
$username = $_SESSION['user'] ?? "User";
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Your Cart - TimeSteal</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display&family=Poppins&display=swap" rel="stylesheet">

<style>

/* BASE */
body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background: #050505;
    color: white;
}

/* NAVBAR */
.navbar {
    width: 93%;
    background: #000;
    padding: 18px 40px;
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

.nav-links {
    display: flex;
    gap: 35px;
    align-items: center;
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
    width: 0%;
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

.user {
    display: flex;
    align-items: center;
    gap: 10px;
}

.avatar {
    width: 35px;
    height: 35px;
    background: #c6a45a;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: black;
    font-weight: bold;
}

/* LAYOUT */
.cart-container {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 40px;
    max-width: 1200px;
    margin: auto;
    padding: 40px;
}

/* ITEMS */
.cart-items {
    background: rgba(255,255,255,0.05);
    padding: 30px;
    border-radius: 20px;
}

.cart-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 25px 0;
    border-bottom: 1px solid #333;
    gap: 20px;
}

.cart-product {
    display: flex;
    align-items: center;
    gap: 16px;
    min-width: 260px;
}

.cart-thumb {
    width: 72px;
    height: 72px;
    object-fit: cover;
    border-radius: 10px;
    border: 1px solid rgba(198,164,90,0.4);
    background: #111;
    flex: 0 0 72px;
}

.cart-product h3 {
    margin: 0 0 6px;
}

.cart-product p {
    margin: 0;
}

/* QTY */
.qty {
    display: flex;
    gap: 10px;
    align-items: center;
}

.qty button {
    border: 1px solid #c6a45a;
    background: transparent;
    color: #c6a45a;
    width: 35px;
    height: 35px;
    border-radius: 6px;
    cursor: pointer;
}

.qty button:hover {
    background: #c6a45a;
    color: black;
}

/* REMOVE */
.remove-btn {
    border: 1px solid #c6a45a;
    padding: 8px 18px;
    border-radius: 25px;
    color: #c6a45a;
    text-decoration: none;
    transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    position: relative;
    overflow: hidden;
    will-change: transform;
}

.remove-btn:hover {
    background: #c6a45a;
    color: black;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(198, 164, 90, 0.3);
}

.remove-btn:active {
    transform: translateY(0) scale(0.98);
}

/* SUMMARY */
.summary {
    background: rgba(255,255,255,0.05);
    padding: 30px;
    border-radius: 20px;
}

.summary h3 {
    color: #c6a45a;
}

.summary p {
    display: flex;
    justify-content: space-between;
}

/* 🔥 COUPON ROW */
.coupon-box {
    display: flex;
    width: 100%;
    border: 1px solid #333;
    border-radius: 12px;
    overflow: hidden;
    margin-top: 15px;
}

.coupon-input {
    flex: 1;
    padding: 12px;
    border: none;
    background: transparent;
    color: white;
}

.coupon-input:focus {
    outline: none;
}

.coupon-btn {
    padding: 0 20px;
    border: none;
    background: linear-gradient(135deg, #c6a45a, #d4af37);
    color: black;
    font-weight: bold;
    cursor: pointer;
}

.coupon-btn:hover {
    opacity: 0.9;
}

/* INPUT GROUP (FIX SPACING) */
.form-group {
    margin-top: 18px;
}

.input-field {
    width: 92%;
    padding: 12px;
    background: transparent;
    border: 1px solid #333;
    border-radius: 8px;
    color: white;
}

.input-field:focus {
    border-color: #c6a45a;
    outline: none;
}

/* PAYMENT */
.payment-option {
    border: 1px solid #333;
    padding: 16px;
    border-radius: 12px;
    margin-top: 12px;
    cursor: pointer;
}

.payment-option:hover {
    border-color: #c6a45a;
}

.payment-option.active {
    border-color: #c6a45a;
    background: rgba(198,164,90,0.1);
}

.payment-title {
    color: #c6a45a;
    margin: 24px 0 12px;
    font-weight: 500;
}

.payment-dropdown {
    position: relative;
    margin-top: 12px;
}

.payment-button {
    width: 100%;
    padding: 14px 16px;
    border: 1px solid rgba(198,164,90,0.5);
    border-radius: 8px;
    background: #070707;
    color: white;
    font: inherit;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    text-align: left;
    transition: 0.3s;
}

.payment-button.selected,
.payment-button.open {
    background: #c6a45a;
    color: black;
    border-color: #c6a45a;
    border-radius: 8px 8px 0 0;
}

.payment-menu {
    position: absolute;
    top: 100%;
    left: 0;
    width: 100%;
    z-index: 30;
    background: #050505;
    border: 1px solid rgba(198,164,90,0.55);
    border-top: 0;
    border-radius: 0 0 8px 8px;
    overflow: hidden;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-6px);
    transition: 0.25s;
}

.payment-menu.open {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.payment-choice {
    padding: 14px 16px;
    border-top: 1px solid rgba(255,255,255,0.08);
    cursor: pointer;
    transition: 0.25s;
}

.payment-choice:hover,
.payment-choice.active {
    background: #c6a45a;
    color: black;
}

.offer-note {
    color: #888;
    font-size: 12px;
    margin-top: 8px;
    line-height: 1.6;
}

input:-webkit-autofill,
input:-webkit-autofill:hover,
input:-webkit-autofill:focus {
    -webkit-text-fill-color: #ffffff;
    caret-color: #ffffff;
    box-shadow: 0 0 0 1000px #050505 inset;
    -webkit-box-shadow: 0 0 0 1000px #050505 inset;
    border-color: rgba(198,164,90,0.5);
    transition: background-color 9999s ease-in-out 0s;
}

/* BUTTON */
.btn {
    width: 100%;
    padding: 14px;
    border-radius: 25px;
    border: none;
    font-weight: bold;
    cursor: pointer;
    background: linear-gradient(135deg, #c6a45a, #d4af37);
    color: black;
    margin-top: 30px;
    transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    position: relative;
    overflow: hidden;
    will-change: transform;
    box-shadow: 0 4px 15px rgba(198, 164, 90, 0.2);
}

.btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 30px rgba(198, 164, 90, 0.4);
    background: linear-gradient(135deg, #d4af37, #c6a45a);
}

.btn:active {
    transform: translateY(-1px) scale(0.98);
}

.empty {
    text-align: center;
    padding: 40px;
    color: #888;
}

</style>
</head>

<body>

<!-- NAVBAR -->
<div class="navbar">
<div class="nav-container">

<div class="logo">TIME STEAL</div>

<div class="nav-links">
<a href="home.php">Home</a>
<a href="cart.php">Cart</a>

<div class="user">
<div class="avatar"><?php echo strtoupper($username[0]); ?></div>
<span><?php echo $username; ?></span>
</div>

</div>

</div>
</div>

<h1 style="text-align:center;color:#c6a45a;">Your Cart</h1>

<div class="cart-container">

<!-- LEFT -->
<div class="cart-items">

<?php
$total = 0;

$sql = "SELECT cart.*, products.name, products.price 
        FROM cart 
        JOIN products ON cart.product_id = products.id 
        WHERE cart.user_id = '$user_id'";

$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) == 0){
    echo "<div class='empty'>Your cart is empty</div>";
}

while($row = mysqli_fetch_assoc($result)) {

$subtotal = $row['price'] * $row['quantity'];
$total += $subtotal;
$product = getProductById((int) $row['product_id']);
$thumb = $product ? "images/" . $product['img'] : "images/watch_bg.jpg";
?>

<div class="cart-item">

<div class="cart-product">
<img class="cart-thumb" src="<?php echo htmlspecialchars($thumb, ENT_QUOTES, "UTF-8"); ?>" alt="<?php echo htmlspecialchars($row['name'], ENT_QUOTES, "UTF-8"); ?>">
<div>
<h3><?php echo htmlspecialchars($row['name'], ENT_QUOTES, "UTF-8"); ?></h3>
<p>&#8377;<?php echo $row['price']; ?></p>
</div>
</div>

<div class="qty">
<button onclick="updateCart(<?php echo $row['id']; ?>,'decrease')">-</button>
<span><?php echo $row['quantity']; ?></span>
<button onclick="updateCart(<?php echo $row['id']; ?>,'increase')">+</button>
</div>

<div>
<p>₹<?php echo $subtotal; ?></p>
<a class="remove-btn" href="remove.php?id=<?php echo $row['product_id']; ?>">Remove</a>
</div>

</div>

<?php } ?>

</div>

<!-- RIGHT -->
<div class="summary">

<h3>Order Summary</h3>

<p><span>Subtotal</span> <span>₹<?php echo $total; ?></span></p>

<?php $gst = $total * 0.18; ?>

<p><span>GST (18%)</span> <span>₹<?php echo $gst; ?></span></p>

<hr>

<h2>₹<?php echo $total + $gst; ?></h2>

<!-- 🔥 COUPON -->
<div class="coupon-box">
<input class="coupon-input" placeholder="Enter Coupon Code">
<button class="coupon-btn">Apply</button>
</div>

<button class="btn"><a href="checkout.php" style="color:inherit;text-decoration:none;display:block;">Proceed to Checkout</a></button>

</div>

</div>

<script>
function updateCart(id, action){
    fetch('update_cart.php',{ 
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'id='+id+'&action='+action
    }).then(()=>location.reload());
}</script>
</body>
</html>

