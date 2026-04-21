<?php
session_start();
include "config.php";

// 🔢 CART COUNT (only for logged in users)
$count = 0;
if (isset($_SESSION['loggedin'])) {
    $user_id = $_SESSION['user_id'];
    $count_q = mysqli_query($conn, "SELECT SUM(quantity) as total FROM cart WHERE user_id='$user_id'");
    $count = mysqli_fetch_assoc($count_q)['total'] ?? 0;
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TIME STEAL</title>

<link rel="stylesheet" href="styles.css?v=150">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display&family=Poppins&display=swap" rel="stylesheet">

<style>

    .btn-cart.disabled {
    opacity: 0.4;
    pointer-events: none;
    border: 1px solid #444;
}

/* ===== PREMIUM SEARCH & SORT STYLING ===== */
.search-sort-bar {
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
    gap: 25px !important;
    margin: 50px auto !important;
    max-width: 900px !important;
    flex-wrap: wrap !important;
    padding: 0 20px !important;
    text-align: center !important;
    width: 100% !important;
    box-sizing: border-box !important;
}

.search-container {
    position: relative !important;
    flex: 1 !important;
    min-width: 280px !important;
    max-width: 500px !important;
}

.search-input {
    width: 100% !important;
    padding: 15px 45px 15px 18px !important;
    background: rgba(255, 255, 255, 0.03) !important;
    border: 1.5px solid rgba(198, 164, 90, 0.5) !important;
    border-radius: 16px !important;
    color: #e8e8e8 !important;
    font-size: 14px !important;
    font-weight: 300 !important;
    letter-spacing: 0.5px !important;
    outline: none !important;
    font-family: 'Poppins', sans-serif !important;
    transition: all 0.35s ease !important;
    box-sizing: border-box !important;
    box-shadow: inset 0 0 0 1px rgba(255,255,255,0.04), 0 10px 30px rgba(0,0,0,0.25) !important;
}

.search-input::placeholder {
    color: rgba(200, 200, 200, 0.5) !important;
    font-weight: 300 !important;
}

.search-input:focus {
    background: rgba(255, 255, 255, 0.08) !important;
    border-color: #c6a45a !important;
    box-shadow: 0 0 25px rgba(198, 164, 90, 0.3), inset 0 0 8px rgba(198, 164, 90, 0.08) !important;
    color: #ffffff !important;
}

.search-input:hover {
    border-color: rgba(198, 164, 90, 0.7) !important;
    background: rgba(255, 255, 255, 0.05) !important;
}

.search-icon {
    position: absolute !important;
    right: 16px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    color: #c6a45a !important;
    font-size: 16px !important;
    pointer-events: none !important;
    opacity: 0.7 !important;
    transition: opacity 0.3s !important;
}

.search-input:focus ~ .search-icon {
    opacity: 1 !important;
}

.sort-dropdown {
    position: relative !important;
    min-width: 220px !important;
}

.sort-button {
    width: 100% !important;
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    padding: 15px 18px !important;
    background: rgba(255, 255, 255, 0.03) !important;
    border: 1.5px solid rgba(198, 164, 90, 0.5) !important;
    border-radius: 16px !important;
    color: #e8e8e8 !important;
    font-size: 14px !important;
    font-weight: 300 !important;
    letter-spacing: 0.5px !important;
    cursor: pointer !important;
    outline: none !important;
    font-family: 'Poppins', sans-serif !important;
    transition: all 0.35s ease !important;
    box-shadow: inset 0 0 0 1px rgba(255,255,255,0.04), 0 10px 30px rgba(0,0,0,0.25) !important;
    text-align: left !important;
}

.sort-button:hover {
    border-color: rgba(198, 164, 90, 0.7) !important;
    background-color: rgba(255, 255, 255, 0.05) !important;
}

.sort-button .dropdown-arrow {
    margin-left: 12px !important;
    color: #c6a45a !important;
}

.sort-options {
    position: absolute !important;
    top: calc(100% + 12px) !important;
    left: 0 !important;
    width: 100% !important;
    background: #0a0a0a !important;
    border: 1px solid rgba(198, 164, 90, 0.4) !important;
    border-radius: 16px !important;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.35) !important;
    overflow: hidden !important;
    opacity: 0 !important;
    visibility: hidden !important;
    transform: translateY(-10px) !important;
    transition: all 0.25s ease !important;
    z-index: 20 !important;
}

.sort-options.open {
    opacity: 1 !important;
    visibility: visible !important;
    transform: translateY(0) !important;
}

.sort-option {
    padding: 10px 18px !important;
    color: #e8e8e8 !important;
    cursor: pointer !important;
    font-weight: 300 !important;
    transition: background 0.25s ease, color 0.25s ease !important;
    font-size: 13px !important;
}

.sort-option:not(:last-child) {
    border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
}

.sort-option:hover,
.sort-option.active {
    background: #c6a45a !important;
    color: #111 !important;
}

.sort-option.active {
    font-weight: 500 !important;
}

/* ===== TOAST ===== */
.toast {
    position: fixed;
    top: 96px;
    right: 20px;
    background: #c6a45a;
    color: black;
    padding: 12px 20px;
    border-radius: 30px;
    z-index: 9999;

    opacity: 0;
    transform: translateY(-20px);
    animation: slideIn 0.5s forwards, fadeOut 0.5s forwards 2.5s;
}

@keyframes slideIn { to { opacity:1; transform:translateY(0);} }
@keyframes fadeOut { to { opacity:0;} }

/* ===== ORIGINAL NAVBAR STYLE ===== */
.navbar {
    width: 100%;
    background: #000;
    padding: 18px 60px;
}

.nav-container {
    display: flex;
    align-items: center;
    width: 93%;
}

/* Push menu to right */
.nav-links {
    display: flex;
    gap: 25px;
    list-style: none;
    align-items: center;

    margin-left: auto; /* 🔥 THIS FIXES ALIGNMENT */
}

.logo {
    margin-right: 40px;
}

.logo {
    color: #c6a45a;
    font-family: 'Playfair Display', serif;
    font-size: 22px;
    letter-spacing: 3px;
}

.nav-links {
    display: flex;
    gap: 25px;
    list-style: none;
    align-items: center;
}

.nav-links a {
    position: relative;
    color: #ddd;
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

/* CART BADGE */
.cart-icon {
    position: relative;
}

.cart-count {
    position: absolute;
    top: -8px;
    right: -12px;
    background: #c6a45a;
    color: black;
    font-size: 11px;
    padding: 3px 7px;
    border-radius: 50%;
}

.username {
    color: #aaa;
    font-size: 14px;
}


/* ===== USER MENU ===== */
.user-menu {
    position: relative;
}

/* BUTTON */
.user-btn {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    color: #ddd;
    font-size: 14px;
}

/* AVATAR */
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
}

.avatar.small {
    width: 26px;
    height: 26px;
    font-size: 12px;
}

/* ARROW */
.arrow {
    font-size: 10px;
    transition: 0.3s;
}

/* ===== DROPDOWN ===== */
.dropdown {
    position: absolute;
    top: 45px;
    right: 0;
    width: 240px;

    background: rgba(20,20,20,0.95);
    backdrop-filter: blur(15px);

    border-radius: 12px;
    padding: 15px;

    opacity: 0;
    visibility: hidden;
    transform: translateY(15px);

    transition: 0.3s ease;
}

/* SHOW */
.dropdown.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

/* HEADER */
.dropdown-header {
    font-size: 12px;
    color: #888;
    margin-bottom: 10px;
}

/* ACCOUNT ITEM */
.account {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px;
    border-radius: 8px;
    cursor: pointer;
    transition: 0.3s;
}

.account:hover {
    background: rgba(255,255,255,0.05);
}

/* ACTIVE ACCOUNT */
.account.active {
    background: rgba(198,164,90,0.15);
}

/* DIVIDER */
.dropdown-divider {
    height: 1px;
    background: rgba(255,255,255,0.1);
    margin: 10px 0;
}

/* LINKS */
.dropdown a {
    display: block;
    padding: 8px;
    color: #ddd;
    text-decoration: none;
    border-radius: 8px;
    transition: 0.3s;
}

.dropdown a.account {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px;
}

.dropdown a:hover {
    background: rgba(255,255,255,0.05);
    color: #c6a45a;
}

/* LOGOUT */
.logout {
    color: #ff4d4d;
}

/* ===== PREMIUM WATCH CARD STYLING ===== */
.watches {
    display: grid !important;
    grid-template-columns: repeat(auto-fill, 280px) !important;
    justify-content: center !important;
    gap: 30px !important;
    margin-top: 50px !important;
}

.watch-card {
    position: relative !important;
    width: 280px !important;
    min-height: 430px !important;
    background: linear-gradient(180deg, rgba(255,255,255,0.065), rgba(255,255,255,0.02)) !important;
    border: 1px solid rgba(198,164,90,0.2) !important;
    border-radius: 8px !important;
    overflow: hidden !important;
    padding-bottom: 20px !important;
    transition: transform 0.4s ease, box-shadow 0.4s ease, border 0.4s ease !important;
    cursor: pointer !important;
    box-shadow: 0 22px 55px rgba(0,0,0,0.32) !important;
}

.watch-card:focus {
    outline: 2px solid #c6a45a !important;
    outline-offset: 4px !important;
}

.watch-card:hover {
    transform: translateY(-8px) scale(1.02) !important;
    box-shadow: 0 0 18px rgba(198,164,90,0.46), 0 26px 60px rgba(0,0,0,0.65) !important;
    border: 2px solid #c6a45a !important;
}

.watch-card img {
    width: 100% !important;
    height: 260px !important;
    object-fit: cover !important;
    display: block !important;
}

.watch-card h3 {
    padding: 15px !important;
    margin: 0 !important;
    color: #ffffff !important;
    position: relative !important;
    z-index: 2 !important;
}

.price {
    color: #c6a45a !important;
    padding-left: 15px !important;
    position: relative !important;
    z-index: 2 !important;
}

.btn-cart {
    margin-left: 15px !important;
    border: 1px solid #c6a45a !important;
    background: transparent !important;
    color: #c6a45a !important;
    padding: 10px 18px !important;
    border-radius: 20px !important;
    cursor: pointer !important;
    position: relative !important;
    overflow: hidden !important;
    z-index: 3 !important;
    transition: color 0.4s ease !important;
}

.btn-cart::before {
    content: "" !important;
    position: absolute !important;
    top: 0 !important;
    left: -100% !important;
    width: 100% !important;
    height: 100% !important;
    background: #c6a45a !important;
    transition: left 0.4s ease !important;
    z-index: -1 !important;
}

.btn-cart:hover {
    color: black !important;
}

.btn-cart:hover::before {
    left: 0 !important;
}

    /* ===== SITE FOOTER ===== */
    .site-footer {
        background: #050505;
        color: #f4f4f4;
        padding: 38px 0 18px;
        border-top: 1px solid rgba(255,255,255,0.08);
    }

    .footer-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(140px, 1fr));
        gap: 18px;
        padding: 0 20px;
        align-items: flex-start;
    }
    .footer-section {
        margin-bottom: 18px;
    }
    .footer-section h3 {
        font-size: 13px;
        letter-spacing: 2px;
        margin-bottom: 14px;
        color: #fff;
        text-transform: uppercase;
    }

    .footer-column {
        text-align: center;
    }

    .footer-column h3 {
        font-size: 13px;
        letter-spacing: 2px;
        margin-bottom: 14px;
        color: #fff;
        text-transform: uppercase;
    }

    .footer-column a,
    .footer-column p {
        display: block;
        color: #c9c9c9;
        text-decoration: none;
        margin: 0 auto 10px;
        font-size: 13px;
        line-height: 1.6;
        position: relative;
        transition: color 0.25s ease;
    }

    .footer-column:not(.footer-side) a {
        display: block;
        width: max-content;
    }

    .footer-column:not(.footer-side) a::after {
        content: "";
        position: absolute;
        left: 0;
        bottom: -2px;
        width: 100%;
        height: 1px;
        background: #ffffff;
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.25s ease;
    }

    .footer-column:not(.footer-side) a:hover {
        color: #c6a45a;
    }

    .footer-column:not(.footer-side) a:hover::after {
        transform: scaleX(1);
    }

    .footer-contact {
        margin-bottom: 12px;
        color: #fff;
        font-weight: 500;
    }

    .footer-side {
        display: flex;
        flex-direction: column;
        gap: 10px;
        align-items: center;
    }

    .app-buttons {
        display: grid;
        grid-template-columns: repeat(2, minmax(150px, auto));
        justify-content: center;
        gap: 12px;
        margin-bottom: 12px;
        width: max-content;
    }

    .app-button {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 14px;
        min-height: 52px;
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 18px;
        color: #fff;
        text-decoration: none;
        transition: transform 0.25s ease, border-color 0.25s ease, background 0.25s ease;
    }

    .app-button:hover {
        transform: translateY(-2px);
        border-color: rgba(255,255,255,0.22);
        background: rgba(255,255,255,0.08);
    }

    .app-icon {
        width: 36px;
        height: 36px;
        display: grid;
        place-items: center;
        background: rgba(255,255,255,0.12);
        border-radius: 12px;
        color: #fff;
    }

    .app-icon svg {
        width: 18px;
        height: 18px;
        color: #fff;
    }

    .app-copy {
        display: flex;
        flex-direction: column;
        line-height: 1.2;
        font-size: 12px;
    }

    .app-copy strong {
        font-size: 14px;
        color: #c6a45a;
    }

    .footer-social {
        margin-bottom: 10px;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .footer-social span {
        display: block;
        margin-bottom: 12px;
        color: #fff;
        font-weight: 700;
        font-size: 14px;
        letter-spacing: 0.04em;
    }

    .social-links {
        display: flex;
        gap: 18px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .social-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 8px;
        background: transparent;
        border-radius: 0;
        transition: transform 0.25s ease, background 0.25s ease, box-shadow 0.25s ease;
    }

    .social-icon:hover {
        transform: translateY(-2px);
        background: rgba(255,255,255,0.08);
        box-shadow: 0 10px 30px rgba(0,0,0,0.14);
    }

    .social-icon svg {
        width: 26px;
        height: 26px;
        display: block;
    }

    .social-icon.facebook,
    .social-icon.instagram,
    .social-icon.x,
    .social-icon.youtube {
        background: rgba(255,255,255,0.08);
        color: #fff;
    }

    .social-icon.facebook:hover,
    .social-icon.instagram:hover,
    .social-icon.x:hover,
    .social-icon.youtube:hover {
        background: rgba(255,255,255,0.16);
    }

    .footer-help p {
        margin-bottom: 10px;
        font-size: 13px;
        line-height: 1.6;
        color: #ccc;
    }

    .footer-divider {
        margin: 10px 20px;
        border-top: 1px solid rgba(255,255,255,0.08);
    }

    .footer-bottom.container {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 14px 20px 0;
    }

    .payment-row {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        font-size: 12px;
        color: #c9c9c9;
    }

    .footer-help a {
        color: #c6a45a;
        text-decoration: none;
    }

    .footer-copyright {
        color: #aaa;
        font-size: 12px;
    }

    @media (max-width: 1120px) {
        .footer-grid {
            grid-template-columns: repeat(2, minmax(180px, 1fr));
        }
    }

    @media (max-width: 680px) {
        .footer-grid {
            grid-template-columns: 1fr;
        }

        .app-buttons {
            grid-template-columns: 1fr;
        }
    }
    opacity: 0.9 !important;
    line-height: 1.7 !important;
}

/* DISCOUNT BADGE - LUXURY PREMIUM STYLING */
.discount-badge {
    position: absolute !important;
    top: 16px !important;
    left: 16px !important;
    padding: 10px 22px !important;
    background: linear-gradient(135deg, rgba(20, 20, 20, 0.95) 0%, rgba(10, 10, 10, 0.98) 100%) !important;
    border: 2px solid #c6a45a !important;
    border-radius: 8px !important;
    color: #f5e6c8 !important;
    font-weight: 700 !important;
    font-size: 12px !important;
    letter-spacing: 2px !important;
    box-shadow: 
        0 0 30px rgba(198, 164, 90, 0.4),
        0 8px 24px rgba(0, 0, 0, 0.8),
        inset 0 1px 0 rgba(245, 230, 200, 0.15),
        inset 0 -1px 2px rgba(0, 0, 0, 0.6) !important;
    z-index: 999 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    text-transform: uppercase !important;
    font-family: 'Playfair Display', serif !important;
    transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) !important;
    backdrop-filter: blur(12px) !important;
    min-width: 64px !important;
    white-space: nowrap !important;
    position: relative !important;
}

.watch-card .badge {
    position: absolute !important;
    top: 16px !important;
    left: 16px !important;
    padding: 10px 22px !important;
    background: linear-gradient(135deg, rgba(20, 20, 20, 0.95) 0%, rgba(10, 10, 10, 0.98) 100%) !important;
    border: 2px solid #c6a45a !important;
    border-radius: 8px !important;
    color: #f5e6c8 !important;
    font-weight: 700 !important;
    font-size: 12px !important;
    letter-spacing: 2px !important;
    box-shadow:
        0 0 30px rgba(198, 164, 90, 0.4),
        0 8px 24px rgba(0, 0, 0, 0.8),
        inset 0 1px 0 rgba(245, 230, 200, 0.15),
        inset 0 -1px 2px rgba(0, 0, 0, 0.6) !important;
    z-index: 999 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    text-transform: uppercase !important;
    font-family: 'Playfair Display', serif !important;
    transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) !important;
    backdrop-filter: blur(12px) !important;
    min-width: 64px !important;
    white-space: nowrap !important;
    position: relative !important;
}

/* Hover effect */
.watch-card:hover .discount-badge {
    transform: translateY(-6px) scale(1.1) !important;
    box-shadow: 
        0 0 40px rgba(198, 164, 90, 0.6),
        0 12px 32px rgba(0, 0, 0, 0.9),
        inset 0 1px 0 rgba(245, 230, 200, 0.25),
        inset 0 -1px 3px rgba(0, 0, 0, 0.8) !important;
    color: #fffaed !important;

}

.watch-card:hover .badge {
    transform: translateY(-6px) scale(1.1) !important;
    box-shadow: 
        0 0 40px rgba(198, 164, 90, 0.6),
        0 12px 32px rgba(0, 0, 0, 0.9),
        inset 0 1px 0 rgba(245, 230, 200, 0.25),
        inset 0 -1px 3px rgba(0, 0, 0, 0.8) !important;
    color: #fffaed !important;
}

/* OUT OF STOCK BUTTON */
.btn-cart.disabled {
    opacity: 0.5 !important;
    pointer-events: none !important;
    border: 1px solid #555 !important;
    color: #888 !important;
}

@media (max-width: 768px) {
    .watches {
        grid-template-columns: repeat(auto-fill, 220px) !important;
        gap: 20px !important;
    }

    .watch-card {
    position: relative !important;
    width: 280px !important;
    min-height: 430px !important;
    background: #111 !important;
    border-radius: 12px !important;
    overflow: hidden !important;
    padding-bottom: 20px !important;
    transition: transform 0.4s ease, box-shadow 0.4s ease !important;
    cursor: pointer !important;
}

  .watch-card img {
    width: 100% !important;
    height: 260px !important;
    object-fit: cover !important;

    /* 🔥 ADD THIS */
    display: block !important;
    transition: none !important;   /* prevents unwanted scaling */
}
}

@media (max-width: 480px) {
    .watches {
        grid-template-columns: minmax(0, 280px) !important;
    }

    .watch-card {
        width: 100% !important;
        min-height: 410px !important;
    }
}

/* ===== HIGH BUDGET HOME REFRESH ===== */
body {
    background:
        linear-gradient(180deg, #030303 0%, #080908 45%, #020202 100%) !important;
    margin-top: 0 !important;
    padding-top: 0 !important;
}

.navbar {
    padding: 14px 32px !important;
    background: rgba(0,0,0,0.72) !important;
    border-bottom: 1px solid rgba(198,164,90,0.18) !important;
    box-shadow: 0 18px 50px rgba(0,0,0,0.35) !important;
    box-sizing: border-box !important;
    overflow: visible !important;
}

.nav-container {
    width: 100% !important;
    max-width: 1380px !important;
    margin: 0 auto !important;
}

.logo {
    font-size: 23px !important;
    letter-spacing: 6px !important;
    flex: 0 0 auto !important;
}

.nav-links {
    gap: 22px !important;
    margin-left: auto !important;
    flex-wrap: nowrap !important;
    min-width: 0 !important;
}

.nav-links a,
.user-btn {
    letter-spacing: 0.3px !important;
    white-space: nowrap !important;
}

.user-btn {
    max-width: 150px !important;
}

.user-btn span:not(.arrow) {
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
}

.hero {
    min-height: calc(100vh - 78px) !important;
    height: auto !important;
    margin-top: 0 !important;
    padding-top: 0 !important;
    align-items: stretch !important;
    justify-content: center !important;
    isolation: isolate !important;
    border-bottom: 1px solid rgba(198,164,90,0.15) !important;
}

.hero-video {
    opacity: 0.34 !important;
    filter: brightness(0.48) saturate(0.82) contrast(1.18) !important;
}

.hero-overlay {
    background:
        linear-gradient(90deg, rgba(0,0,0,0.94) 0%, rgba(0,0,0,0.7) 42%, rgba(5,8,6,0.55) 100%),
        radial-gradient(circle at 78% 48%, rgba(12,74,48,0.34), transparent 34%) !important;
    animation: none !important;
}

.glow-orb,
.hero-lines {
    display: none !important;
}

.hero-content {
    width: min(1320px, 92%) !important;
    max-width: none !important;
    min-height: min(760px, calc(100vh - 78px)) !important;
    display: grid !important;
    grid-template-columns: minmax(0, 0.94fr) minmax(360px, 1.06fr) !important;
    align-items: center !important;
    gap: 42px !important;
    text-align: left !important;
    padding: 68px 0 46px !important;
}

.hero-copy {
    position: relative;
    z-index: 4;
}

.hero-copy::after {
    content: "";
    position: absolute;
    top: -12%;
    right: -42%;
    width: 72%;
    height: 118%;
    background: linear-gradient(90deg, rgba(3,3,3,0.78), rgba(3,3,3,0.25), transparent);
    pointer-events: none;
    z-index: -1;
}

.hero-kicker {
    color: #c6a45a;
    font-size: 12px;
    letter-spacing: 5px;
    text-transform: uppercase;
    margin-bottom: 24px;
    animation: heroLift 0.8s ease both;
}

.hero-content h1 {
    font-size: clamp(58px, 7.2vw, 118px) !important;
    line-height: 0.94 !important;
    letter-spacing: 1px !important;
    text-shadow: 0 20px 70px rgba(0,0,0,0.8) !important;
    color: #f8f7f2 !important;
    animation: heroLift 1s ease 0.1s both !important;
    margin-bottom: 34px !important;
}

.hero-content p {
    color: rgba(248,247,242,0.72) !important;
    text-transform: none !important;
    letter-spacing: 0 !important;
    font-size: 18px !important;
    line-height: 1.9 !important;
    max-width: 620px !important;
    animation: heroLift 1s ease 0.2s both !important;
}

.hero-decor-top,
.hero-decor-bottom {
    display: none !important;
}

.hero-actions {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
    margin-top: 34px;
    animation: heroLift 1s ease 0.32s both;
}

.hero-cta {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 170px;
    min-height: 46px;
    padding: 12px 22px;
    border: 1px solid rgba(198,164,90,0.72);
    color: #c6a45a;
    text-decoration: none;
    border-radius: 8px;
    transition: 0.35s ease;
}

.hero-cta.primary,
.hero-cta:hover {
    background: #c6a45a;
    color: #020202;
    box-shadow: 0 20px 48px rgba(198,164,90,0.22);
}

.hero-visual {
    position: relative;
    min-height: 640px;
    display: grid;
    place-items: center;
    animation: heroWatchIn 1.1s ease 0.18s both;
    align-self: center;
    max-height: 720px;
    overflow: visible;
}

.hero-visual::before {
    content: "";
    position: absolute;
    inset: -16% -12% -10% -18%;
    width: auto;
    height: auto;
    border: 0;
    background:
        radial-gradient(circle at 54% 52%, rgba(198,164,90,0.22), transparent 28%),
        radial-gradient(circle at 72% 38%, rgba(50,115,72,0.22), transparent 34%),
        linear-gradient(90deg, rgba(0,0,0,0.72), transparent 42%, rgba(0,0,0,0.44));
    filter: blur(18px);
    transform: none;
    z-index: -1;
}

.hero-visual::after {
    content: "";
    position: absolute;
    inset: -8% -8% 8% -20%;
    background:
        linear-gradient(90deg, #030303 0%, rgba(3,3,3,0.82) 12%, rgba(3,3,3,0.08) 34%, transparent 54%),
        linear-gradient(180deg, #030303 0%, transparent 18%, transparent 72%, #030303 100%),
        linear-gradient(110deg, transparent 14%, rgba(255,255,255,0.12) 46%, transparent 58%);
    transform: translateX(-120%);
    animation: heroSheen 4.8s ease-in-out infinite;
    pointer-events: none;
    mix-blend-mode: screen;
    z-index: 2;
}

.hero-watch {
    width: 100%;
    max-width: 860px;
    height: clamp(540px, 68vh, 720px);
    object-fit: cover;
    object-position: center;
    border: 0;
    transform: translateX(-18px) scale(1.08);
    filter: saturate(0.88) contrast(1.08) brightness(0.94) drop-shadow(0 48px 90px rgba(0,0,0,0.9));
    -webkit-mask-image:
        linear-gradient(90deg, transparent 0%, black 15%, black 86%, transparent 100%),
        linear-gradient(180deg, transparent 0%, black 12%, black 80%, transparent 100%);
    -webkit-mask-composite: source-in;
    mask-image:
        linear-gradient(90deg, transparent 0%, black 15%, black 86%, transparent 100%),
        linear-gradient(180deg, transparent 0%, black 12%, black 80%, transparent 100%);
    mask-composite: intersect;
}

.hero-stats {
    position: absolute;
    left: 0;
    right: 0;
    bottom: -20px;
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1px;
    background: linear-gradient(90deg, rgba(198,164,90,0.08), rgba(198,164,90,0.2), rgba(50,115,72,0.1));
    border: 1px solid rgba(198,164,90,0.18);
    backdrop-filter: blur(18px);
    box-shadow: 0 24px 60px rgba(0,0,0,0.46);
    z-index: 3;
}

.hero-stat {
    background: rgba(2,2,2,0.56);
    padding: 18px;
}

.hero-stat strong {
    display: block;
    color: #fff;
    font-size: 22px;
    margin-bottom: 3px;
}

.hero-stat span {
    color: rgba(255,255,255,0.62);
    font-size: 12px;
}

@keyframes heroLift {
    from { opacity: 0; transform: translateY(28px); filter: blur(8px); }
    to { opacity: 1; transform: translateY(0); filter: blur(0); }
}

@keyframes heroWatchIn {
    from { opacity: 0; transform: translateX(40px) scale(0.96); }
    to { opacity: 1; transform: translateX(0) scale(1); }
}

@keyframes heroSheen {
    0%, 45% { transform: translateX(-120%); }
    72%, 100% { transform: translateX(120%); }
}

.prestige-band {
    width: min(1320px, 92%);
    margin: 0 auto;
    padding: 72px 0 32px;
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 18px;
}

.prestige-item {
    border-top: 1px solid rgba(198,164,90,0.34);
    padding: 24px 0 0;
}

.prestige-item span {
    display: block;
    color: #c6a45a;
    font-size: 12px;
    letter-spacing: 3px;
    text-transform: uppercase;
    margin-bottom: 10px;
}

.prestige-item p {
    color: rgba(255,255,255,0.72);
    line-height: 1.8;
    margin: 0;
}

.editorial-feature {
    width: min(1320px, 92%);
    margin: 52px auto 20px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    min-height: 480px;
    border: 1px solid rgba(198,164,90,0.18);
    background: #090909;
    overflow: hidden;
}

.editorial-image {
    min-height: 480px;
    background:
        linear-gradient(90deg, rgba(0,0,0,0.1), rgba(0,0,0,0.62)),
        url("images/watch2.jpg") center/cover;
}

.editorial-copy {
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 64px;
}

.editorial-copy span {
    color: #c6a45a;
    letter-spacing: 4px;
    text-transform: uppercase;
    font-size: 12px;
}

.editorial-copy h2 {
    font-family: 'Playfair Display', serif;
    font-size: clamp(38px, 4vw, 68px);
    line-height: 1;
    margin: 18px 0;
    color: #f8f7f2;
}

.editorial-copy p {
    color: rgba(255,255,255,0.7);
    line-height: 1.85;
    max-width: 520px;
}

.collection {
    width: min(1320px, 92%) !important;
    max-width: none !important;
    padding: 96px 0 80px !important;
}

.section-title {
    font-family: 'Playfair Display', serif !important;
    font-size: clamp(42px, 5vw, 76px) !important;
    color: #f8f7f2 !important;
    margin-bottom: 14px !important;
}

.collection-intro {
    color: rgba(255,255,255,0.62);
    text-align: center;
    max-width: 680px;
    margin: 0 auto 36px;
    line-height: 1.8;
}

.search-input,
.sort-button {
    border-radius: 8px !important;
    background: rgba(255,255,255,0.045) !important;
}

input:-webkit-autofill,
input:-webkit-autofill:hover,
input:-webkit-autofill:focus,
textarea:-webkit-autofill,
textarea:-webkit-autofill:hover,
textarea:-webkit-autofill:focus {
    -webkit-text-fill-color: #ffffff !important;
    caret-color: #ffffff !important;
    box-shadow: 0 0 0 1000px #050505 inset !important;
    -webkit-box-shadow: 0 0 0 1000px #050505 inset !important;
    border-color: rgba(198,164,90,0.5) !important;
    transition: background-color 9999s ease-in-out 0s !important;
}

.watches {
    gap: 34px !important;
}

.watch-card {
    position: relative !important;
    width: 280px !important;
    min-height: 430px !important;
    background: linear-gradient(180deg, rgba(255,255,255,0.065), rgba(255,255,255,0.02)) !important;
    border: 1px solid rgba(198,164,90,0.2) !important;
    border-radius: 8px !important;
    overflow: hidden !important;
    padding-bottom: 20px !important;
    transition: transform 0.4s ease, box-shadow 0.4s ease, border 0.4s ease !important;
    cursor: pointer !important;
    box-shadow: 0 22px 55px rgba(0,0,0,0.32) !important;
}

.watch-card:focus {
    outline: 2px solid #c6a45a !important;
    outline-offset: 4px !important;
}

.watch-card:hover {
    border: 2px solid #c6a45a !important;
    transform: translateY(-8px) scale(1.02) !important;
    box-shadow: 0 0 18px rgba(198,164,90,0.46), 0 26px 60px rgba(0,0,0,0.65) !important;
}

.watch-card::after {
    content: "View details";
    position: absolute;
    right: 16px;
    bottom: 20px;
    color: rgba(255,255,255,0.62);
    font-size: 11px;
    letter-spacing: 1px;
    text-transform: uppercase;
    opacity: 0;
    transform: translateY(8px);
    transition: 0.35s;
    z-index: 4;
}

.watch-card:hover::after {
    opacity: 1;
    transform: translateY(0);
}

.watch-card img {
    width: 100% !important;
    height: 286px !important;
    object-fit: cover !important;
    display: block !important;
}

.watch-card h3 {
    padding: 15px !important;
    margin: 0 !important;
    color: #ffffff !important;
    position: relative !important;
    z-index: 2 !important;
}

.price {
    color: #c6a45a !important;
    padding-left: 15px !important;
    position: relative !important;
    z-index: 2 !important;
}

.btn-cart {
    margin-left: 15px !important;
    border: 1px solid #c6a45a !important;
    background: transparent !important;
    color: #c6a45a !important;
    padding: 10px 18px !important;
    border-radius: 20px !important;
    cursor: pointer !important;
    position: relative !important;
    overflow: hidden !important;
    z-index: 3 !important;
    transition: color 0.4s ease !important;
}

.btn-cart::before {
    content: "" !important;
    position: absolute !important;
    top: 0 !important;
    left: -100% !important;
    width: 100% !important;
    height: 100% !important;
    background: #c6a45a !important;
    transition: left 0.4s ease !important;
    z-index: -1 !important;
}

.btn-cart:hover {
    color: black !important;
}

.btn-cart:hover::before {
    left: 0 !important;
}

@media (max-width: 980px) {
    .hero-content,
    .editorial-feature {
        grid-template-columns: 1fr !important;
    }

    .hero-visual {
        min-height: 540px;
    }

    .hero-watch {
        height: 520px;
    }

    .prestige-band {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 1180px) {
    .navbar {
        padding: 14px 22px !important;
    }

    .logo {
        font-size: 20px !important;
        letter-spacing: 4px !important;
    }

    .nav-links {
        gap: 14px !important;
    }

    .nav-links a,
    .user-btn {
        font-size: 13px !important;
    }
}

@media (max-width: 640px) {
    .navbar {
        padding: 14px 20px !important;
    }

    .logo {
        font-size: 18px !important;
        letter-spacing: 4px !important;
    }

    .hero-content {
        padding-top: 48px !important;
    }

    .hero-watch {
        height: 440px;
    }

    .hero-stats {
        position: relative;
        bottom: auto;
        grid-template-columns: 1fr;
        margin-top: 18px;
    }

    .editorial-copy {
        padding: 36px 24px;
    }
}


/* ===== EMPTY STATE - NO WATCHES FOUND ===== */
.watch-not-found {
    grid-column: 1 / -1 !important;
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 120px 40px !important;
    text-align: center !important;
    background: linear-gradient(135deg, rgba(198,164,90,0.08) 0%, rgba(198,164,90,0.02) 100%) !important;
    border: 2px solid rgba(198,164,90,0.25) !important;
    border-radius: 16px !important;
    margin: 40px 0 !important;
    position: relative !important;
    overflow: hidden !important;
}

.watch-not-found::before {
    content: "" !important;
    position: absolute !important;
    top: -50% !important;
    right: -50% !important;
    width: 400px !important;
    height: 400px !important;
    background: radial-gradient(circle, rgba(198,164,90,0.15) 0%, transparent 70%) !important;
    z-index: 0 !important;
    animation: float 6s ease-in-out infinite !important;
}

@keyframes float {
    0%, 100% { transform: translate(0, 0) rotate(0deg); }
    50% { transform: translate(20px, -20px) rotate(180deg); }
}

.watch-not-found h3 {
    font-size: 42px !important;
    font-weight: 300 !important;
    letter-spacing: 2px !important;
    color: #ffffff !important;
    margin: 0 0 16px 0 !important;
    position: relative !important;
    z-index: 1 !important;
    font-family: 'Playfair Display', serif !important;
    text-transform: uppercase !important;
    background: linear-gradient(135deg, #ffffff 0%, #c6a45a 100%) !important;
    -webkit-background-clip: text !important;
    -webkit-text-fill-color: transparent !important;
    background-clip: text !important;
}

.watch-not-found p {
    font-size: 16px !important;
    color: rgba(255,255,255,0.7) !important;
    letter-spacing: 0.5px !important;
    line-height: 1.8 !important;
    margin: 0 !important;
    max-width: 500px !important;
    position: relative !important;
    z-index: 1 !important;
    font-weight: 300 !important;
}

@media (max-width: 768px) {
    .watch-not-found {
        padding: 80px 24px !important;
    }
    
    .watch-not-found h3 {
        font-size: 28px !important;
    }
    
    .watch-not-found p {
        font-size: 14px !important;
    }
}


</style>
</head>

<body>

<!-- ✅ TOAST -->
<?php
if(isset($_SESSION['cart_msg'])){
    echo "<div class='toast'>".$_SESSION['cart_msg']."</div>";
    unset($_SESSION['cart_msg']);
}
?>

<!-- ===== ORIGINAL NAVBAR ===== -->
<header class="navbar">
    <div class="nav-container">

        <div class="logo">TIME STEAL</div>

        <ul class="nav-links">
            <li><a href="home.php">Home</a></li>
            <li><a href="#collection">Collection</a></li>
            <li><a href="about.html">About</a></li>
            <li><a href="contact.php">Contact</a></li>

            <?php if (isset($_SESSION['loggedin'])): ?>
                <!-- CART (only for logged in users) -->
                <li class="cart-icon">
                    <a href="cart.php">Cart</a>
                    <span class="cart-count"><?php echo $count; ?></span>
                </li>

                <!-- USER PROFILE MENU (logged in) -->
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

                        <!-- CURRENT USER -->
                        <div class="account active">
                            <div class="avatar small">
                                <?php echo strtoupper($_SESSION['user'][0]); ?>
                            </div>
                            <span><?php echo $_SESSION['user']; ?></span>
                        </div>

                        <!-- FAKE MULTI USERS (UI ONLY) -->
                        <?php
                        // 🔥 FETCH UNIQUE USERNAMES
                        $users = $conn->query("SELECT DISTINCT username FROM users ORDER BY username");

                        while($u = $users->fetch_assoc()){
                            $name = $u['username'];
                            if($name === $_SESSION['user']) continue;
                        ?>

                        <a href="switch_user.php?user=<?php echo urlencode($name); ?>" class="account">
                            <div class="avatar small">
                                <?php echo strtoupper($name[0]); ?>
                            </div>
                            <span><?php echo $name; ?></span>
                        </a>

                        <?php } ?>

                        <div class="dropdown-divider"></div>
                        <a href="register.php" class="account"><span>➕ Add Account</span></a>
                        <a href="logout.php" class="logout">Logout</a>
                    </div>
                </li>
            <?php else: ?>
                <!-- LOGIN & REGISTER BUTTONS (not logged in) -->
                <li class="auth-buttons">
                    <a href="login.php" class="btn-login">Login</a>
                    <a href="register.php" class="btn-register">Register</a>
                </li>
            <?php endif; ?>
        </ul>

    </div>
</header>

<!-- ===== HERO ===== -->
<section class="hero">
    <!-- Premium Background Video - Watches Falling -->
    <video 
        class="hero-video" 
        autoplay 
        muted 
        loop 
        playsinline
        preload="auto">
        <source src="https://videos.pexels.com/video-files/3735648/3735648-hd_1920_1080_30fps.mp4" type="video/mp4">
        <source src="https://videos.pexels.com/video-files/2676623/2676623-hd_1920_1080_30fps.mp4" type="video/mp4">
    </video>

    <!-- Overlay Gradient -->
    <div class="hero-overlay"></div>

    <!-- Animated Lines -->
    <div class="hero-lines">
        <div class="hero-line"></div>
        <div class="hero-line"></div>
        <div class="hero-line"></div>
        <div class="hero-line"></div>
    </div>

    <!-- Luxury Glow Orbs -->
    <div class="glow-orb glow-orb-1"></div>
    <div class="glow-orb glow-orb-2"></div>

    <div class="hero-content">
        <div class="hero-copy">
            <div class="hero-kicker">TimeSteal Atelier</div>
            <h1>Precision Beyond Time</h1>
            <p>Rare silhouettes, polished mechanics, and modern restraint for collectors who choose presence over noise.</p>
            <div class="hero-actions">
                <a class="hero-cta primary" href="#collection">Explore Collection</a>
                <a class="hero-cta" href="product_detail.php?id=5">View Platinum Edge</a>
            </div>
        </div>

        <div class="hero-visual" aria-hidden="true">
            <img class="hero-watch" src="images/watch5.jpg" alt="">
            <div class="hero-stats">
                <div class="hero-stat">
                    <strong>09</strong>
                    <span>Signature models</span>
                </div>
                <div class="hero-stat">
                    <strong>2 yrs</strong>
                    <span>Service warranty</span>
                </div>
                <div class="hero-stat">
                    <strong>3-5</strong>
                    <span>Day insured delivery</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="prestige-band">
    <div class="prestige-item">
        <span>Materials</span>
        <p>Polished steel, gold tone finishing, mineral crystal, and straps selected for daily comfort.</p>
    </div>
    <div class="prestige-item">
        <span>Experience</span>
        <p>Clean product pages, customer reviews, cart previews, and a detail-first buying flow.</p>
    </div>
    <div class="prestige-item">
        <span>Service</span>
        <p>Every watch includes premium packaging, care guidance, and a two-year service warranty.</p>
    </div>
</section>

<section class="editorial-feature">
    <div class="editorial-image" aria-hidden="true"></div>
    <div class="editorial-copy">
        <span>Featured Release</span>
        <h2>Golden Chronos</h2>
        <p>A bold chronograph profile with a gold-toned finish, built for evenings, celebrations, and the kind of entrance that does not need explaining.</p>
        <div class="hero-actions">
            <a class="hero-cta primary" href="product_detail.php?id=2">Discover the Watch</a>
        </div>
    </div>
</section>

<!-- ===== COLLECTION ===== -->
<section class="collection container" id="collection">
<h2 class="section-title">Featured Watches</h2>
<p class="collection-intro">A focused collection of nine premium watches, each with full product details, customer reviews, and cart-ready buying.</p>

<!-- ===== SEARCH & SORT BAR ===== -->
<div class="search-sort-bar">
    <div class="search-container">
        <input type="text" id="searchInput" class="search-input" placeholder="Search watches...">
        <span class="search-icon">🔍</span>
    </div>
    
    <div class="sort-dropdown">
        <button type="button" id="sortButton" class="sort-button">
            <span id="sortLabel">Sort By</span>
            <span class="dropdown-arrow">▼</span>
        </button>
        <div id="sortOptions" class="sort-options">
            <div class="sort-option active" data-value="default">Sort By</div>
            <div class="sort-option" data-value="price-low">Price: Low to High</div>
            <div class="sort-option" data-value="price-high">Price: High to Low</div>
            <div class="sort-option" data-value="name-az">Name: A to Z</div>
            <div class="sort-option" data-value="name-za">Name: Z to A</div>
            <div class="sort-option" data-value="discount">Discount: High to Low</div>
        </div>
    </div>
</div>

<!-- ===== WATCHES GRID ===== -->
<div class="watches"></div>

<footer class="site-footer">
    <div class="footer-grid container">
        <div class="footer-column">
            <h3>COLLECTIONS</h3>
            <a href="#">Diver's</a>
            <a href="#">TimeSteal Automatics</a>
            <a href="#">Police Batman</a>
            <a href="#">Stellar</a>
            <a href="#">Raga Power Pearls</a>
            <a href="#">Nebula Jewels</a>
            <a href="#">Grandmaster</a>
            <a href="#">Maritime</a>
        </div>

        <div class="footer-column">
            <h3>CUSTOMER SERVICE</h3>
            <a href="#">Payment Options</a>
            <a href="#">Track Order</a>
            <a href="#">Encircle Program</a>
            <a href="#">Find TimeSteal Flagships</a>
            <a href="#">E Warranty Registration</a>
        </div>

        <div class="footer-column">
            <div class="footer-section">
                <h3>CONTACT US</h3>
                <p class="footer-contact">+91 98765 43210</p>
                <p class="footer-contact">support@timesteal.com</p>
                <a href="#">Help & Contact</a>
                <a href="#">FAQs</a>
            </div>
            <div class="footer-section">
                <h3>ABOUT TIME STEAL</h3>
                <a href="#">Brand Protection</a>
                <a href="#">Corporate</a>
                <a href="#">Careers</a>
                <a href="#">Blog</a>
            </div>
        </div>

        <div class="footer-column footer-side">
            <h3>Download TimeSteal App</h3>
            <div class="app-buttons">
                <a href="#" class="app-button">
                    <span class="app-icon">
                        <svg viewBox="0 0 384 512" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path fill="currentColor" d="M316.5 256.8c-2.8-36.1 16-63.6 51.8-78.6-19.3-28.1-48.2-44.6-84-47.4-35.4-2.9-70.7 21.4-89.7 21.4-19.6 0-50.5-20.9-82.7-20.4-42.6.6-82.1 24.6-104 62.6-44.5 78.4-11.3 194 31.9 257.7 21.1 31.5 46.3 67 79.2 65.5 31.7-1.5 43.7-20.1 82.2-20.1 38 0 50.8 20.1 82.2 19.3 33.1-.9 54.7-32.9 75.7-64.5 21-31.1 29.7-61.2 30.1-62.7-.7-.3-58.5-22.5-61.2-85.9zM258.4 0c-20.1 1.2-44.4 13.9-58.5 32-12.1 15.4-22.7 39.4-19.8 62.2 21.4 1.2 44.2-11.5 58.1-30.8 9.7-12.1 17.1-31.4 15.5-49.4z"/>
                        </svg>
                    </span>
                    <span class="app-copy">
                        <span>Download on the</span>
                        <strong>App Store</strong>
                    </span>
                </a>
                <a href="#" class="app-button">
                    <span class="app-icon">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path fill="#34A853" d="M3.8 2.5 18.6 12 3.8 21.5C3.1 21.9 2.2 21.6 1.8 20.9c-.4-.7-.2-1.6.5-2.1L12 12 2.3 5.2c-.7-.4-.9-1.4-.5-2.1.4-.7 1.3-.9 2-.5Z"/>
                            <path fill="#EA4335" d="M19.7 4.8 10.9 11.5 13.5 13.3 22.3 6.6c.6-.4.8-1.2.5-1.9-.4-.7-1.2-1.1-2-.9l-1.1.1Z"/>
                            <path fill="#FBBC05" d="M3.8 2.5 12 12l3.2-2.4L5.6 1.8c-.7-.4-1.6-.2-2 .7-.2.4-.2.9 0 1.2Z"/>
                            <path fill="#4285F4" d="M22.3 17.4 13.5 10.7 10.9 12.5 19.7 19.2c.8.6 1.9.4 2.4-.4.4-.7.2-1.6-.5-2.1Z"/>
                        </svg>
                    </span>
                    <span class="app-copy">
                        <span>Get it on</span>
                        <strong>Google Play</strong>
                    </span>
                </a>
            </div>
            <div class="footer-social">
                <span>Follow Us</span>
                <div class="social-links">
                    <a href="#" class="social-icon facebook" aria-label="Facebook">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M22 12a10 10 0 1 0-11.5 9.9v-7H8.2v-2.9h2.3V9.5c0-2.3 1.4-3.6 3.5-3.6 1 0 2 .1 2 .1v2.3h-1.2c-1.2 0-1.6.8-1.6 1.6v1.9h2.8l-.4 2.9h-2.4V22A10 10 0 0 0 22 12"/></svg>
                    </a>
                    <a href="#" class="social-icon instagram" aria-label="Instagram">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 7.5A4.5 4.5 0 1 0 16.5 12 4.51 4.51 0 0 0 12 7.5Zm6.4-.9a1.05 1.05 0 1 1-1.05-1.05A1.05 1.05 0 0 1 18.4 6.6ZM12 9.8A2.2 2.2 0 1 1 9.8 12 2.2 2.2 0 0 1 12 9.8Zm6.7 1.9c0 3.1-2.5 5.6-5.6 5.6s-5.6-2.5-5.6-5.6 2.5-5.6 5.6-5.6 5.6 2.5 5.6 5.6Zm0 0"/></svg>
                    </a>
                    <a href="#" class="social-icon x" aria-label="X">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M19.4 7.7c-.4.6-.9 1.2-1.5 1.7l-3.5 2.9 4.2 5.7c.5.7.4 1.7-.3 2.2-.8.5-1.8.4-2.3-.3l-4-5.4-2.8 2.4c-.8.7-1.9.6-2.5-.2-.6-.7-.6-1.8.1-2.4l2.9-2.5-4.8-3.6c-.9-.6-1.1-1.8-.5-2.7.6-.9 1.8-1.1 2.7-.5l5.4 3.9 2.4-2c.9-.7 2.1-.5 2.7.4.5.8.4 1.8-.4 2.4Z"/></svg>
                    </a>
                    <a href="#" class="social-icon youtube" aria-label="YouTube">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M10 15.5l5.8-3.5L10 8.5v7Zm11.4-3.6c0 1.4-.1 2.7-.4 3.8-.3 1.1-1.2 2-2.3 2.3-2 .5-10.7.5-10.7.5s-8.7 0-10.7-.5c-1.1-.3-2-1.2-2.3-2.3C.1 14.6 0 13.3 0 11.9s.1-2.7.4-3.8c.3-1.1 1.2-2 2.3-2.3 2-.5 10.7-.5 10.7-.5s8.7 0 10.7.5c1.1.3 2 1.2 2.3 2.3.3 1.1.4 2.4.4 3.8Z"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="footer-divider"></div>
    <div class="footer-bottom container">
        <div class="payment-row">
            <span>VISA</span>
            <span>MasterCard</span>
            <span>PayPal</span>
            <span>American Express</span>
            <span>Diners Club</span>
            <span>RuPay</span>
            <span>ICICI</span>
            <span>AXIS Bank</span>
        </div>
        <div class="footer-copyright">
            © 2026 TimeSteal Company Limited. All Rights Reserved.
        </div>
    </div>
</footer>

<script>
// 🎬 WATCH DATA
const watchesData = [
  {id:1, img:"watch1.jpg", name:"Obsidian Royale", price:3500, discount:0, stock:1},
  {id:2, img:"watch2.jpg", name:"Golden Chronos", price:4200, discount:10, stock:1},
  {id:3, img:"watch3.jpg", name:"Midnight Steel", price:3800, discount:0, stock:1},
  {id:4, img:"watch4.jpg", name:"Royal Phantom", price:5400, discount:0, stock:0},
  {id:5, img:"watch5.jpg", name:"Platinum Edge", price:6100, discount:15, stock:1},
  {id:6, img:"watch6.jpg", name:"Aurora Chronograph", price:4800, discount:0, stock:1},
  {id:7, img:"watch7.jpg", name:"Black Nebula", price:5200, discount:5, stock:1},
  {id:8, img:"watch8.jpg", name:"Emerald Prestige", price:4700, discount:8, stock:1},
  {id:9, img:"watch9.jpg", name:"Titanium Horizon", price:5600, discount:20, stock:1},
];

// 🔍 RENDER WATCHES
function renderWatches(watches) {
  const container = document.querySelector('.watches');
  container.innerHTML = '';
  
  if(watches.length === 0) {
    container.innerHTML = `
      <div class="watch-not-found">
        <h3>No watches found</h3>
        <p>We couldn't find any watches matching that search. Try another keyword or browse the full collection.</p>
      </div>
    `;
    return;
  }
  
  watches.forEach(watch => {
    const finalPrice = watch.discount > 0 ? watch.price - (watch.price * watch.discount / 100) : watch.price;
    
    const card = document.createElement('div');
    card.className = 'watch-card';
    card.setAttribute('role', 'link');
    card.setAttribute('tabindex', '0');
    card.addEventListener('click', () => {
      window.location.href = `product_detail.php?id=${watch.id}`;
    });
    card.addEventListener('keydown', (event) => {
      if(event.key === 'Enter') {
        window.location.href = `product_detail.php?id=${watch.id}`;
      }
    });
    
    let badgeHTML = '';
    if(watch.discount > 0) {
      badgeHTML = `<div class="discount-badge">-${watch.discount}%</div>`;
    }
    
    let priceHTML = '';
    if(watch.discount > 0) {
      priceHTML = `<span style="text-decoration: line-through; color:#888;">₹${watch.price}</span> ₹${finalPrice}`;
    } else {
      priceHTML = `₹${watch.price}`;
    }
    
    let buttonHTML = '';
    if(watch.stock == 0) {
      buttonHTML = `<button class="btn-cart disabled">Out of Stock</button>`;
    } else {
      buttonHTML = `<a href="add_to_cart.php?id=${watch.id}" onclick="event.stopPropagation()"><button class="btn-cart">Add to Cart</button></a>`;
    }
    
    card.innerHTML = `
      ${badgeHTML}
      <img src="images/${watch.img}">
      <h3>${watch.name}</h3>
      <p class="price">${priceHTML}</p>
      ${buttonHTML}
    `;
    
    container.appendChild(card);
  });
}

// 🔎 SEARCH FUNCTIONALITY
document.getElementById('searchInput').addEventListener('keyup', filterAndSort);

// 🔀 SORT FUNCTIONALITY
const sortButton = document.getElementById('sortButton');
const sortOptions = document.getElementById('sortOptions');
let currentSortValue = 'default';

sortButton.addEventListener('click', () => {
  sortOptions.classList.toggle('open');
});

document.querySelectorAll('.sort-option').forEach(option => {
  option.addEventListener('click', (e) => {
    const selectedValue = e.currentTarget.dataset.value;
    currentSortValue = selectedValue;
    document.querySelectorAll('.sort-option').forEach(opt => opt.classList.remove('active'));
    e.currentTarget.classList.add('active');
    document.getElementById('sortLabel').textContent = e.currentTarget.textContent;
    sortOptions.classList.remove('open');
    filterAndSort();
  });
});

window.addEventListener('click', (e) => {
  if (!e.target.closest('.sort-dropdown')) {
    sortOptions.classList.remove('open');
  }
});

// 🎯 FILTER & SORT COMBINED
function filterAndSort() {
  let filtered = watchesData.slice();
  
  // 🔍 SEARCH FILTER
  const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
  if(searchTerm) {
    filtered = filtered.filter(watch => watch.name.toLowerCase().includes(searchTerm));
  }
  
  // 🔀 SORT
  const sortValue = currentSortValue;
  
  if(sortValue === 'price-low') {
    filtered.sort((a, b) => {
      const priceA = a.discount > 0 ? a.price - (a.price * a.discount / 100) : a.price;
      const priceB = b.discount > 0 ? b.price - (b.price * b.discount / 100) : b.price;
      return priceA - priceB;
    });
  } else if(sortValue === 'price-high') {
    filtered.sort((a, b) => {
      const priceA = a.discount > 0 ? a.price - (a.price * a.discount / 100) : a.price;
      const priceB = b.discount > 0 ? b.price - (b.price * b.discount / 100) : b.price;
      return priceB - priceA;
    });
  } else if(sortValue === 'name-az') {
    filtered.sort((a, b) => a.name.localeCompare(b.name));
  } else if(sortValue === 'name-za') {
    filtered.sort((a, b) => b.name.localeCompare(a.name));
  } else if(sortValue === 'discount') {
    filtered.sort((a, b) => b.discount - a.discount);
  }
  
  renderWatches(filtered);
}

// 🎬 INITIAL RENDER
renderWatches(watchesData);

// 🔽 USER DROPDOWN
function toggleDropdown() {
    document.getElementById("dropdown").classList.toggle("show");
}

/* Close when clicking outside */
window.onclick = function(e) {
    if (!e.target.closest('.user-menu')) {
        document.getElementById("dropdown").classList.remove("show");
    }
}
</script>
</body>
</html>
</body>
</html>
