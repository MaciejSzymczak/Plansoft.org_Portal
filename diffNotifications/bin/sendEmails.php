<?php

/*
 * Send notification about changes in time table
 * @version 2022.10.12
 * @author Maciej Szymczak
 */

require_once('..\conf\connection.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'E:/xampp/php/PHPMailer/src/Exception.php';
require 'E:/xampp/php/PHPMailer/src/PHPMailer.php';
require 'E:/xampp/php/PHPMailer/src/SMTP.php';


//---------------------------------------------------------------------------------------------
function writeToLog($message) {
	global $LOG_fileName;
	$myfile =fopen($LOG_fileName,"a") or die("Unable to open the file!");
	fwrite($myfile, date('Y-m-d H:i:s') ." " . $message . PHP_EOL);
	fclose($myfile);
}

//---------------------------------------------------------------------------------------------
function sendEmail($emailTo, $subject, $content)
{
	global $SMTP_Host;
	global $SMTP_Username;
	global $SMTP_Password;
	global $SMTP_Port;
	global $SMTP_From_Email; 
	global $SMTP_From_Name;        
	
	$mail = new PHPMailer(true);

	try {
		$mail->SMTPDebug = SMTP::DEBUG_SERVER;                     
		$mail->isSMTP();                                           
		$mail->Host       = $SMTP_Host;                     
		$mail->SMTPAuth   = true;                                  
		$mail->Username   = $SMTP_Username;                   
		$mail->Password   = $SMTP_Password;                     
		$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;        
		$mail->Port       = $SMTP_Port;
		//required by host prdexch01.wat.edu.pl (not required by mail.wat.edu.pl)
		$mail->SMTPOptions = [
        'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
        ]];
		
		//Recipients
		$mail->setFrom($SMTP_From_Email, $SMTP_From_Name);
		//$mail->addAddress('soft@home.pl');               //Name is optional
		$mail->addAddress('ext.mszy@wat.edu.pl');       
		$mail->addAddress('zbigniew.ciolek@wat.edu.pl');      
		$mail->addAddress('cezary.wisniewski@wat.edu.pl');     
		$mail->addAddress('zdzislaw.bociarski@wat.edu.pl');           
		//$mail->addReplyTo('no-reply@wat.edu.pl', 'Information');
		//$mail->addCC('bcc@example.com');
		//$mail->addBCC('bcc@example.com');

		//Attachments
		//$mail->addAttachment('test.jpg');    

		$mail->CharSet = 'UTF-8';
		$mail->isHTML(true);
		$mail->Subject = $subject;
		$mail->Body    = $content;
	    $mail->AltBody = $content;

		$mail->send();
		writeToLog('Message has been sent to '.$emailTo);
		sleep(1);
	} catch (Exception $e) {
		writeToLog("Message has NOT been sent to '.$emailTo.'. Mailer Error: {$mail->ErrorInfo}");
	}
}
  
//---------------------------------------------------------------------------------------------
function main() {
	writeToLog("*** START ***");
	global $username;
	global $password;
	global $database;
	
	$queryChanges = "
	select owner
		 , lec.email
		 , lec.title
		 , lec.first_name
		 , lec.last_name
		 , to_char(day,'yyyy-mm-dd') day
		 , from_hour
		 , from_min
		 , to_hour
		 , to_min
		 , sub.name sub_name
		 , frm.name frm_name
		 , calc_groups
		 , calc_rooms
		 , DIFF_CATCHER_HELPER.desc2
		 , rtrim(to_char(sum, 'FM0.99'), '.')  sum
		 , decode(diff_flag,'DELETE','Usunięcie','Wstawienie') diff_flag
	  from DIFF_CATCHER_HELPER
		 , lecturers lec
		 , subjects sub
		 , forms frm
	  where lec_id = lec.id
		and sub_id = sub.id(+)
		and for_id = frm.id
		and dim = 'DIFF' 
		and lec.email is not null
	  order by  
		   lec.Email
		 , lec.title
		 , lec.first_name
		 , lec.last_name
		 , owner
		 , day
		 , from_hour
		 , from_min
		 , to_hour
		 , to_min
		 , sub.name 
		 , frm.name 
		 , calc_groups
		 , calc_rooms
		 , DIFF_CATCHER_HELPER.desc2
		 , sum 
		 , diff_flag
	";
	//!!!	and lec.id in (select id from lecturers where diff_notifications = '+' and email is not null)
	 
	$connection = oci_connect($username, $password, $database);
	if (!$connection) {
		$m = oci_error();
		writeToLog( '*** Could not connect to database: '. $m['message'] );
	}
	 
	$parsedQChanges = oci_parse($connection, $queryChanges);
	if (!$parsedQChanges) {
		$m = oci_error($connection);
		writeToLog( '*** Could not parse statement: '. $m['message'] );
	}
	$executeResult = oci_execute($parsedQChanges);
	if (!$executeResult) {
		$m = oci_error($parsedQChanges);
		writeToLog( '*** Could not execute statement: '. $m['message'] );
	}

	$priorEmail = "";
	$firstEntry = true;  
	$tableHeader= '
	<!doctype html>
	<html>
	<html lang="en">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width">
	<title></title>
	<style></style>
	</head>
	
	<body>
	<div id="email" style="width:600px;">

	<!-- Header --> 
	<table role="presentation" border="0" cellspacing="0" width="100%">
	<tr>
	<td>
	</td>
	</tr>
	</table>

	<!-- Body --> 
	<table role="presentation" border="0" cellspacing="0" width="100%">
	<tr>
	<td>

	Witaj!<br/>
	<br/>
	*** TEST. ROZWIAZANIE JESZCZE NIE FUNKCJONUJE W PEŁNI, TYLKO NIEKTÓRE EMAILE SĄ WYSYŁANE. CIĄG DALSZY NASTĄPI ***
	
	Wprowadzono zmiany w Twoim rozkładzie zajęć.<br/>
	Ten email został wysłany automatycznie, prosimy na niego nie odpowiadać.<br/>
	W razie pytań prosimy o kontakt z Planistą.<br/>
	<br/>
	<table border="1" cellspacing="0"><tr>
	<th>Planista</th>
	<th>Tytuł</th>
	<th>Imię</th>
	<th>Nazwisko</th>
	<th>Dzień</th>
	<th>Godz.</th>
	<th>Godz. do</th>
	<th>Przedmiot</th>
	<th>Forma</th>
	<th>Grupa</th>
	<th>Sala</th>
	<th>Opis</th>
	<th>Suma</th>
	<th>Różnica</th></tr>'.PHP_EOL;
	$tableTail= '</table>
	
	</td> 
	</tr>
	</table>

	<!-- Footer -->
	<table role="presentation" border="0" cellspacing="0" width="100%">
	<tr>
	<td> 
	</td>
	</tr>
	</table> 
	</div>
	</body>	
	</html>
	
	'.PHP_EOL;
	$body = $tableHeader;
	while (($row = oci_fetch_array($parsedQChanges, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
		$currentEmail = $row['EMAIL'];
		if ($firstEntry) {
			$priorEmail = $currentEmail;
			$firstEntry = false;
		}
		if ($currentEmail == $priorEmail) {
			$body = $body  
				   . "<tr><td>"
				   . $row["OWNER"] 
				   . "</td><td>" . $row["TITLE"] 
				   . "</td><td>" . $row["FIRST_NAME"] 
				   . "</td><td>" . $row["LAST_NAME"] 
				   . "</td><td>" . $row["DAY"]
				   . "</td><td>" . $row["FROM_HOUR"] .":". $row["FROM_MIN"] 
				   . "</td><td>" . $row["TO_HOUR"] .":". $row["TO_MIN"] 
				   . "</td><td>" . $row["SUB_NAME"]  
				   . "</td><td>" . $row["FRM_NAME"]  
				   . "</td><td>" . $row["CALC_GROUPS"]  
				   . "</td><td>" . $row["CALC_ROOMS"]  
				   . "</td><td>" . $row["DESC2"] 
				   . "</td><td>" . $row["SUM"]   
				   . "</td><td>" . $row["DIFF_FLAG"] 
				   ."</td></tr>".PHP_EOL;
		} else {
			sendEmail($priorEmail, "Powiadomienie o zmianach w rozkładach zajęć", $body.$tableTail);
			$priorEmail = $currentEmail;
			$body = $tableHeader		
				   . "<tr><td>"
				   . $row["OWNER"] 
				   . "</td><td>" . $row["TITLE"] 
				   . "</td><td>" . $row["FIRST_NAME"] 
				   . "</td><td>" . $row["LAST_NAME"] 
				   . "</td><td>" . $row["DAY"]
				   . "</td><td>" . $row["FROM_HOUR"] .":". $row["FROM_MIN"] 
				   . "</td><td>" . $row["TO_HOUR"] .":". $row["TO_MIN"] 
				   . "</td><td>" . $row["SUB_NAME"]  
				   . "</td><td>" . $row["FRM_NAME"]  
				   . "</td><td>" . $row["CALC_GROUPS"]  
				   . "</td><td>" . $row["CALC_ROOMS"]  
				   . "</td><td>" . $row["DESC2"] 
				   . "</td><td>" . $row["SUM"]   
				   . "</td><td>" . $row["DIFF_FLAG"] 
				   ."</td></tr>".PHP_EOL;
		}
	}

	sendEmail($priorEmail, "Powiadomienie o zmianach w rozkładach zajęć", $body.$tableTail);
	writeToLog("*** STOP ***");
}

//========================================== Main ====================================================================
main();

?>
