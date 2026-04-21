<?php
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require __DIR__ . "/PHPMailer/src/Exception.php";
require __DIR__ . "/PHPMailer/src/PHPMailer.php";
require __DIR__ . "/PHPMailer/src/SMTP.php";

$mailConfig = require __DIR__ . "/mail_config.php";

function showMessage($title, $message)
{
    $safeTitle = htmlspecialchars($title, ENT_QUOTES, "UTF-8");
    $safeMessage = htmlspecialchars($message, ENT_QUOTES, "UTF-8");

    echo "<!DOCTYPE html>
<html>
<head>
<meta charset=\"UTF-8\">
<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
<title>{$safeTitle}</title>
<style>
body{margin:0;min-height:100vh;display:grid;place-items:center;background:#050505;color:#fff;font-family:Arial,sans-serif}
.box{max-width:520px;padding:32px;border:1px solid #c6a45a;background:#111;text-align:center}
h1{color:#c6a45a;margin-top:0}
a{color:#c6a45a}
</style>
</head>
<body>
<div class=\"box\">
<h1>{$safeTitle}</h1>
<p>{$safeMessage}</p>
<a href=\"contact.php\">Back to Contact</a>
</div>
</body>
</html>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    showMessage("Invalid Request", "Please send your message from the contact form.");
}

$name = trim($_POST["name"] ?? "");
$visitorEmail = trim($_POST["email"] ?? "");
$message = trim($_POST["message"] ?? "");

if ($name === "" || $visitorEmail === "" || $message === "") {
    showMessage("Missing Details", "Please fill in your name, email address, and message.");
}

if (!filter_var($visitorEmail, FILTER_VALIDATE_EMAIL)) {
    showMessage("Invalid Email", "Please enter a valid email address.");
}

if (
    $mailConfig["username"] === "your@gmail.com" ||
    $mailConfig["password"] === "your_gmail_app_password" ||
    $mailConfig["from_email"] === "your@gmail.com"
) {
    showMessage(
        "Mail Not Configured",
        "Open mail_config.php and replace the placeholder Gmail address and app password with real SMTP credentials."
    );
}

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = $mailConfig["host"];
    $mail->SMTPAuth = true;
    $mail->Username = $mailConfig["username"];
    $mail->Password = $mailConfig["password"];
    $mail->SMTPSecure = $mailConfig["secure"] === "ssl"
        ? PHPMailer::ENCRYPTION_SMTPS
        : PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = (int) $mailConfig["port"];

    $mail->setFrom($mailConfig["from_email"], $mailConfig["from_name"]);
    $mail->addAddress($visitorEmail, $name);
    $mail->addReplyTo($mailConfig["from_email"], $mailConfig["from_name"]);

    if (!empty($mailConfig["owner_email"]) && filter_var($mailConfig["owner_email"], FILTER_VALIDATE_EMAIL)) {
        $mail->addBCC($mailConfig["owner_email"]);
    }

    $mail->isHTML(false);
    $mail->Subject = "TimeSteal received your message";
    $mail->Body =
        "Hello {$name},\n\n" .
        "Thanks for contacting TimeSteal. We received your message and will get back to you soon.\n\n" .
        "Your message:\n{$message}\n\n" .
        "Regards,\nTimeSteal";

    $mail->AltBody = $mail->Body;
    $mail->send();

    showMessage("Message Sent", "Your email was sent successfully to {$visitorEmail}.");
} catch (Exception $e) {
    showMessage("Mail Error", "The email could not be sent. PHPMailer said: " . $mail->ErrorInfo);
}
