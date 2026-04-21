<?php
session_start();
include "config.php";
include "products_catalog.php";

// Show MySQL errors clearly
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Optional login - users can browse without logging in
$user_id = (int) ($_SESSION["user_id"] ?? 0);
$username = $_SESSION["user"] ?? "Customer";
$safe_username = htmlspecialchars($username, ENT_QUOTES, "UTF-8");
$user_initial = htmlspecialchars(strtoupper(substr($username, 0, 1)), ENT_QUOTES, "UTF-8");

// Cart count (only if logged in)
$count = 0;
if (isset($_SESSION['loggedin'])) {
    $count_q = mysqli_query($conn, "SELECT SUM(quantity) as total FROM cart WHERE user_id=$user_id");
    $count = mysqli_fetch_assoc($count_q)['total'] ?? 0;
}
$count = mysqli_fetch_assoc($count_q)['total'] ?? 0;

// Product fetch
$product_id = (int) ($_GET["id"] ?? $_POST["product_id"] ?? 0);
$product = getProductById($product_id);

if (!$product) {
    http_response_code(404);
    die("Product not found");
}

// Ensure reviews table (WITHOUT username column)
$conn->query("CREATE TABLE IF NOT EXISTS product_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    rating TINYINT NOT NULL,
    review TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// =========================
// ADD REVIEW
// =========================
$review_message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $rating = (int) ($_POST["rating"] ?? 0);
    $review = trim($_POST["review"] ?? "");

    if ($rating < 1 || $rating > 5) {
        $review_message = "Please choose a rating from 1 to 5.";
    } elseif ($review === "") {
        $review_message = "Please write a review before submitting.";
    } else {

        // ✅ FIXED INSERT (no username column)
        $stmt = $conn->prepare("
            INSERT INTO product_reviews (product_id, user_id, rating, review) 
            VALUES (?, ?, ?, ?)
        ");

        if (!$stmt) {
            die("Prepare failed (INSERT): " . $conn->error);
        }

        $stmt->bind_param("iiis", $product_id, $user_id, $rating, $review);
        $stmt->execute();
        $stmt->close();

        header("Location: product_detail.php?id=" . $product_id . "#reviews");
        exit();
    }
}

// =========================
// FETCH REVIEWS
// =========================

// ✅ FIXED SELECT with JOIN
$stmt = $conn->prepare("
    SELECT u.username, r.rating, r.review, r.created_at
    FROM product_reviews r
    JOIN users u ON r.user_id = u.id
    WHERE r.product_id = ?
    ORDER BY r.created_at DESC
");

if (!$stmt) {
    die("Prepare failed (SELECT): " . $conn->error);
}

$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$db_reviews = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// =========================
// REST OF YOUR ORIGINAL CODE (UNCHANGED)
// =========================

$sample_reviews = $product["sample_reviews"];

$all_ratings = array_merge(
    array_map(fn($review) => (int) $review["rating"], $sample_reviews),
    array_map(fn($review) => (int) $review["rating"], $db_reviews)
);

$review_count = count($all_ratings);
$average_rating = $review_count > 0 ? number_format(array_sum($all_ratings) / $review_count, 1) : "New";

$final_price = getFinalPrice($product);

$safe_name = htmlspecialchars($product["name"], ENT_QUOTES, "UTF-8");
$safe_tagline = htmlspecialchars($product["tagline"], ENT_QUOTES, "UTF-8");
$safe_description = htmlspecialchars($product["description"], ENT_QUOTES, "UTF-8");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $safe_name; ?> - TimeSteal</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box}
html{scroll-behavior:smooth}
body{margin:0;background:#050505;color:#fff;font-family:Poppins,sans-serif;padding-top:82px;overflow-x:hidden}
body::before{content:"";position:fixed;inset:0;background:radial-gradient(circle at 20% 8%,rgba(198,164,90,.18),transparent 28%),radial-gradient(circle at 85% 30%,rgba(255,255,255,.08),transparent 24%),#050505;pointer-events:none;z-index:-2}
.shine{position:fixed;inset:0;background:linear-gradient(110deg,transparent 0%,rgba(198,164,90,.06) 45%,transparent 62%);animation:pageShine 9s linear infinite;pointer-events:none;z-index:-1}
@keyframes pageShine{from{transform:translateX(-120%)}to{transform:translateX(120%)}}
.navbar{position:fixed;top:0;left:0;width:100%;background:rgba(0,0,0,.96);backdrop-filter:blur(10px);z-index:20;padding:15px 60px}
.nav-container{display:flex;align-items:center;justify-content:space-between}
.logo{color:#c6a45a;font-family:'Playfair Display',serif;font-size:22px;letter-spacing:4px}
.nav-links{display:flex;gap:25px;list-style:none;margin:0;padding:0;align-items:center}
.nav-links a{position:relative;color:#ddd;text-decoration:none;font-size:14px}
.nav-links a::after{content:"";position:absolute;left:0;bottom:-4px;width:0;height:2px;background:#c6a45a;transition:.35s}
.nav-links a:hover{color:#c6a45a}
.nav-links a:hover::after{width:100%}
.cart-icon{position:relative}
.cart-count{position:absolute;top:-9px;right:-13px;background:#c6a45a;color:#000;font-size:11px;padding:3px 7px;border-radius:50%}
.user-menu{position:relative}
.user-btn{display:flex;align-items:center;gap:10px;cursor:pointer;color:#ddd;font-size:14px}
.avatar{width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#c6a45a,#d4af37);display:flex;align-items:center;justify-content:center;color:#000;font-weight:700}
.avatar.small{width:26px;height:26px;font-size:12px}
.arrow{font-size:10px;transition:.3s}
.dropdown{position:absolute;top:45px;right:0;width:240px;background:rgba(20,20,20,.96);backdrop-filter:blur(15px);border:1px solid rgba(198,164,90,.18);border-radius:8px;padding:15px;opacity:0;visibility:hidden;transform:translateY(15px);transition:.3s}
.dropdown.show{opacity:1;visibility:visible;transform:translateY(0)}
.dropdown-header{font-size:12px;color:#888;margin-bottom:10px}
.account{display:flex;align-items:center;gap:10px;padding:8px;border-radius:8px;color:#ddd;text-decoration:none}
.account:hover,.account.active{background:rgba(198,164,90,.12)}
.dropdown-divider{height:1px;background:rgba(255,255,255,.1);margin:10px 0}
.logout{display:block;color:#ff6969;text-decoration:none;padding:8px;border-radius:6px}
.page{width:min(1180px,92%);margin:0 auto;padding:48px 0}
.back-arrow{width:46px;height:46px;border:1px solid rgba(198,164,90,.55);border-radius:50%;display:inline-grid;place-items:center;color:#c6a45a;background:rgba(255,255,255,.035);text-decoration:none;font-size:28px;line-height:0;cursor:pointer;transition:.3s;box-shadow:0 0 0 rgba(198,164,90,0);padding:0}
.back-arrow span{display:block;line-height:1;transform:translate(-1px,-1px)}
.back-arrow:hover{background:#c6a45a;color:#000;transform:translateX(-4px);box-shadow:0 0 28px rgba(198,164,90,.32)}
.product-hero{display:grid;grid-template-columns:minmax(320px,520px) 1fr;gap:54px;align-items:start;margin-top:28px}
.product-image{position:sticky;top:110px;background:#111;border:1px solid rgba(198,164,90,.26);border-radius:8px;overflow:hidden;box-shadow:0 25px 70px rgba(0,0,0,.55);animation:floatIn .8s ease both}
.product-image::after{content:"";position:absolute;inset:0;background:linear-gradient(120deg,transparent 20%,rgba(255,255,255,.16),transparent 52%);transform:translateX(-130%);animation:glassSweep 4.5s ease-in-out infinite}
.product-image img{width:100%;height:560px;object-fit:cover;display:block;transition:transform .8s ease}
.product-image:hover img{transform:scale(1.06)}
@keyframes glassSweep{0%,45%{transform:translateX(-130%)}75%,100%{transform:translateX(130%)}}
@keyframes floatIn{from{opacity:0;transform:translateY(35px)}to{opacity:1;transform:translateY(0)}}
.details{animation:floatIn .9s ease .12s both}
.badge{display:inline-block;background:#c6a45a;color:#000;padding:6px 12px;border-radius:4px;font-size:12px;font-weight:700;margin-bottom:18px}
h1{font-family:'Playfair Display',serif;letter-spacing:3px;color:#c6a45a;font-size:46px;margin:0 0 14px}
.tagline{color:#ddd;font-size:18px;margin:0 0 24px}
.rating{color:#d4af37;margin-bottom:22px}
.price{font-size:32px;color:#fff;margin:16px 0}
.old-price{color:#777;text-decoration:line-through;font-size:18px;margin-right:10px}
.detail-copy{color:#d6d6d6;line-height:1.9;margin:24px 0}
.actions{display:flex;gap:14px;flex-wrap:wrap;margin:28px 0}
.btn{display:inline-block;border:1px solid #c6a45a;color:#c6a45a;background:transparent;text-decoration:none;padding:12px 24px;border-radius:6px;cursor:pointer;font:inherit;transition:.3s;position:relative;overflow:hidden}
.btn::before{content:"";position:absolute;top:0;left:-100%;width:100%;height:100%;background:#c6a45a;transition:.4s ease;z-index:-1}
.btn:hover{color:#000;box-shadow:0 0 24px rgba(198,164,90,.35)}
.btn:hover::before{left:0}
.btn.disabled{border-color:#555;color:#777;pointer-events:none}
.info-strip{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin:30px 0}
.info-pill{background:rgba(255,255,255,.045);border:1px solid rgba(198,164,90,.18);border-radius:8px;padding:16px;animation:rise .7s ease both}
.info-pill:nth-child(2){animation-delay:.08s}.info-pill:nth-child(3){animation-delay:.16s}.info-pill:nth-child(4){animation-delay:.24s}
.info-pill span{display:block;color:#c6a45a;font-size:12px;text-transform:uppercase;letter-spacing:1px;margin-bottom:5px}
@keyframes rise{from{opacity:0;transform:translateY(22px)}to{opacity:1;transform:translateY(0)}}
.panel{border:1px solid rgba(198,164,90,.2);background:rgba(255,255,255,.035);border-radius:8px;padding:28px;margin-top:30px;transition:.35s}
.panel:hover{border-color:rgba(198,164,90,.42);box-shadow:0 18px 45px rgba(0,0,0,.28)}
.gold{color:#c6a45a}
.spec-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}
.spec{border-bottom:1px solid rgba(255,255,255,.08);padding-bottom:12px}
.spec span{display:block;color:#888;font-size:12px;text-transform:uppercase;letter-spacing:1px;margin-bottom:4px}
.feature-list{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;margin:0;padding:0;list-style:none}
.feature-list li{border:1px solid rgba(255,255,255,.08);border-radius:8px;padding:14px;background:rgba(0,0,0,.2)}
.reviews-head{display:flex;justify-content:space-between;gap:18px;align-items:center}
.review{border-top:1px solid rgba(255,255,255,.08);padding:20px 0;animation:rise .55s ease both}
.review:first-of-type{border-top:0}
.review strong{color:#fff}
.review-date{color:#777;font-size:12px}
.stars{color:#d4af37;letter-spacing:2px}
.review-form label{display:block;margin-bottom:12px}
.rating-dropdown{position:relative;margin-bottom:12px}
.rating-button{width:100%;display:flex;align-items:center;justify-content:space-between;background:#050505;color:#fff;border:1px solid rgba(198,164,90,.55);border-radius:8px;padding:14px 20px;font:inherit;cursor:pointer;text-align:left;transition:.25s}
.rating-button.selected,.rating-button[aria-expanded="true"]{background:#c6a45a;color:#000;border-color:#c6a45a;border-radius:8px 8px 0 0}
.rating-menu{position:absolute;top:100%;left:0;width:100%;background:#050505;border:1px solid rgba(198,164,90,.55);border-top:0;border-radius:0 0 8px 8px;overflow:hidden;opacity:0;visibility:hidden;transform:translateY(-6px);transition:.25s;z-index:5}
.rating-menu.open{opacity:1;visibility:visible;transform:translateY(0)}
.rating-option{padding:14px 20px;color:#fff;border-top:1px solid rgba(255,255,255,.08);cursor:pointer;transition:.25s}
.rating-option:hover,.rating-option.active{background:#c6a45a;color:#000}
.dropdown-arrow{font-size:11px}
textarea{width:100%;background:#0b0b0b;border:1px solid rgba(198,164,90,.35);color:#fff;border-radius:6px;padding:12px;font:inherit}
textarea{min-height:120px;resize:vertical;margin:12px 0}
input:-webkit-autofill,input:-webkit-autofill:hover,input:-webkit-autofill:focus,textarea:-webkit-autofill,textarea:-webkit-autofill:hover,textarea:-webkit-autofill:focus{-webkit-text-fill-color:#fff;caret-color:#fff;box-shadow:0 0 0 1000px #050505 inset;-webkit-box-shadow:0 0 0 1000px #050505 inset;border-color:rgba(198,164,90,.5);transition:background-color 9999s ease-in-out 0s}
.form-message{color:#ffb3b3}
.cart-toast{position:fixed;top:96px;right:24px;background:#c6a45a;color:#000;padding:13px 22px;border-radius:8px;font-weight:600;z-index:50;opacity:0;transform:translateY(-14px);pointer-events:none;transition:.3s;box-shadow:0 14px 35px rgba(0,0,0,.35)}
.cart-toast.show{opacity:1;transform:translateY(0)}
@media(max-width:920px){.product-hero{grid-template-columns:1fr}.product-image{position:relative;top:0}.product-image img{height:430px}.info-strip{grid-template-columns:repeat(2,1fr)}.navbar{padding:16px 22px}.nav-links{gap:14px;flex-wrap:wrap}.spec-grid,.feature-list{grid-template-columns:1fr}}
@media(max-width:540px){.info-strip{grid-template-columns:1fr}h1{font-size:34px}.product-image img{height:340px}.user-btn span:not(.arrow){display:none}}
</style>
</head>
<body>
<div class="shine"></div>
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
                <span class="cart-count"><?php echo (int) $count; ?></span>
            </li>
            <li class="user-menu">
                <div class="user-btn" onclick="toggleDropdown()">
                    <div class="avatar"><?php echo $user_initial; ?></div>
                    <span><?php echo $safe_username; ?></span>
                    <span class="arrow">▼</span>
                </div>
                <div id="dropdown" class="dropdown">
                    <div class="dropdown-header">Accounts</div>
                    <div class="account active">
                        <div class="avatar small"><?php echo $user_initial; ?></div>
                        <span><?php echo $safe_username; ?></span>
                    </div>
                    <?php
                    $users = $conn->query("SELECT DISTINCT username FROM users ORDER BY username");
                    while ($u = $users->fetch_assoc()) {
                        $name = $u["username"];
                        if ($name === $username) continue;
                    ?>
                    <a href="switch_user.php?user=<?php echo urlencode($name); ?>" class="account">
                        <div class="avatar small"><?php echo htmlspecialchars(strtoupper(substr($name, 0, 1)), ENT_QUOTES, "UTF-8"); ?></div>
                        <span><?php echo htmlspecialchars($name, ENT_QUOTES, "UTF-8"); ?></span>
                    </a>
                    <?php } ?>
                    <div class="dropdown-divider"></div>
                    <a href="register.php" class="account"><span>+ Add Account</span></a>
                    <a href="logout.php" class="logout">Logout</a>
                </div>
            </li>
        </ul>
    </div>
</header>

<main class="page">
    <button class="back-arrow" type="button" onclick="goBack()" aria-label="Return to previous page"><span>‹</span></button>

    <section class="product-hero">
        <div class="product-image">
            <img src="images/<?php echo htmlspecialchars($product["img"], ENT_QUOTES, "UTF-8"); ?>" alt="<?php echo $safe_name; ?>">
        </div>

        <div class="details">
            <?php if ((int) $product["discount"] > 0) { ?>
                <span class="badge"><?php echo (int) $product["discount"]; ?>% off</span>
            <?php } ?>
            <h1><?php echo $safe_name; ?></h1>
            <p class="tagline"><?php echo $safe_tagline; ?></p>
            <div class="rating">
                <?php echo $average_rating === "New" ? "No ratings yet" : $average_rating . " / 5"; ?>
                · <?php echo $review_count; ?> customer review<?php echo $review_count === 1 ? "" : "s"; ?>
            </div>
            <div class="price">
                <?php if ((int) $product["discount"] > 0) { ?>
                    <span class="old-price">₹<?php echo (int) $product["price"]; ?></span>
                <?php } ?>
                ₹<?php echo $final_price; ?>
            </div>
            <p class="detail-copy"><?php echo $safe_description; ?></p>

            <div class="actions">
                <?php if ((int) $product["stock"] > 0) { ?>
                    <button class="btn primary" type="button" onclick="addProductToCart(<?php echo $product_id; ?>)">Add to Cart</button>
                <?php } else { ?>
                    <span class="btn disabled">Out of Stock</span>
                <?php } ?>
                <a class="btn" href="#reviews">Read Reviews</a>
            </div>

            <div class="info-strip">
                <div class="info-pill"><span>SKU</span><?php echo htmlspecialchars($product["sku"], ENT_QUOTES, "UTF-8"); ?></div>
                <div class="info-pill"><span>Delivery</span><?php echo htmlspecialchars($product["delivery"], ENT_QUOTES, "UTF-8"); ?></div>
                <div class="info-pill"><span>Returns</span><?php echo htmlspecialchars($product["returns"], ENT_QUOTES, "UTF-8"); ?></div>
                <div class="info-pill"><span>Availability</span><?php echo htmlspecialchars($product["availability"], ENT_QUOTES, "UTF-8"); ?></div>
            </div>

            <div class="panel">
                <h2 class="gold">Product Details</h2>
                <div class="spec-grid">
                    <div class="spec"><span>Brand</span><?php echo htmlspecialchars($product["brand"], ENT_QUOTES, "UTF-8"); ?></div>
                    <div class="spec"><span>Collection</span><?php echo htmlspecialchars($product["collection_rank"], ENT_QUOTES, "UTF-8"); ?></div>
                    <div class="spec"><span>Movement</span><?php echo htmlspecialchars($product["movement"], ENT_QUOTES, "UTF-8"); ?></div>
                    <div class="spec"><span>Dial</span><?php echo htmlspecialchars($product["dial"], ENT_QUOTES, "UTF-8"); ?></div>
                    <div class="spec"><span>Case</span><?php echo htmlspecialchars($product["case"], ENT_QUOTES, "UTF-8"); ?></div>
                    <div class="spec"><span>Strap</span><?php echo htmlspecialchars($product["strap"], ENT_QUOTES, "UTF-8"); ?></div>
                    <div class="spec"><span>Glass</span><?php echo htmlspecialchars($product["glass"], ENT_QUOTES, "UTF-8"); ?></div>
                    <div class="spec"><span>Dimensions</span><?php echo htmlspecialchars($product["dimensions"], ENT_QUOTES, "UTF-8"); ?></div>
                    <div class="spec"><span>Weight</span><?php echo htmlspecialchars($product["weight"], ENT_QUOTES, "UTF-8"); ?></div>
                    <div class="spec"><span>Clasp</span><?php echo htmlspecialchars($product["clasp"], ENT_QUOTES, "UTF-8"); ?></div>
                    <div class="spec"><span>Water Resistance</span><?php echo htmlspecialchars($product["water"], ENT_QUOTES, "UTF-8"); ?></div>
                    <div class="spec"><span>Warranty</span><?php echo htmlspecialchars($product["warranty"], ENT_QUOTES, "UTF-8"); ?></div>
                </div>
            </div>
        </div>
    </section>

    <section class="panel">
        <h2 class="gold">Why Customers Pick It</h2>
        <ul class="feature-list">
            <?php foreach ($product["highlights"] as $highlight) { ?>
                <li><?php echo htmlspecialchars($highlight, ENT_QUOTES, "UTF-8"); ?></li>
            <?php } ?>
            <li><?php echo htmlspecialchars($product["package"], ENT_QUOTES, "UTF-8"); ?></li>
        </ul>
    </section>

    <section class="panel" id="reviews">
        <div class="reviews-head">
            <h2 class="gold">Customer Reviews</h2>
            <span><?php echo $average_rating === "New" ? "Be the first to review" : $average_rating . " average rating"; ?></span>
        </div>

        <form method="POST" class="review-form">
            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
            <input type="hidden" name="rating" id="ratingInput" required>
            <label for="rating">Your Rating</label>
            <div class="rating-dropdown" id="ratingDropdown">
                <button class="rating-button" id="ratingButton" type="button">
                    <span id="ratingLabel">Choose rating</span>
                    <span class="dropdown-arrow">▼</span>
                </button>
                <div class="rating-menu" id="ratingMenu">
                    <div class="rating-option" data-value="5">5 - Excellent</div>
                    <div class="rating-option" data-value="4">4 - Very good</div>
                    <div class="rating-option" data-value="3">3 - Good</div>
                    <div class="rating-option" data-value="2">2 - Fair</div>
                    <div class="rating-option" data-value="1">1 - Poor</div>
                </div>
            </div>
            <textarea name="review" placeholder="Share your experience with this watch..." required></textarea>
            <?php if ($review_message !== "") { ?>
                <p class="form-message"><?php echo htmlspecialchars($review_message, ENT_QUOTES, "UTF-8"); ?></p>
            <?php } ?>
            <button class="btn primary" type="submit">Add Review</button>
        </form>

        <?php foreach ($db_reviews as $review) { ?>
            <div class="review">
                <strong><?php echo htmlspecialchars($review["username"], ENT_QUOTES, "UTF-8"); ?></strong>
                <div class="stars"><?php echo str_repeat("★", (int) $review["rating"]); ?><?php echo str_repeat("☆", 5 - (int) $review["rating"]); ?></div>
                <p><?php echo nl2br(htmlspecialchars($review["review"], ENT_QUOTES, "UTF-8")); ?></p>
                <div class="review-date"><?php echo date("d M Y", strtotime($review["created_at"])); ?></div>
            </div>
        <?php } ?>

        <?php foreach ($sample_reviews as $review) { ?>
            <div class="review">
                <strong><?php echo htmlspecialchars($review["name"], ENT_QUOTES, "UTF-8"); ?></strong>
                <div class="stars"><?php echo str_repeat("★", (int) $review["rating"]); ?><?php echo str_repeat("☆", 5 - (int) $review["rating"]); ?></div>
                <p><?php echo htmlspecialchars($review["text"], ENT_QUOTES, "UTF-8"); ?></p>
                <div class="review-date"><?php echo htmlspecialchars($review["date"], ENT_QUOTES, "UTF-8"); ?> · Verified purchase</div>
            </div>
        <?php } ?>
    </section>
</main>

<script>
function showCartToast(message) {
    let toast = document.getElementById("cartToast");
    if (!toast) {
        toast = document.createElement("div");
        toast.id = "cartToast";
        toast.className = "cart-toast";
        document.body.appendChild(toast);
    }

    toast.textContent = message;
    toast.classList.add("show");
    setTimeout(() => toast.classList.remove("show"), 2400);
}

function addProductToCart(productId) {
    fetch(`add_to_cart.php?id=${productId}&ajax=1`, { credentials: "same-origin" })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showCartToast(data.message || "Added to cart");
                const count = document.querySelector(".cart-count");
                if (count && data.cartCount !== undefined) {
                    count.textContent = data.cartCount;
                }
                return;
            }

            showCartToast(data.message || "Could not add to cart");
        })
        .catch(() => showCartToast("Could not add to cart"));
}

function goBack() {
    if (window.history.length > 1) {
        window.history.back();
        return;
    }

    window.location.href = "home.php#collection";
}

const ratingButton = document.getElementById("ratingButton");
const ratingMenu = document.getElementById("ratingMenu");
const ratingInput = document.getElementById("ratingInput");
const ratingLabel = document.getElementById("ratingLabel");

ratingButton.addEventListener("click", () => {
    ratingMenu.classList.toggle("open");
    ratingButton.setAttribute("aria-expanded", ratingMenu.classList.contains("open") ? "true" : "false");
});

document.querySelectorAll(".rating-option").forEach(option => {
    option.addEventListener("click", () => {
        ratingInput.value = option.dataset.value;
        ratingLabel.textContent = option.textContent;
        ratingButton.classList.add("selected");
        ratingButton.setAttribute("aria-expanded", "false");
        document.querySelectorAll(".rating-option").forEach(item => item.classList.remove("active"));
        option.classList.add("active");
        ratingMenu.classList.remove("open");
    });
});

document.addEventListener("click", event => {
    if (!event.target.closest("#ratingDropdown")) {
        ratingMenu.classList.remove("open");
        ratingButton.setAttribute("aria-expanded", "false");
    }
});

function toggleDropdown() {
    document.getElementById("dropdown").classList.toggle("show");
}
window.onclick = function(e) {
    if (!e.target.closest('.user-menu')) {
        const dropdown = document.getElementById("dropdown");
        if (dropdown) dropdown.classList.remove("show");
    }
}
</script>
</body>
</html>
