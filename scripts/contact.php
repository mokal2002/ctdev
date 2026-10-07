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

// GET FORM DATA

// =====================================================



$fname   = trim($_POST['fname'] ?? '');

$lname   = trim($_POST['lname'] ?? '');

$email   = trim($_POST['email'] ?? '');

$phone   = trim($_POST['number'] ?? '');

$message = trim($_POST['messages'] ?? '');



$name = trim($fname . ' ' . $lname);





// =====================================================

// VALIDATION

// =====================================================



if (!$fname || !$lname || !$email || !$phone) {



    http_response_code(400);



    echo json_encode([

        "status" => "error",

        "message" => "Please fill in all required fields."

    ]);



    exit;

}





if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {



    http_response_code(400);



    echo json_encode([

        "status" => "error",

        "message" => "Please enter a valid email address."

    ]);



    exit;

}





if (!preg_match('/^[0-9]{10}$/', $phone)) {



    http_response_code(400);



    echo json_encode([

        "status" => "error",

        "message" => "Please enter a valid 10-digit phone number."

    ]);



    exit;

}





// =====================================================

// ESCAPE DATA FOR HTML EMAIL

// =====================================================



$safeName = htmlspecialchars(

    $name,

    ENT_QUOTES,

    'UTF-8'

);



$safeFname = htmlspecialchars(

    $fname,

    ENT_QUOTES,

    'UTF-8'

);



$safeEmail = htmlspecialchars(

    $email,

    ENT_QUOTES,

    'UTF-8'

);



$safePhone = htmlspecialchars(

    $phone,

    ENT_QUOTES,

    'UTF-8'

);



$safeMessage = htmlspecialchars(

    $message,

    ENT_QUOTES,

    'UTF-8'

);





// =====================================================

// MESSAGE

// =====================================================



$messageHtml = $message !== ''

    ? nl2br($safeMessage)

    : '<span style="color:#004958;font-style:italic;">No message was provided.</span>';





// =====================================================

// PHPMailer

// =====================================================



$mail = new PHPMailer(true);





try {



    // =================================================

    // SMTP SETTINGS

    // =================================================



    $mail->isSMTP();



    $mail->Host = 'smtp.gmail.com';



    $mail->SMTPAuth = true;



    // Gmail sending account

    $mail->Username = 'contact.crescenttechno@gmail.com';



    // Gmail App Password

    // Replace this with your actual App Password

    $mail->Password = 'vtdotbcohduazfpw';



    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;



    $mail->Port = 587;





    // =================================================

    // SENDER

    // =================================================



    $mail->setFrom(

        $mail->Username,

        'Crescent Technoserve Website'

    );





    // =================================================

    // REPLY TO VISITOR

    // =================================================



    $mail->addReplyTo(

        $email,

        $name

    );





    // =================================================

    // RECEIVER

    // =================================================



    // Testing email

    $mail->addAddress(

        'testhub582@gmail.com'

    );





    /*

    AFTER TESTING, CHANGE ABOVE TO:



    $mail->addAddress(

        'support@crescenttechnoserve.com'

    );

    */





    // =================================================

    // EMBED LOGO

    // =================================================



    $logoPath = __DIR__ . '/logo.png';



    $hasLogo = false;



    if (file_exists($logoPath)) {



        $mail->addEmbeddedImage(

            $logoPath,

            'logo',

            'logo.png'

        );



        $hasLogo = true;

    }





    // =================================================

    // EMAIL SETTINGS

    // =================================================



    $mail->isHTML(true);



    $mail->CharSet = 'UTF-8';



    $mail->Subject =

        'New Contact Form Enquiry - ' . $safeName;





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



<title>New Contact Form Enquiry</title>



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

<!-- OUTER WRAPPER -->

<!-- ================================================= -->



<table

role="presentation"

width="100%"

cellpadding="0"

cellspacing="0"

border="0"

style="

background-color:#FFFFFF;

padding:28px 10px;

"

>



<tr>



<td align="center">





<!-- ================================================= -->

<!-- MAIN EMAIL CONTAINER -->

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

"

>





<!-- ================================================= -->

<!-- TOP GREEN LINE -->

<!-- ================================================= -->



<tr>



<td

style="

height:4px;

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





<!-- LOGO -->



<td

width="48"

style="

width:48px;

vertical-align:middle;

"

>';



if ($hasLogo) {



    $mail->Body .= '



<img

src="cid:logo"

alt="Crescent Technoserve"

width="42"

height="42"

style="

display:block;

width:42px;

height:42px;

border:0;

"

>



';



} else {



    $mail->Body .= '



<div

style="

width:42px;

height:42px;

line-height:42px;

text-align:center;

background-color:#FFFFFF;

color:#004958;

font-size:20px;

font-weight:bold;

"

>

C

</div>



';



}





$mail->Body .= '



</td>





<!-- COMPANY NAME -->



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

margin-top:1px;

"

>

Website Contact Centre

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

New message received

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

Someone has submitted a new enquiry through your website.

</p>





<!-- ================================================= -->

<!-- CONTACT INFORMATION -->

<!-- ================================================= -->



<table

role="presentation"

width="100%"

cellpadding="0"

cellspacing="0"

border="0"

style="

margin-top:26px;

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





<!-- NAME ICON -->



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





<!-- NAME TEXT -->



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





<!-- EMAIL ICON -->



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





<!-- EMAIL TEXT -->



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

<!-- PHONE -->

<!-- ================================================= -->



<tr>



<td

style="

padding:17px 0;

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





<!-- PHONE ICON -->



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





<!-- PHONE TEXT -->



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

>



<a

href="tel:' . $safePhone . '"

style="

color:#004958;

text-decoration:none;

"

>

' . $safePhone . '

</a>



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

margin-top:8px;

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

Message

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

border-left:3px solid #004958;

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

border-radius:6px;

"

>

Reply to ' . $safeFname . ' &rarr;

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

background-color:#FFFFFF;

border-top:1px solid #004958;

padding:20px 30px;

text-align:center;

"

>





<div

style="

font-size:12px;

line-height:18px;

font-weight:bold;

color:#004958;

"

>

Crescent Technoserve

</div>





<div

style="

font-size:10.5px;

line-height:18px;

color:#004958;

margin-top:3px;

"

>

This is an automated notification from your website contact form.

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

color:#004958;

text-decoration:none;

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

        "message" => "Thanks! Your message has been sent."

    ]);





} catch (Exception $e) {



    http_response_code(500);



    error_log(

        "Contact form mail failed: " .

        $mail->ErrorInfo

    );





    echo json_encode([

        "status" => "error",

        "message" => "Something went wrong sending your message. Please try again."

    ]);

}



?>