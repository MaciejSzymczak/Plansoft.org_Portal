<?php


//cd c:\plansoft.org_DiffNotif\bin
//c:\xampp\php\php.exe testSendEmailPHPMAILER.php


require_once('..\conf\connection.php');


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


require 'C:/xampp/php/PHPMailer/src/Exception.php';
require 'C:/xampp/php/PHPMailer/src/PHPMailer.php';
require 'C:/xampp/php/PHPMailer/src/SMTP.php';


error_reporting(-1);
ini_set('display_errors', 'On');


function sendEmail($emailTo, $subject, $content)
{
	global $SMTP_Host;
	global $SMTP_Auth;        
	global $SMTP_Username;
	global $SMTP_Password;
	global $SMTP_Port;
	global $SMTP_From_Email; 
	global $SMTP_From_Name;        
	
	$mail = new PHPMailer(true);


		$mail->SMTPDebug = SMTP::DEBUG_SERVER;                     
		$mail->isSMTP();                                           
		$mail->Host       = $SMTP_Host;                     
		$mail->SMTPAuth   = $SMTP_Auth;                                  
		$mail->Username   = $SMTP_Username;                   
		$mail->Password   = $SMTP_Password;                     
		$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;        
		$mail->Port       = $SMTP_Port;
		//$mail->SMTPOptions = [
        //'ssl' => [
        //        'verify_peer' => false,
        //        'verify_peer_name' => false
        //]];
		
		//Recipients
		$mail->setFrom($SMTP_From_Email, $SMTP_From_Name);
		$mail->addAddress($emailTo);               //Name is optional
       
		
		//$mail->addReplyTo('no-reply@wat.edu.pl', 'Information');
		//$mail->addCC('bcc@example.com');


		//Attachments
		//$mail->addAttachment('test.jpg');    


		$mail->CharSet = 'UTF-8';
		$mail->isHTML(true);
		$mail->Subject = $subject;
		$mail->Body    = $content;
	    $mail->AltBody = $content;
		print "*******************************************************";
		$mail->send();			
}




echo "before";
sendEmail("soft@home.pl","Example Subject: Lubię jeść łąka jaźń","Example Body: Lubię jeść łąka jaźń");
echo "after";


?>
