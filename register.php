<?php
ob_start(); // 🔥 IMPORTANT (fixes header issue)
session_start();
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // VALIDATION
    if(strlen($username) < 3){
        $error = "Username must be at least 3 characters";
    }
    elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $error = "Invalid email format";
    }
    elseif(strlen($password) < 6){
        $error = "Password must be at least 6 characters";
    }
    else {

        $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if($stmt->num_rows > 0){
            $error = "Email already exists!";
        } else {

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users(username,email,password) VALUES(?,?,?)");
            $stmt->bind_param("sss", $username, $email, $hashed_password);

            if($stmt->execute()){

                // 🔥 AUTO LOGIN AFTER REGISTRATION
                $user_id = $stmt->insert_id;
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user'] = $username;
                $_SESSION['loggedin'] = true;
                
                $_SESSION['success'] = "Registered Successfully!";

                // ✅ REDIRECT TO HOME
                header("Location: home.php");
                exit();
            } else {
                $error = "Something went wrong!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register</title>
<link rel="stylesheet" href="auth.css">
</head>

<body>

<div class="auth-container">
<div class="auth-box">

<h2>TIME STEAL</h2>

<form method="POST">

<div class="inputBox">
<input type="text" name="username" required>
<label>Username</label>
</div>

<div class="inputBox">
<input type="email" name="email" required>
<label>Email</label>
</div>

<div class="inputBox">
<input type="password" name="password" id="password" required oninput="checkPasswordStrength()">
<label>Password</label>
</div>

<!-- Password Strength Meter & Requirements -->
<div class="password-conditions">
    <div id="strength-bar" class="strength-bar">
        <div id="strength-fill" class="strength-fill"></div>
    </div>
    <ul class="requirements">
        <li id="req-length" class="req">At least 8 characters</li>
        <li id="req-upper" class="req">One uppercase letter</li>
        <li id="req-lower" class="req">One lowercase letter</li>
        <li id="req-digit" class="req">One number</li>
        <li id="req-special" class="req">One special character (!@#$%^&*)</li>
    </ul>
</div>

<button class="auth-btn" type="submit">Register</button>

</form>

<p class="error"><?php if(isset($error)) echo $error; ?></p>

</div>
</div>

<script src="transition.js"></script>

<script>
function checkPasswordStrength() {
    const pwd = document.getElementById('password').value;
    let score = 0;
    // Conditions
    const length = pwd.length >= 8;
    const upper = /[A-Z]/.test(pwd);
    const lower = /[a-z]/.test(pwd);
    const digit = /[0-9]/.test(pwd);
    const special = /[!@#$%^&*]/.test(pwd);
    // Visual feedback
    document.getElementById('req-length').classList.toggle('met', length);
    document.getElementById('req-upper').classList.toggle('met', upper);
    document.getElementById('req-lower').classList.toggle('met', lower);
    document.getElementById('req-digit').classList.toggle('met', digit);
    document.getElementById('req-special').classList.toggle('met', special);
    score = [length, upper, lower, digit, special].filter(Boolean).length;
    // Strength bar
    const fill = document.getElementById('strength-fill');
    fill.style.width = (score * 20) + '%';
    fill.style.background =
        score <= 2 ? '#ff4d4d' :
        score === 3 ? '#ffc107' :
        score === 4 ? '#ffe066' : '#4caf50';
}
</script>
<style>
.password-conditions {
    margin-bottom: 18px;
    text-align: left;
}
.strength-bar {
    width: 100%;
    height: 8px;
    background: #222;
    border-radius: 6px;
    margin-bottom: 8px;
    overflow: hidden;
    box-shadow: 0 0 8px #c6a45a33;
}
.strength-fill {
    height: 100%;
    width: 0%;
    background: #ff4d4d;
    border-radius: 6px;
    transition: width 0.4s, background 0.4s;
}
.requirements {
    list-style: none;
    padding: 0 0 0 5px;
    margin: 0;
}
.req {
    color: #bbb;
    font-size: 13px;
    margin-bottom: 2px;
    transition: color 0.3s;
    position: relative;
    padding-left: 18px;
}
.req.met {
    color: #4caf50;
}
.req.met::before {
    content: '✔';
    color: #4caf50;
    position: absolute;
    left: 0;
    font-size: 13px;
}
.req::before {
    content: '•';
    color: #bbb;
    position: absolute;
    left: 0;
    font-size: 13px;
}
</style>

</body>
</html>

<?php
ob_end_flush(); // 🔥 important
?>