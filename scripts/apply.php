<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit("Invalid request");
}

// -------- FORM DATA --------
$name    = $_POST['name'] ?? '';
$email   = $_POST['email'] ?? '';
$country = $_POST['orderby'] ?? '';
$website = $_POST['website'] ?? '';
$message = $_POST['message'] ?? '';

if (!$name || !$email || !$message) {
    exit("Required fields missing");
}

// -------- FILE --------
if (!isset($_FILES['cv']) || $_FILES['cv']['error'] !== 0) {
    exit("CV upload failed");
}

$fileTmp  = $_FILES['cv']['tmp_name'];
$fileName = $_FILES['cv']['name'];

// -------- MAIL --------
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'contact.crescenttechno@gmail.com';   // 🔴 your email
    $mail->Password   = 'vtdotbcohduazfpw';     // 🔴 Gmail App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom($email, $name);
    $mail->addAddress('support@crescenttechnoserve.com');     // 🔴 receive here

    $mail->addAttachment($fileTmp, $fileName);

    $mail->isHTML(true);
    $mail->Subject = "Job Application - $name";
    $mail->Body    = "
        <h3>New Job Application</h3>
        <p><strong>Name:</strong> $name</p>
        <p><strong>Email:</strong> $email</p>
        <p><strong>Country:</strong> $country</p>
        <p><strong>Portfolio:</strong> $website</p>
        <p><strong>Message:</strong><br>$message</p>
    ";

    $mail->send();
    echo "success";

} catch (Exception $e) {
    echo "Mailer Error: {$mail->ErrorInfo}";
}