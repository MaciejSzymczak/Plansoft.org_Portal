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
function containsKey($map, $keyValue) {
	foreach($map as $x => $x_value) {
	  if ($x == $keyValue) return true;
	}
    return false;	 
}

//---------------------------------------------------------------------------------------------
function keySet($map) {
	$res = array();
	foreach($map as $x => $x_value) {
	  array_push($res, $x);
	}
	return $res;
}


//---------------------------------------------------------------------------------------------
function containsIgnoreCase ($str , $searchFor) {
  $str = strtoupper($str);
  $searchFor = strtoupper($searchFor);
  if ( strlen(strpos($str, $searchFor))==0) { return false;} 
  else { return true; }  
}


//---------------------------------------------------------------------------------------------
function blockingRule ($ruleName, $user, $allowKeyWord, $owners, $emailTo, $content) {
	if ( strtoupper($emailTo)==strtoupper($user) ) { 
		if ( containsIgnoreCase($content,$allowKeyWord)==false ) { 
			writeToLog( '***| Message blocked by the rule: '.$ruleName.' EmailTo:'.$emailTo); return true; 
		} else {
			writeToLog( '***| Message allowed by the rule: '.$ruleName.' EmailTo:'.$emailTo); return false; 			
		}
	}
	return false;
}

//---------------------------------------------------------------------------------------------
function sendEmail($emailTo, $subject, $content, $owners)
{
	//Test mode
	$blockEmails = false;

	//blocking rules
	if (blockingRule ('FIZYKA_ONLY', 'urszula.chodorow@wat.edu.pl', 'FIZYKA', $owners, $emailTo, $content)) return;
		
	//cc Rules
	$ccRules = [
	  "JJABLONSKI"      => 'jaroslaw.jablonski@wat.edu.pl'
	, "WEL_ZBOCIARSKI1" => 'zdzislaw.bociarski@wat.edu.pl,magdalena.ponurska@wat.edu.pl'
	, "WEL_ZBOCIARSKI2" => 'zdzislaw.bociarski@wat.edu.pl,magdalena.ponurska@wat.edu.pl'
	, "WEL_MPONURSKA1"  => 'zdzislaw.bociarski@wat.edu.pl,magdalena.ponurska@wat.edu.pl'
	, "WEL_MPONURSKA2"  => 'zdzislaw.bociarski@wat.edu.pl,magdalena.ponurska@wat.edu.pl'
	, "BPLATA"          => 'boguslaw.plata@wat.edu.pl,michal.szymerski@wat.edu.pl'
	, "MSZYMERSKI"      => 'boguslaw.plata@wat.edu.pl,michal.szymerski@wat.edu.pl'
	, "JJABLONSKI"      => 'jaroslaw.jablonski@wat.edu.pl'
	, "CWISNIEWSKI"     => 'cezary.wisniewski@wat.edu.pl'
	, "SWDOWIAK"        => 'stanislaw.wdowiak@wat.edu.pl'
	];
	
	global $SMTP_Host;
	global $SMTP_Auth;        
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
		$mail->SMTPAuth   = $SMTP_Auth;                                  
		$mail->Username   = $SMTP_Username;                   
		$mail->Password   = $SMTP_Password;                     
		$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;        
		$mail->Port       = $SMTP_Port;
		$mail->SMTPOptions = [
        'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
        ]];
		
		//Recipients
		$mail->setFrom($SMTP_From_Email, $SMTP_From_Name);
		//addAddress
		$emailTo = str_replace(";",",",$emailTo);
		$emailTo = str_replace(" ","",$emailTo);
		foreach(explode(",",$emailTo) as $email) {
		   $mail->addAddress($email);               //Name is optional
		}
		$mail->addBCC('ext.mszy@wat.edu.pl');       
		$mail->addBCC('zbigniew.ciolek@wat.edu.pl');         

		//cc Rules
		foreach($ccRules as $key => $value) {
			if (containsKey($owners,$key)  ) {
				$ccEmails = explode(',', $value );
					foreach($ccEmails as $ccEmail) {
						$mail->addCC($ccEmail);  
						writeToLog('CC rule applied. Owner:'.$key.' ccAddress:'.$ccEmail);						
					}
			}
		}
		
		//$mail->addReplyTo('no-reply@wat.edu.pl', 'Information');
		//$mail->addCC('bcc@example.com');

		//Attachments
		//$mail->addAttachment('test.jpg');    

		$mail->CharSet = 'UTF-8';
		$mail->isHTML(true);
		$mail->Subject = $subject;
		$mail->Body    = $content;
	    $mail->AltBody = $content;
		
		if ($blockEmails == false) {
			$mail->send();			
		}
		
		writeToLog( Implode(keySet($owners),',').'| Message has been sent to '.$emailTo);
		//sleep(1);
	} catch (Exception $e) {
		writeToLog( Implode(keySet($owners),',').'| Message has NOT been sent to '.$emailTo.' Mailer Error: ' . $mail->ErrorInfo);
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
		and lec.id in (select id from lecturers where diff_notifications = '+' and email is not null)
		and sub.diff_notifications = '+'
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

	Dzień dobry,<br/>
	<br/>
	Pani/Pana rozkład zajęć uległ zmianie w zakresie widocznym poniżej.<br/>
	W razie jakichkolwiek pytań lub zastrzeżeń prosimy o bezpośredni kontakt z Planistą.<br/>
	Wiadomość została wygenerowana automatycznie, <strong>prosimy na nią nie odpowiadać</strong>.<br/>
	
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
	<small><span style="font-size:12px; color:#bdc3c7;">Email został wygenerowany za pomocą oprogramowania  <a href="http://www.plansoft.org/">plansoft.org</a></span></small>
	</td>
	</tr>
	</table> 
	</div>
	</body>	
	</html>
	
	'.PHP_EOL;
	$body = $tableHeader;
	$owners = [];
	while (($row = oci_fetch_array($parsedQChanges, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
		$currentEmail = $row['EMAIL'];
		if ($firstEntry) {
			$priorEmail = $currentEmail;
			$firstEntry = false;
		}
		if ($currentEmail == $priorEmail) {
			$owners[ $row["OWNER"] ] = 'YES';
			$body = $body  
				   . "<tr><td>"  . $row["OWNER"] 
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
			sendEmail($priorEmail, "Powiadomienie o zmianach w rozkładach zajęć", $body.$tableTail, $owners);
			$owners = [];
			$priorEmail = $currentEmail;
			$owners[ $row["OWNER"] ] = 'YES';
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

	if ($firstEntry==false) {
		sendEmail($priorEmail, "Powiadomienie o zmianach w rozkładach zajęć", $body.$tableTail, $owners);
	}
	writeToLog("*** STOP ***");
}

//========================================== Main ====================================================================
main();

?>
