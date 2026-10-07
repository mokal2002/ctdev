<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../PHPMailer/src/Exception.php';
require __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../PHPMailer/src/SMTP.php';

header('Content-Type: application/json');


// =====================================================
// ONLY ALLOW POST REQUEST
// =====================================================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    http_response_code(405);

    echo json_encode([
        "status" => "error",
        "message" => "Invalid request"
    ]);

    exit;
}


// =====================================================
// FORM DATA
// =====================================================

$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$jobRole = trim($_POST['orderby'] ?? '');
$phone   = trim($_POST['phone'] ?? '');
$message = trim($_POST['message'] ?? '');


// =====================================================
// REQUIRED FIELD VALIDATION
// =====================================================

if (!$name || !$email || !$message) {

    http_response_code(400);

    echo json_encode([
        "status" => "error",
        "message" => "Please fill in all required fields."
    ]);

    exit;
}


// =====================================================
// EMAIL VALIDATION
// =====================================================

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    http_response_code(400);

    echo json_encode([
        "status" => "error",
        "message" => "Please enter a valid email address."
    ]);

    exit;
}


// =====================================================
// CV VALIDATION
// =====================================================

if (
    !isset($_FILES['cv']) ||
    $_FILES['cv']['error'] !== UPLOAD_ERR_OK
) {

    http_response_code(400);

    echo json_encode([
        "status" => "error",
        "message" => "CV upload failed. Please attach a PDF or Word file."
    ]);

    exit;
}


$fileTmp  = $_FILES['cv']['tmp_name'];
$fileName = $_FILES['cv']['name'];
$fileSize = $_FILES['cv']['size'];


// Get file extension
$fileExtension = strtolower(
    pathinfo($fileName, PATHINFO_EXTENSION)
);


// Allowed CV formats
$allowedExtensions = [
    'pdf',
    'doc',
    'docx'
];


if (!in_array($fileExtension, $allowedExtensions, true)) {

    http_response_code(400);

    echo json_encode([
        "status" => "error",
        "message" => "Invalid CV format. Please upload PDF, DOC, or DOCX."
    ]);

    exit;
}


// Maximum 5 MB
$maxFileSize = 5 * 1024 * 1024;

if ($fileSize > $maxFileSize) {

    http_response_code(400);

    echo json_encode([
        "status" => "error",
        "message" => "CV file is too large. Maximum allowed size is 5 MB."
    ]);

    exit;
}


// =====================================================
// ESCAPE HTML DATA
// =====================================================

$safeName = htmlspecialchars(
    $name,
    ENT_QUOTES,
    'UTF-8'
);

$safeEmail = htmlspecialchars(
    $email,
    ENT_QUOTES,
    'UTF-8'
);

$safeJobRole = htmlspecialchars(
    $jobRole ?: 'Not specified',
    ENT_QUOTES,
    'UTF-8'
);

$safePhone = htmlspecialchars(
    $phone ?: 'Not provided',
    ENT_QUOTES,
    'UTF-8'
);

$safeMessage = htmlspecialchars(
    $message,
    ENT_QUOTES,
    'UTF-8'
);

$safeFileName = htmlspecialchars(
    $fileName,
    ENT_QUOTES,
    'UTF-8'
);


// =====================================================
// MESSAGE
// =====================================================

$messageHtml = $message !== ''
    ? nl2br($safeMessage)
    : 'No message was provided.';


// =====================================================
// PHPMailer
// =====================================================

$mail = new PHPMailer(true);


try {

    // =================================================
    // SMTP
    // =================================================

    $mail->isSMTP();

    $mail->Host = 'smtp.gmail.com';

    $mail->SMTPAuth = true;

    $mail->Username = 'contact.crescenttechno@gmail.com';

    // IMPORTANT:
    // Put your NEW Gmail App Password here.
    $mail->Password = 'vtdotbcohduazfpw';

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

    $mail->Port = 587;


    // =================================================
    // SENDER
    // =================================================

    $mail->setFrom(
        $mail->Username,
        'Crescent Technoserve Careers'
    );


    // =================================================
    // REPLY TO APPLICANT
    // =================================================

    $mail->addReplyTo(
        $email,
        $name
    );


    // =================================================
    // RECEIVER
    // =================================================

    // TESTING
    $mail->addAddress(
        'testhub582@gmail.com'
    );


    /*
    AFTER TESTING:

    $mail->addAddress(
        'support@crescenttechnoserve.com'
    );
    */


    // =================================================
    // EMBED LOGO
    // =================================================

    $logoPath = __DIR__ . '/logo.png';

    if (file_exists($logoPath)) {

        $mail->addEmbeddedImage(
            $logoPath,
            'logo',
            'logo.png'
        );

    }


    // =================================================
    // ATTACH CV
    // =================================================

    $mail->addAttachment(
        $fileTmp,
        $fileName
    );


    // =================================================
    // EMAIL SETTINGS
    // =================================================

    $mail->isHTML(true);

    $mail->CharSet = 'UTF-8';

    $mail->Encoding = 'base64';

    $mail->Subject =
        'Job Application - ' .
        $safeJobRole .
        ' - ' .
        $safeName;


    // =================================================
    // EMAIL BODY
    // =================================================

    $mail->Body = '

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>New Job Application</title>

</head>


<body
style="
margin:0;
padding:0;
background-color:#FFFFFF;
font-family:Arial,Helvetica,sans-serif;
-webkit-text-size-adjust:100%;
"
>


<!-- ================================================= -->
<!-- OUTER TABLE -->
<!-- ================================================= -->

<table
role="presentation"
width="100%"
cellpadding="0"
cellspacing="0"
border="0"
style="
background-color:#FFFFFF;
padding:20px 10px;
"
>

<tr>

<td align="center">


<!-- ================================================= -->
<!-- MAIN CONTAINER -->
<!-- ================================================= -->

<table
role="presentation"
width="600"
cellpadding="0"
cellspacing="0"
border="0"
style="
width:600px;
max-width:600px;
background-color:#FFFFFF;
border:1px solid #004958;
"
>


<!-- ================================================= -->
<!-- TOP BORDER -->
<!-- ================================================= -->

<tr>

<td
style="
height:5px;
background-color:#004958;
font-size:0;
line-height:0;
"
>
&nbsp;
</td>

</tr>


<!-- ================================================= -->
<!-- HEADER -->
<!-- ================================================= -->

<tr>

<td
style="
background-color:#004958;
padding:28px 30px;
"
>


<table
role="presentation"
width="100%"
cellpadding="0"
cellspacing="0"
border="0"
>

<tr>


<!-- ================================================= -->
<!-- LOGO -->
<!-- ================================================= -->

<td
width="52"
style="
width:52px;
vertical-align:middle;
"
>

<img
src="cid:logo"
alt="Crescent Technoserve"
width="44"
height="44"
style="
display:block;
width:44px;
height:44px;
border:0;
outline:none;
text-decoration:none;
"
>

</td>


<!-- ================================================= -->
<!-- COMPANY -->
<!-- ================================================= -->

<td
style="
vertical-align:middle;
padding-left:14px;
"
>

<div
style="
font-size:20px;
line-height:26px;
font-weight:bold;
color:#FFFFFF;
"
>
Crescent Technoserve
</div>


<div
style="
font-size:11px;
line-height:17px;
color:#FFFFFF;
margin-top:2px;
"
>
Career Application Centre
</div>

</td>


</tr>

</table>


</td>

</tr>


<!-- ================================================= -->
<!-- CONTENT -->
<!-- ================================================= -->

<tr>

<td
style="
background-color:#FFFFFF;
padding:34px 30px 32px;
"
>


<!-- ================================================= -->
<!-- TITLE -->
<!-- ================================================= -->

<h1
style="
margin:0 0 7px 0;
padding:0;
font-size:24px;
line-height:32px;
font-weight:700;
color:#004958;
font-family:Arial,Helvetica,sans-serif;
"
>
New job application received
</h1>


<!-- DESCRIPTION -->

<p
style="
margin:0;
padding:0;
font-size:13px;
line-height:21px;
color:#004958;
font-family:Arial,Helvetica,sans-serif;
"
>
A candidate has submitted a new application through your careers page.
</p>


<!-- ================================================= -->
<!-- INFORMATION -->
<!-- ================================================= -->

<table
role="presentation"
width="100%"
cellpadding="0"
cellspacing="0"
border="0"
style="
margin-top:27px;
"
>


<!-- ================================================= -->
<!-- NAME -->
<!-- ================================================= -->

<tr>

<td
style="
padding:0 0 17px 0;
border-bottom:1px solid #004958;
"
>

<table
role="presentation"
width="100%"
cellpadding="0"
cellspacing="0"
border="0"
>

<tr>


<td
width="38"
style="
width:38px;
vertical-align:middle;
font-size:19px;
color:#004958;
font-family:Arial,Helvetica,sans-serif;
"
>
◉
</td>


<td
style="
vertical-align:middle;
"
>

<div
style="
font-size:10px;
line-height:15px;
font-weight:bold;
color:#004958;
letter-spacing:0.8px;
text-transform:uppercase;
"
>
Full Name
</div>


<div
style="
font-size:14px;
line-height:21px;
font-weight:600;
color:#004958;
"
>
' . $safeName . '
</div>

</td>


</tr>

</table>

</td>

</tr>


<!-- ================================================= -->
<!-- EMAIL -->
<!-- ================================================= -->

<tr>

<td
style="
padding:17px 0;
border-bottom:1px solid #004958;
"
>

<table
role="presentation"
width="100%"
cellpadding="0"
cellspacing="0"
border="0"
>

<tr>


<td
width="38"
style="
width:38px;
vertical-align:middle;
font-size:19px;
color:#004958;
font-family:Arial,Helvetica,sans-serif;
"
>
✉
</td>


<td
style="
vertical-align:middle;
"
>

<div
style="
font-size:10px;
line-height:15px;
font-weight:bold;
color:#004958;
letter-spacing:0.8px;
text-transform:uppercase;
"
>
Email Address
</div>


<div
style="
font-size:14px;
line-height:21px;
font-weight:600;
"
>

<a
href="mailto:' . $safeEmail . '"
style="
color:#004958;
text-decoration:none;
"
>
' . $safeEmail . '
</a>

</div>

</td>


</tr>

</table>

</td>

</tr>


<!-- ================================================= -->
<!-- JOB ROLE -->
<!-- ================================================= -->

<tr>

<td
style="
padding:17px 0;
border-bottom:1px solid #004958;
"
>

<table
role="presentation"
width="100%"
cellpadding="0"
cellspacing="0"
border="0"
>

<tr>


<td
width="38"
style="
width:38px;
vertical-align:middle;
font-size:19px;
color:#004958;
font-family:Arial,Helvetica,sans-serif;
"
>
▣
</td>


<td
style="
vertical-align:middle;
"
>

<div
style="
font-size:10px;
line-height:15px;
font-weight:bold;
color:#004958;
letter-spacing:0.8px;
text-transform:uppercase;
"
>
Position Applied For
</div>


<div
style="
font-size:14px;
line-height:21px;
font-weight:600;
color:#004958;
"
>
' . $safeJobRole . '
</div>

</td>


</tr>

</table>

</td>

</tr>


<!-- ================================================= -->
<!-- PHONE -->
<!-- ================================================= -->

<tr>

<td
style="
padding:17px 0;
border-bottom:1px solid #004958;
"
>

<table
role="presentation"
width="100%"
cellpadding="0"
cellspacing="0"
border="0"
>

<tr>


<td
width="38"
style="
width:38px;
vertical-align:middle;
font-size:19px;
color:#004958;
font-family:Arial,Helvetica,sans-serif;
"
>
☎
</td>


<td
style="
vertical-align:middle;
"
>

<div
style="
font-size:10px;
line-height:15px;
font-weight:bold;
color:#004958;
letter-spacing:0.8px;
text-transform:uppercase;
"
>
Phone Number
</div>


<div
style="
font-size:14px;
line-height:21px;
font-weight:600;
color:#004958;
"
>';

if ($phone !== '') {

    $mail->Body .= '

<a
href="tel:' . $safePhone . '"
style="
color:#004958;
text-decoration:none;
"
>
' . $safePhone . '
</a>

';

} else {

    $mail->Body .= '

Not provided

';

}

$mail->Body .= '

</div>

</td>


</tr>

</table>

</td>

</tr>


</table>


<!-- ================================================= -->
<!-- MESSAGE -->
<!-- ================================================= -->

<div
style="
margin-top:26px;
"
>


<div
style="
font-size:10px;
line-height:15px;
font-weight:bold;
color:#004958;
letter-spacing:0.8px;
text-transform:uppercase;
margin-bottom:9px;
"
>
Cover Message
</div>


<table
role="presentation"
width="100%"
cellpadding="0"
cellspacing="0"
border="0"
>

<tr>

<td
style="
background-color:#FFFFFF;
border:1px solid #004958;
padding:15px 16px;
font-size:14px;
line-height:23px;
color:#004958;
"
>

' . $messageHtml . '

</td>

</tr>

</table>


</div>


<!-- ================================================= -->
<!-- CV -->
<!-- ================================================= -->

<div
style="
margin-top:22px;
"
>


<table
role="presentation"
width="100%"
cellpadding="0"
cellspacing="0"
border="0"
style="
border:1px solid #004958;
"
>

<tr>

<td
style="
padding:14px 15px;
"
>


<table
role="presentation"
cellpadding="0"
cellspacing="0"
border="0"
>

<tr>


<td
width="34"
style="
width:34px;
vertical-align:middle;
font-size:20px;
color:#004958;
font-family:Arial,Helvetica,sans-serif;
"
>
▤
</td>


<td
style="
vertical-align:middle;
"
>

<div
style="
font-size:10px;
font-weight:bold;
letter-spacing:0.8px;
text-transform:uppercase;
color:#004958;
"
>
Resume / CV Attached
</div>


<div
style="
font-size:13px;
font-weight:600;
color:#004958;
margin-top:3px;
"
>
' . $safeFileName . '
</div>

</td>


</tr>

</table>


</td>

</tr>

</table>


</div>


<!-- ================================================= -->
<!-- REPLY BUTTON -->
<!-- ================================================= -->

<div
style="
margin-top:27px;
"
>

<a
href="mailto:' . $safeEmail . '"
style="
display:inline-block;
background-color:#004958;
color:#FFFFFF;
font-size:13px;
font-weight:bold;
text-decoration:none;
padding:12px 20px;
border:1px solid #004958;
"
>
Reply to ' . $safeName . ' &rarr;
</a>

</div>


</td>

</tr>


<!-- ================================================= -->
<!-- FOOTER -->
<!-- ================================================= -->

<tr>

<td
style="
background-color:#004958;
padding:20px 30px;
text-align:center;
"
>


<div
style="
font-size:12px;
line-height:18px;
font-weight:bold;
color:#FFFFFF;
"
>
Crescent Technoserve
</div>


<div
style="
font-size:10.5px;
line-height:18px;
color:#FFFFFF;
margin-top:3px;
"
>
This is an automated notification from your careers page.
</div>


<div
style="
font-size:10.5px;
line-height:18px;
margin-top:2px;
"
>

<a
href="https://www.crescenttechnoserve.com"
style="
color:#FFFFFF;
text-decoration:none;
font-weight:bold;
"
>
crescenttechnoserve.com
</a>

</div>


</td>

</tr>


</table>


</td>

</tr>

</table>


</body>

</html>

';


    // =================================================
    // SEND EMAIL
    // =================================================

    $mail->send();


    echo json_encode([
        "status" => "success",
        "message" => "Application submitted successfully!"
    ]);


} catch (Exception $e) {

    http_response_code(500);

    error_log(
        "Apply form mail failed: " .
        $mail->ErrorInfo
    );


    echo json_encode([
        "status" => "error",
        "message" => "Something went wrong submitting your application. Please try again."
    ]);
}

?>