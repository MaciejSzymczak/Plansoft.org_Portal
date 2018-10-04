<?php Require_once 'pageHeader.php'; ?>
<?php Require_once 'impet/dbconnection.php'; ?>

<div class="container" style="margin-top:30px">
  <div class="row" style="padding:30px">
    <div class="col-sm-4">
	</div>
    <div class="col-sm-4">
		<table>
			<tr>
				<td> <img src="img/cube.jpg" style="height: 80px; width: 80px;"/> </td>
				<td>&nbsp;</td>
				<td class="page-header text-center"><h1><a href="http://plansoft.org" class="card-link">Plansoft.org</h1></a> <p>Planowanie zajęć i dyżurów</p></td>
			</tr>
		</table>
    </div>
    <div class="col-sm-4">
	</div>
  </div>
  
  <div class="row">
    <div class="col-sm-3">
	</div>
    <div class="col-sm-6">

		<div class="card text-center">
			  <div class="card-header">LOGOWANIE</div>
			  <div class="card-body">

				<?php
				  if ( isset($_POST['command']) ) {
					  if($_POST['command']=='COMMAND_VERIFY_USER'){
						  $fields = sqlSelect( sprintf("
						        select p.password_sha1
								     , getsha1('%s')
									 , p.lec_id
									 , p.rol_id
									 , p.cal_id
									 , r.can_edit_l
									 , r.can_edit_g
									 , r.can_edit_r
									 , r.can_edit_s
									 , r.can_edit_f
									 , r.can_delete
									 , r.can_insert
									 , r.can_edit_o
									 , r.can_edit_d 
									 , parent.name parent_name
						        from planners p, planners r, planners parent
								where p.parent_id=parent.id 
								  and  r.id = p.rol_id 
								  and p.active_flag = '+'
								  and p.name = '%s'",$_POST['password'],$_POST['username']));						  
						if ($fields[0]!="" && $fields[0]==$fields[1]) {
							debug('--- user successfully logged: '.$_POST['username'].' ---');
							$_SESSION['username']=$_POST['username'];							
							$_SESSION['lec_id']=$fields[2];							
							$_SESSION['rol_id']=$fields[3];							
							$_SESSION['cal_id']=$fields[4];							
							$_SESSION['can_delete']=$fields[10];							
							$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp		
							$_SESSION['supervisor']=$fields[14];							
							$_SESSION['isActive']='true';							
							header('Location: menu.php');
							exit();
						} else {
							Trigger_error('Invalid login : User ' . $_POST['username'], E_USER_NOTICE);
							echo '<div class="alert alert-warning"><strong>Błąd!</strong> Logowanie nie powiodło się.</div>';							
						}
					  }	  
				  }
				?>

			     <form class="form-horizontal" name="login-form" id="login-form" method="post" action="./index.php">
					<p><input tabindex="1" type="text" class="form-control" id="username" placeholder="Użytkownik" name="username">
					<input tabindex="2" type="password" class="form-control" id="password" placeholder="Hasło" name="password"></p>
					<input tabindex="3" type="submit" id="cmdlogin" name="cmdlogin" class="btn btn-primary" value="Zaloguj się"  />
					<input type="hidden" id="command" name='command' value="COMMAND_VERIFY_USER"/>
					<!--a href="http://plansoft.org" class="card-link">Pomoc</a-->
				</form>
			  </div>
		</div>
    </div>
    <div class="col-sm-3">
	</div>
  </div>

  
</div>

<script>
	document.getElementById("username").focus();
</script>

<?php Require_once 'pageFooter.php'; ?>