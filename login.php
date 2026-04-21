<?php
session_start();
include "config.php";

// ✅ AUTO LOGIN VIA COOKIE
if(isset($_COOKIE['user_id'])){
    $_SESSION['user_id'] = $_COOKIE['user_id'];

    $stmt = $conn->prepare("SELECT username FROM users WHERE id=?");
    $stmt->bind_param("i", $_COOKIE['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if($row = $result->fetch_assoc()){
        $_SESSION['user'] = $row['username'];
        $_SESSION['loggedin'] = true;
        header("Location: home.php");
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {

        if (password_verify($password, $row['password'])) {

            $_SESSION['user'] = $row['username'];
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['loggedin'] = true;

            // ✅ REMEMBER ME COOKIE
            if(isset($_POST['remember'])){
                setcookie("user_id", $row['id'], time() + (86400 * 7), "/");
            }

            header("Location: home.php");
            exit();

        } else {
            $error = "Invalid password";
        }

    } else {
        $error = "User not found";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="auth.css">
<title>TimeSteal</title>
</head>

<body>
    <?php
if(isset($_SESSION['success'])){
    echo "<div class='toast'>".$_SESSION['success']."</div>";
    unset($_SESSION['success']);
}
?>

<canvas id="particles"></canvas>

<!-- ✅ TOAST -->
<?php
if(isset($_SESSION['success'])){
    echo "<div class='toast'>".$_SESSION['success']."</div>";
    unset($_SESSION['success']);
}
?>

<div class="auth-container">

    <div class="auth-box">

        <h2>TIME STEAL</h2>
        <p>Crafted for those who value every second</p>

        <form method="POST">

            <div class="inputBox">
                <input type="text" name="username" required>
                <label>Username</label>
            </div>

            <div class="inputBox">
                <input type="password" name="password" required>
                <label>Password</label>
            </div>

            <button class="auth-btn">Login</button>

        </form>

        <div class="auth-link">
            New here? <a href="register.php">Create Account</a>
        </div>

        <p class="error"><?php if(isset($error)) echo $error; ?></p>

    </div>

</div>

<script src="transition.js"></script>

<!-- PARTICLES -->
<script>
const canvas = document.getElementById("particles");
const ctx = canvas.getContext("2d");

canvas.width = window.innerWidth;
canvas.height = window.innerHeight;

let particles = [];

for(let i=0;i<20;i++){   // reduced
    particles.push({
        x: Math.random()*canvas.width,
        y: Math.random()*canvas.height,
        r: Math.random()*2,
        d: Math.random()*0.5
    });
}

function draw(){
    ctx.clearRect(0,0,canvas.width,canvas.height);

    ctx.fillStyle = "rgba(198,164,90,0.12)";
    ctx.beginPath();

    for(let i=0;i<particles.length;i++){
        let p = particles[i];
        ctx.moveTo(p.x,p.y);
        ctx.arc(p.x,p.y,p.r,0,Math.PI*2,true);
    }

    ctx.fill();
    update();
}

function update(){
    for(let i=0;i<particles.length;i++){
        let p = particles[i];
        p.y += p.d;

        if(p.y > canvas.height){
            p.y = 0;
            p.x = Math.random()*canvas.width;
        }
    }
}

setInterval(draw, 33);
</script>

</body>
</html>