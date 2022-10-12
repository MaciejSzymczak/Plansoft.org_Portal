<?php

require_once('..\conf\connection.php');

error_reporting(-1);
ini_set('display_errors', 'On');

function sendEmail($recipient, $subject, $content, $from)
{
    $header = "From: $from" . "\r\n";
	$header .= "Mime-Version: 1.0" . "\r\n";
	$header .= "Content-type: text/html; charset=UTF-8\r\n";
		 
	$subject='=?UTF-8?B?'.base64_encode($subject).'?=';
	if (mail($recipient, $subject, $content, $header))
    {
        return true;
    } else
    {
        return false;
    }
    return false;
}

echo "before";
sendEmail($SMTP_Username,"Example Subject: Lubię jeść łąka jaźń","Example Body: Lubię jeść łąka jaźń",$SMTP_Username);
echo "after";

?>
