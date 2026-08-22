<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit("Invalid request");
}

// -------- VERIFY RECAPTCHA --------
$secretKey = "6Ld_SWssAAAAAHIzCfOpE8S3cmJ7-hGkM4YlzG4q";
$recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';

if (!$recaptchaResponse) {
    exit("Please complete reCAPTCHA");
}

$verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$recaptchaResponse");
$responseData = json_decode($verify);

if (!$responseData->success) {
    exit("reCAPTCHA verification failed");
}

// -------- SANITIZE --------
$fname   = htmlspecialchars($_POST['fname'] ?? '');
$lname   = htmlspecialchars($_POST['lname'] ?? '');
$email   = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
$number  = htmlspecialchars($_POST['number'] ?? '');
$message = htmlspecialchars($_POST['messages'] ?? '');

if (!$fname || !$email || !$number) {
    exit("Required fields missing");
}

// -------- ADMIN MAIL --------
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'contact.crescenttechno@gmail.com';
    $mail->Password   = 'vtdotbcohduazfpw';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('contact.crescenttechno@gmail.com', 'Website Contact');
    $mail->addReplyTo($email, $fname);
    $mail->addAddress('support@crescenttechnoserve.com');

    $mail->isHTML(true);
    $mail->Subject = "New Contact Message from $fname";
    $mail->Body    = "
        <h3>New Contact Message</h3>
        <p><strong>Name:</strong> $fname $lname</p>
        <p><strong>Email:</strong> $email</p>
        <p><strong>Phone:</strong> $number</p>
        <p><strong>Message:</strong><br>$message</p>
    ";

    $mail->send();

    // -------- AUTO REPLY --------
    $autoReply = new PHPMailer(true);

    $autoReply->isSMTP();
    $autoReply->Host       = 'smtp.gmail.com';
    $autoReply->SMTPAuth   = true;
    $autoReply->Username   = 'contact.crescenttechno@gmail.com';
    $autoReply->Password   = 'vtdotbcohduazfpw';
    $autoReply->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $autoReply->Port       = 587;

    $autoReply->setFrom('contact.crescenttechno@gmail.com', 'Crescent Technoserve');
    $autoReply->addAddress($email, $fname);

    $autoReply->isHTML(true);
    $autoReply->Subject = "Thank You for Contacting Us";
    $autoReply->Body    = "
        <h3>Hello $fname 👋</h3>
        <p>Thank you for contacting us.</p>
        <p>We have received your message and will respond shortly.</p>
        <br>
        <p><strong>Best Regards</strong><br>Crescent Technoserve</p>
    ";

    $autoReply->send();

    echo "success";

} catch (Exception $e) {
    echo "Mailer Error: {$mail->ErrorInfo}";
}