<?php
session_start();
include "config.php";

$user_id = $_SESSION['user_id'] ?? 0;
$username = $_SESSION['user'] ?? 'Guest';

if (!$user_id) {
    header('Location: login.php');
    exit;
}

$cartItems = [];
$total = 0;

$cartQuery = mysqli_query($conn, "SELECT cart.*, products.name, products.price 
    FROM cart JOIN products ON cart.product_id = products.id 
    WHERE cart.user_id='$user_id'");

while ($row = mysqli_fetch_assoc($cartQuery)) {
    $row['subtotal'] = $row['price'] * $row['quantity'];
    $total += $row['subtotal'];
    $cartItems[] = $row;
}

$gst = round($total * 0.18);
$grandTotal = $total + $gst;
$success = false;
$orderId = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($cartItems)) {
    $fullName = mysqli_real_escape_string($conn, $_POST['fullname'] ?? '');
    $email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    $phone = mysqli_real_escape_string($conn, $_POST['phone'] ?? '');
    $address = mysqli_real_escape_string($conn, $_POST['address'] ?? '');
    $city = mysqli_real_escape_string($conn, $_POST['city'] ?? '');
    $state = mysqli_real_escape_string($conn, $_POST['state'] ?? '');
    $pincode = mysqli_real_escape_string($conn, $_POST['pincode'] ?? '');
    $country = mysqli_real_escape_string($conn, $_POST['country'] ?? 'India');
    $paymentType = mysqli_real_escape_string($conn, $_POST['payment_type'] ?? 'Card');
    $cardOffer = mysqli_real_escape_string($conn, $_POST['card_offer'] ?? 'None');

    mysqli_query($conn, "INSERT INTO orders (user_id, total) VALUES ('$user_id', '$grandTotal')");
    $orderId = mysqli_insert_id($conn);

    foreach ($cartItems as $item) {
        mysqli_query($conn, "INSERT INTO order_items (order_id, product_id, quantity, price)
            VALUES ('$orderId', '{$item['product_id']}', '{$item['quantity']}', '{$item['price']}')");
    }

    mysqli_query($conn, "DELETE FROM cart WHERE user_id='$user_id'");
    $success = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Checkout - TimeSteal</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
body {
    margin: 0;
    background: #050505;
    color: #f4f4f4;
    font-family: 'Poppins', sans-serif;
}

header {
    width: 100%;
    padding: 24px 40px;
    background: rgba(0,0,0,0.9);
    border-bottom: 1px solid rgba(255,255,255,0.08);
}

.nav-container {
    max-width: 1300px;
    margin: auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logo {
    font-family: 'Playfair Display', serif;
    color: #c6a45a;
    font-size: 22px;
    letter-spacing: 3px;
}

.checkout-title {
    max-width: 900px;
    margin: 50px auto 40px;
    padding: 0 40px;
    text-align: center;
}

.checkout-title h1 {
    margin: 0;
    font-size: 42px;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    color: #fff;
    font-family: 'Playfair Display', serif;
    font-weight: 700;
}

.checkout-title p {
    margin: 16px 0 0;
    color: #b0b0b0;
    line-height: 1.8;
    font-size: 15px;
}

.checkout-grid {
    max-width: 1300px;
    margin: 0 auto 50px;
    display: grid;
    grid-template-columns: 2.1fr 1fr;
    gap: 28px;
    padding: 0 40px;
}

.checkout-panel {
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(198,164,90,0.15);
    border-radius: 24px;
    padding: 36px;
}

.order-summary {
    background: linear-gradient(135deg, rgba(198,164,90,0.08) 0%, rgba(255,255,255,0.02) 100%);
    border: 1px solid rgba(198,164,90,0.25);
    border-radius: 24px;
    padding: 32px;
    position: sticky;
    top: 40px;
    max-height: fit-content;
}

.checkout-section {
    margin-bottom: 28px;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    margin-bottom: 18px;
}

.section-header h2 {
    margin: 0;
    color: #fff;
    font-size: 20px;
    letter-spacing: 0.08em;
}

.section-header span {
    color: #c6a45a;
    font-size: 14px;
}

.field-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 20px 24px;
    margin-bottom: 12px;
}

.address-grid {
    margin-top: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.form-group label {
    font-size: 12px;
    color: #b0b0b0;
    font-weight: 500;
    letter-spacing: 0.03em;
    margin-bottom: 10px;
}

.input-field,
.select-field {
    width: 100%;
    padding: 14px 18px;
    border-radius: 14px;
    border: 1px solid rgba(198,164,90,0.2);
    background: rgba(255,255,255,0.05);
    color: #fff;
    font-size: 14px;
    transition: all 0.25s ease;
    box-sizing: border-box;
}

.input-field::placeholder,
.select-field option {
    color: rgba(255,255,255,0.4);
}

.input-field:focus,
.select-field:focus {
    outline: none;
    border-color: rgba(198,164,90,0.8);
}

.payment-methods {
    display: flex;
    gap: 14px;
    flex-wrap: wrap;
}

.payment-option {
    flex: 1 1 140px;
    min-width: 140px;
    padding: 16px;
    border-radius: 18px;
    border: 1px solid rgba(255,255,255,0.1);
    background: rgba(255,255,255,0.04);
    cursor: pointer;
    transition: all 0.25s ease;
    text-align: center;
}

.payment-option.active,
.payment-option:hover {
    border-color: #c6a45a;
    background: rgba(198,164,90,0.12);
}

.payment-option strong {
    display: block;
    margin-top: 8px;
    color: #fff;
}

.order-summary h3 {
    margin: 0 0 24px;
    color: #c6a45a;
    font-size: 20px;
    font-family: 'Playfair Display', serif;
    letter-spacing: 0.05em;
    padding-bottom: 16px;
    border-bottom: 2px solid rgba(198,164,90,0.3);
}

.order-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    padding: 18px 0;
    border-bottom: 1px solid rgba(255,255,255,0.06);
}

.order-item:first-child {
    padding-top: 8px;
}

.order-item:last-child {
    border-bottom: none;
    padding-bottom: 6px;
}

.order-item span {
    color: #d0d0d0;
    font-size: 14px;
    flex: 1;
}

.order-item strong {
    color: #f5d060;
    font-weight: 600;
    font-size: 14px;
    min-width: 80px;
    text-align: right;
}

.summary-total {
    margin-top: 24px;
    padding-top: 20px;
    padding: 20px 0;
    border-top: 2px solid rgba(198,164,90,0.3);
}

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 13px;
    margin-bottom: 14px;
    color: #b0b0b0;
}

.summary-row span:last-child {
    color: #d0d0d0;
    text-align: right;
}

.summary-row strong {
    color: #fff;
    font-weight: 600;
}

.summary-row strong:last-child {
    color: #f5d060;
    font-size: 16px;
}

.place-order-btn {
    width: 100%;
    border: none;
    border-radius: 28px;
    padding: 16px 0;
    background: linear-gradient(135deg, #c6a45a, #f5d060);
    color: #111;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    margin-top: 20px;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.place-order-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 18px 35px rgba(198,164,90,0.25);
}

.coupon-dropdown-wrapper {
    position: relative;
}

.coupon-dropdown-btn {
    width: 100%;
    padding: 14px 16px;
    border-radius: 14px;
    border: 1px solid rgba(255,255,255,0.12);
    background: rgba(255,255,255,0.04);
    color: #fff;
    font-size: 14px;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: all 0.25s ease;
}

.coupon-dropdown-btn:hover,
.coupon-dropdown-btn.open {
    border-color: #c6a45a;
    background: rgba(198,164,90,0.12);
}

.dropdown-arrow {
    font-size: 12px;
    transition: transform 0.25s ease;
}

.coupon-dropdown-btn.open .dropdown-arrow {
    transform: rotate(180deg);
}

.coupon-dropdown-menu {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: rgba(10,10,10,0.98);
    border: 1px solid rgba(198,164,90,0.4);
    border-radius: 14px;
    margin-top: 8px;
    max-height: 0;
    overflow: hidden;
    opacity: 0;
    transition: all 0.3s ease;
    z-index: 1000;
    box-shadow: 0 12px 40px rgba(0,0,0,0.6);
}

.coupon-dropdown-menu.open {
    max-height: 400px;
    opacity: 1;
}

.coupon-item {
    padding: 14px 16px;
    border-bottom: 1px solid rgba(255,255,255,0.06);
    cursor: pointer;
    transition: all 0.2s ease;
}

.coupon-item:last-child {
    border-bottom: none;
}

.coupon-item:hover {
    background: rgba(198,164,90,0.15);
    padding-left: 20px;
}

.coupon-name {
    color: #c6a45a;
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 4px;
}

.coupon-desc {
    color: #999;
    font-size: 12px;
    line-height: 1.4;
}

.notice {
    color: #a0a0a0;
    font-size: 12px;
    margin-top: 14px;
    line-height: 1.6;
    padding: 12px 14px;
    background: rgba(198,164,90,0.08);
    border-left: 3px solid rgba(198,164,90,0.4);
    border-radius: 6px;
}

.success-panel {
    max-width: 700px;
    margin: 80px auto;
    padding: 50px;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 28px;
    text-align: center;
}

.success-panel h1 {
    margin: 0 0 20px;
    color: #c6a45a;
    font-size: 38px;
}

.success-panel p {
    color: #ccc;
    line-height: 1.8;
    margin: 12px 0;
}

.success-panel a {
    display: inline-block;
    margin-top: 24px;
    padding: 14px 28px;
    border-radius: 28px;
    background: #c6a45a;
    color: #111;
    text-decoration: none;
    font-weight: 600;
}

@media (max-width: 980px) {
    .checkout-grid {
        grid-template-columns: 1fr;
    }
    
    .order-summary {
        position: relative;
        top: auto;
        max-height: none;
    }
}

@media (max-width: 680px) {
    .nav-container,
    .checkout-title,
    .checkout-grid {
        padding: 0 20px;
    }

    .field-grid {
        grid-template-columns: 1fr;
        gap: 18px 20px;
    }
    
    .checkout-panel {
        padding: 24px;
    }
    
    .checkout-title h1 {
        font-size: 32px;
    }
}

</style>
</head>
<body>
<header>
    <div class="nav-container">
        <div class="logo">TimeSteal</div>
        <div></div>
    </div>
</header>

<?php if ($success): ?>
    <section class="success-panel">
        <h1>Order Confirmed</h1>
        <p>Your order <strong>#<?php echo htmlspecialchars($orderId); ?></strong> has been placed successfully.</p>
        <p>Our luxury concierge team is preparing your package and will update you shortly via email.</p>
        <a href="home.php">Return to Home</a>
    </section>
<?php else: ?>

<section class="checkout-title">
    <h1>Checkout</h1>
    <p>Complete your order with premium delivery and secure payment. Enter your shipping details, choose your payment preference, and place your order with confidence.</p>
</section>

<div class="checkout-grid">
    <div class="checkout-panel">
        <div class="checkout-section">
            <div class="section-header">
                <h2>Shipping Details</h2>
                <span>Step 1 of 2</span>
            </div>
            <form action="checkout.php" method="POST">
                <div class="field-grid">
                    <div class="form-group">
                        <label for="fullname">Full Name</label>
                        <input id="fullname" name="fullname" class="input-field" type="text" placeholder="John Doe" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input id="email" name="email" class="input-field" type="email" placeholder="john@example.com" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input id="phone" name="phone" class="input-field" type="tel" placeholder="+91 98765 43210" required>
                    </div>
                    <div class="form-group">
                        <label for="state">State</label>
                        <input id="state" name="state" class="input-field" type="text" placeholder="Maharashtra" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Street Address</label>
                    <input id="address" name="address" class="input-field" type="text" placeholder="123 Luxury Street" required>
                </div>

                <div class="field-grid address-grid">
                    <div class="form-group">
                        <label for="city">City</label>
                        <input id="city" name="city" class="input-field" type="text" placeholder="Mumbai" required>
                    </div>
                    <div class="form-group">
                        <label for="pincode">Pincode</label>
                        <input id="pincode" name="pincode" class="input-field" type="text" placeholder="400001" required>
                    </div>
                </div>

                <div class="checkout-section">
                    <div class="section-header">
                        <h2>Payment Method</h2>
                        <span>Step 2 of 2</span>
                    </div>
                    <div class="payment-methods">
                        <label class="payment-option active">
                            <input type="radio" name="payment_type" value="Credit Card" checked hidden>
                            <span>Credit Card</span>
                            <strong>Secure finish</strong>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment_type" value="Debit Card" hidden>
                            <span>Debit Card</span>
                            <strong>Fast approval</strong>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment_type" value="Net Banking" hidden>
                            <span>Net Banking</span>
                            <strong>Trusted banks</strong>
                        </label>
                    </div>

                    <div class="field-grid" style="margin-top:20px;">
                        <div class="form-group">
                            <label for="coupon_code">Coupon Code</label>
                            <div class="coupon-dropdown-wrapper">
                                <button type="button" class="coupon-dropdown-btn" id="couponDropdownBtn">
                                    <span id="couponDropdownLabel">Select a coupon</span>
                                    <span class="dropdown-arrow">▼</span>
                                </button>
                                <div class="coupon-dropdown-menu" id="couponDropdownMenu">
                                    <div class="coupon-item" data-code="LUXURY2026">
                                        <div class="coupon-name">Luxury Collection</div>
                                        <div class="coupon-desc">15% off on watches over ₹50,000</div>
                                    </div>
                                    <div class="coupon-item" data-code="TIMESTEALTH">
                                        <div class="coupon-name">TimeSteal Exclusive</div>
                                        <div class="coupon-desc">₹5,000 off on orders above ₹30,000</div>
                                    </div>
                                    <div class="coupon-item" data-code="ELITE50">
                                        <div class="coupon-name">Elite Member</div>
                                        <div class="coupon-desc">₹2,000 off on all watches</div>
                                    </div>
                                    <div class="coupon-item" data-code="PREMIUM10">
                                        <div class="coupon-name">Premium Plus</div>
                                        <div class="coupon-desc">10% cashback + free shipping</div>
                                    </div>
                                    <div class="coupon-item" data-code="WELCOME100">
                                        <div class="coupon-name">Welcome Bonus</div>
                                        <div class="coupon-desc">₹1,000 off on first purchase</div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="coupon_code" name="coupon_code" value="">
                        </div>
                        <div class="form-group">
                            <label for="card_number">Card Number</label>
                            <input id="card_number" name="card_number" class="input-field" type="text" placeholder="XXXX XXXX XXXX XXXX" required>
                        </div>
                        <div class="form-group">
                            <label for="expiry">Expiry</label>
                            <input id="expiry" name="expiry" class="input-field" type="text" placeholder="MM/YY" required>
                        </div>
                        <div class="form-group">
                            <label for="cvv">CVV</label>
                            <input id="cvv" name="cvv" class="input-field" type="password" placeholder="XXX" required>
                        </div>
                    </div>
                </div>

                <p class="notice">All payments are secured with bank-grade encryption. Your luxury purchase is protected under our premium service guarantee.</p>

                <button type="submit" class="place-order-btn">Place Order</button>
            </form>
        </div>
    </div>

    <aside class="order-summary">
        <h3>Order Summary</h3>

        <?php if (empty($cartItems)): ?>
            <p class="notice">Your cart is empty. Add luxury watches to continue.</p>
        <?php else: ?>
            <?php foreach ($cartItems as $item): ?>
                <div class="order-item">
                    <span><?php echo htmlspecialchars($item['name']); ?> x<?php echo $item['quantity']; ?></span>
                    <strong>₹<?php echo number_format($item['subtotal'], 0); ?></strong>
                </div>
            <?php endforeach; ?>

            <div class="summary-total">
                <div class="summary-row"><span>Subtotal</span><span>₹<?php echo number_format($total, 0); ?></span></div>
                <div class="summary-row"><span>Luxury service fee</span><span>₹0</span></div>
                <div class="summary-row"><span>GST (18%)</span><span>₹<?php echo number_format($gst, 0); ?></span></div>
                <div class="summary-row" style="margin-top:18px;font-size:16px;"><strong>Total</strong><strong>₹<?php echo number_format($grandTotal, 0); ?></strong></div>
            </div>
        <?php endif; ?>
    </aside>
</div>

<?php endif; ?>

<script>
document.querySelectorAll('.payment-option').forEach(option => {
    option.addEventListener('click', () => {
        document.querySelectorAll('.payment-option').forEach(el => el.classList.remove('active'));
        option.classList.add('active');
        option.querySelector('input[type=radio]').checked = true;
    });
});

// Coupon dropdown functionality
const couponBtn = document.getElementById('couponDropdownBtn');
const couponMenu = document.getElementById('couponDropdownMenu');
const couponLabel = document.getElementById('couponDropdownLabel');
const couponInput = document.getElementById('coupon_code');

if (couponBtn) {
    couponBtn.addEventListener('click', () => {
        couponBtn.classList.toggle('open');
        couponMenu.classList.toggle('open');
    });

    document.querySelectorAll('.coupon-item').forEach(item => {
        item.addEventListener('click', () => {
            const code = item.getAttribute('data-code');
            const name = item.querySelector('.coupon-name').textContent;
            
            couponInput.value = code;
            couponLabel.textContent = name + ' - ' + code;
            
            couponBtn.classList.remove('open');
            couponMenu.classList.remove('open');
            
            document.querySelectorAll('.coupon-item').forEach(el => el.classList.remove('selected'));
            item.classList.add('selected');
        });
    });

    // Close menu when clicking outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.coupon-dropdown-wrapper')) {
            couponBtn.classList.remove('open');
            couponMenu.classList.remove('open');
        }
    });
}

</script>
</body>
</html>
