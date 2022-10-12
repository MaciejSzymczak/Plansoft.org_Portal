<?php

require_once('..\conf\connection.php');

//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'E:/xampp/php/PHPMailer/src/Exception.php';
require 'E:/xampp/php/PHPMailer/src/PHPMailer.php';
require 'E:/xampp/php/PHPMailer/src/SMTP.php';
//Load Composer's autoloader
//require 'vendor/autoload.php';

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = $SMTP_Host;                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = $SMTP_Username;                     //SMTP username
    $mail->Password   = $SMTP_Password;                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
    $mail->Port       = $SMTP_Port;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom($SMTP_From_Email, 'Test name');
    $mail->addAddress('soft@home.pl','Name');               //Name is optional
    $mail->addAddress('soft@home.pl','Name');               //Name is optional
    //$mail->addReplyTo('no-reply@example.com', 'Information');
    //$mail->addCC('bcc@example.com');
    //$mail->addBCC('bcc@example.com');

    //Attachments
    //$mail->addAttachment('test.jpg');    
    //$mail->addAttachment('sendmail.txt');
    //Content
    $mail->CharSet = 'UTF-8';
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = 'TEST';
    $mail->Body    = '<html>
<head><meta http-equiv=Content-Type content="text/html; charset=utf-8">
</head>
<body> 
TEST polskich znakow diakrytycznych źródło żrebię jaźń jeść
<br>
</body> 
</html>';

  $mail->AltBody = '--';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>
