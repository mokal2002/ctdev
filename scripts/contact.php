<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit;
}

// -------- FORM DATA (sanitized) --------
$name    = trim(htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8'));
$email   = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
$jobRole = trim(htmlspecialchars($_POST['orderby'] ?? '', ENT_QUOTES, 'UTF-8'));
$phone   = trim(htmlspecialchars($_POST['phone'] ?? '', ENT_QUOTES, 'UTF-8'));
$message = trim(htmlspecialchars($_POST['message'] ?? '', ENT_QUOTES, 'UTF-8'));

if (!$name || !$email || !$message || !$jobRole) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Please fill in all required fields."]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Please enter a valid email address."]);
    exit;
}

// -------- FILE (validated) --------
if (!isset($_FILES['cv']) || $_FILES['cv']['error'] !== 0) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "CV upload failed or missing."]);
    exit;
}

$fileTmp  = $_FILES['cv']['tmp_name'];
$fileName = $_FILES['cv']['name'];
$fileSize = $_FILES['cv']['size'];
$allowedExt = ['pdf', 'doc', 'docx'];
$ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

if (!in_array($ext, $allowedExt)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Only PDF, DOC, or DOCX files are allowed."]);
    exit;
}

if ($fileSize > 5 * 1024 * 1024) { // 5MB limit
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "File is too large — max 5MB."]);
    exit;
}

// -------- MAIL --------
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = getenv('contact.crescenttechno@gmail.com');   // moved out of source
    $mail->Password   = getenv('vtdotbcohduazfpw'); // moved out of source
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom($mail->Username, 'Career Applications'); // use YOUR address as From
    $mail->addReplyTo($email, $name); // so replying goes to the applicant
    $mail->addAddress('support@crescenttechnoserve.com');

    $mail->addAttachment($fileTmp, $fileName);

    $mail->isHTML(true);
    $mail->Subject = "Job Application - $jobRole - $name";
    $mail->Body    = "
        <h3>New Job Application</h3>
        <p><strong>Position:</strong> $jobRole</p>
        <p><strong>Name:</strong> $name</p>
        <p><strong>Email:</strong> $email</p>
        <p><strong>Phone:</strong> $phone</p>
        <p><strong>Message:</strong><br>" . nl2br($message) . "</p>
    ";

    $mail->send();
    echo json_encode(["status" => "success", "message" => "Application submitted successfully!"]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Something went wrong sending your application. Please try again."]);
}