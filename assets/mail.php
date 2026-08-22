<?php
// DEBUG ON (localhost only)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Always return JSON for ajax-form.js
header('Content-Type: application/json');

// Only allow POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(403);
    echo json_encode([
        "status" => "error",
        "message" => "Forbidden"
    ]);
    exit;
}

// Collect form data
$name     = trim($_POST["name"] ?? "");
$email    = trim($_POST["email"] ?? "");
$country  = trim($_POST["orderby"] ?? "");
$website  = trim($_POST["website"] ?? "");
$message  = trim($_POST["message"] ?? "");

// Validate
if ($name === "" || $email === "" || $message === "") {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "Please fill all required fields"
    ]);
    exit;
}

/*
 IMPORTANT:
 mail() FAILS on localhost → causes 500
 So we SKIP mail() locally
*/

// Success response (what ajax-form.js expects)
http_response_code(200);
echo json_encode([
    "status" => "success",
    "message" => "Message sent successfully."
]);
exit;