<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 'On');

	$db_username = "planner";
	$db_password = "..."; 
	$db_database =	"//localhost:1521/xe";	

	// -------------------------------------------------------------------------------------
	
	function debug ($message) {
		$myfile =fopen('../secret/log.log',"a") or die("Unable to open file!");
		$t=time();
		//c = ISO format. 
		fwrite($myfile, date("c",$t) .' '. session_id() .' '. $message . PHP_EOL);
		fclose($myfile);	
	}		
	
	function myErrorHandler($errno, $errstr, $errfile, $errline)
	{
		//$errno = error level, here is complete list:  http://php.net/manual/en/errorfunc.constants.php
		$errlevel = array();
		$errlevel[1]='E_ERROR';
		$errlevel[2]='E_WARNING';
		$errlevel[4]='E_PARSE';
		$errlevel[8]='E_NOTICE';
		$errlevel[16]='E_CORE_ERROR';
		$errlevel[32]='E_CORE_WARNING';
		$errlevel[64]='E_COMPILE_ERROR';
		$errlevel[128]='E_COMPILE_WARNING';
		$errlevel[256]='E_USER_ERROR';
		$errlevel[512]='E_USER_WARNING';
		$errlevel[1024]='E_USER_NOTICE';
		$errlevel[2048]='E_STRICT';
		$errlevel[4096]='E_RECOVERABLE_ERROR';
		$errlevel[8192]='E_DEPRECATED';
		$errlevel[16384]='E_USER_DEPRECATED';
		$errlevel[32767]='E_ALL';

		debug( sprintf("LEVEL: %s, MESSAGE: %s, FILE: %s, LINE: %s", $errlevel[$errno], $errstr, $errfile, $errline) );	
		// false = call standard PHP error handler
		return false;
	}

	$old_error_handler = set_error_handler("myErrorHandler");
	

												
?>