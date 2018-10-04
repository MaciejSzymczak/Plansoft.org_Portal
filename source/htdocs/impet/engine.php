<?php Require_once 'impet/dbconnection.php'; ?>

<?php

/*example use: 
 *checkAccess('');
 *checkAccess('#localhost#index.php');
 */

 function checkAccess ($pagesFrom) {
    $loginPage = 'index.php';
	//session must be initiated by login window
	if (!isset($_SESSION['isActive'])) {
		header('Location: '.$loginPage);
		exit();
	}

	if (!empty($pagesFrom)) {
		if (!isset($_SERVER['HTTP_REFERER'])) {
			$referer = basename($_SERVER['PHP_SELF']);
		} else {
			$referer = basename($_SERVER['HTTP_REFERER']);
		}
		if (strpos($pagesFrom.'#'.basename($_SERVER['PHP_SELF']), $referer) === false ) {
			//go to login page
			header('Location: '.$loginPage);
			exit();
		}
	}
}

//https://stackoverflow.com/questions/520237/how-do-i-expire-a-php-session-after-30-minutes
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
    // last request was more than 30 minutes ago
	debug('Last request was more than 30 minutes ago => session_destroy() ---');
    session_unset();   
    session_destroy(); 
}
$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp

//avoid attacks on sessions like session fixation:
if (!isset($_SESSION['CREATED'])) {
    $_SESSION['CREATED'] = time();
} else if (time() - $_SESSION['CREATED'] > 1800) {
    // session started more than 30 minutes ago
	debug('Session id regenerated: session_regenerate_id  ---');
    session_regenerate_id(true);    // change session ID for the current session and invalidate old session ID
	debug('Done');
    $_SESSION['CREATED'] = time();  // update creation time
}

?>

<?php
	if(isset($_POST['command']) && $_POST['command']=='COMMAND_LOGOUT'){
		debug('Logout: session_destroy()');
		session_unset();  
		session_destroy();
		//header('Location: index.php');
		//exit();
	}
?>

<div class="container">
  <div class="row">
    <div class="col-sm-6">
		<table>
			<tr>
				<td> <img src="img/cube.jpg" style="height: 80px; width: 80px;"/> </td>
				<td>&nbsp;</td>
				<td class="page-header text-center"><h4><a href="http://plansoft.org" class="card-link">Plansoft.org</h4></a> <p>Planowanie zajęć i dyżurów</p></td>
			</tr>
		</table>
	</div>
    <div class="col-sm-6 text-right">
	
		<form id="logoutForm" action="<?php echo basename($_SERVER['PHP_SELF']); ?>" method="POST">
			<input type="hidden" id="command" name='command' value="COMMAND_LOGOUT"/>
		</form>  
		<button type="button" class="btn btn-link" onclick="self.location.href=('./changePassword.php');" <?php if (basename($_SERVER['PHP_SELF'])=='changePassword.php') echo  'style="display:none"'; ?>  >Zmiana hasła</button>
		<button type="button" class="btn btn-link" onclick="$('#logoutForm').submit();" <?php if (basename($_SERVER['PHP_SELF'])=='changePassword.php') echo  'style="display:none"'; ?> >Wyloguj</button>
		<!--button type="button" class="btn btn-link" onclick="window.location.href = 'http://www.plansoft.org/';" >Plansoft.org</button-->
	</div>
  </div>
  <hr style="margin:0px"></hr>
</div>

