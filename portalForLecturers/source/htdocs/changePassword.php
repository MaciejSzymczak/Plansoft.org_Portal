<?php Require_once 'pageHeader.php'; ?>
<?php Require_once 'impet/engine.php'; ?>

<?php checkAccess('#localhost#menu.php'); ?>    

<script>
  function changePasswordClick() {	
    errorFlag=false;  
    clearErrorMessage();
	oldPassword = document.getElementById("oldPassword");
	newPassword = document.getElementById("newPassword");
	repeatPassword = document.getElementById("repeatPassword");
	
	oldPassword.classList.remove('invalid');
	newPassword.classList.remove('invalid');	
	repeatPassword.classList.remove('invalid');	
	
    if (oldPassword.value=="") {
		oldPassword.classList.add('invalid');
		errorFlag = true;
	}

    if (newPassword.value=="") {
		newPassword.classList.add('invalid');
		errorFlag = true;
	}

    if (repeatPassword.value=="") {
		repeatPassword.classList.add('invalid');
		errorFlag = true;
	}

    if (newPassword.value!=repeatPassword.value) {
		newPassword.classList.add('invalid');
		repeatPassword.classList.add('invalid');
		setErrorMessage("Wpisz dwukrotnie to samo hasło");
		errorFlag = true;
	}	
	
	if (errorFlag)
	    return;	
	
    document.getElementById("command").value="CHANGE_PASSWORD";
    document.getElementById("poldPassword").value=oldPassword.value;
    document.getElementById("pnewPassword").value=newPassword.value;
    document.getElementById("action").submit();
  }
</script>

<form id="action" action="changePassword.php" method="POST">
  <input type="hidden" id="command" name="command" value="CHANGE_PASSWORD"/>
  <input type="hidden" id="poldPassword" name="poldPassword" value=""/>
  <input type="hidden" id="pnewPassword" name="pnewPassword" value=""/>
</form> 

<style>
.valid {
  border-color: #ddffdd;
  border-width: 0px;
}
.invalid {
  border-color: #800000;
  border-width: 3px;
}
</style>

<div class="container">
  <div class="row">
    <div class="col-sm-12">
		<br/>
		
		<script>
			function clearErrorMessage() {
				document.getElementById("errorMessage").style.display="none";			
			}
			function setErrorMessage(m) {
				document.getElementById("errorMessage").innerHTML = m;
				document.getElementById("errorMessage").style.display="block";			
			}
		</script>
		<div id="errorMessage" style="display:none" class="alert alert-warning"></div>

		<?php
		  if(isset($_POST['command']) && $_POST['command']=='CHANGE_PASSWORD'){
			  $fields = sqlSelect( sprintf("
					select password_sha1
						 , getsha1('%s') oldsha1
						 , getsha1('%s') newsha1	
					from planners
					where active_flag = '+'
					  and name = '%s'",$_POST['poldPassword'],$_POST['pnewPassword'],$_SESSION['username']));	
				if ($fields[0] != $fields[1]) {
					echo '<div class="alert alert-warning"><strong>Stare hasło jest nieprawidłowe. Spróbuj ponownie</strong></div>';				
				}
				else {
					global $dbconn;
					$sql = 'BEGIN update planners set password_sha1=:password_sha1 where name=:name; END;';
					$stmt = oci_parse($dbconn,$sql);
					oci_bind_by_name($stmt,':password_sha1',$fields[2]);
					oci_bind_by_name($stmt,':name',$_SESSION['username']);
					$success = oci_execute($stmt);
					if (!$success) {
						$m = oci_error();
						echo '<div class="alert alert-warning"><strong>Wystąpił nieokreślony błąd</strong></div>';				
					} else {
						echo '<div class="alert alert-warning"><strong>Hasło zostało zmienione</strong></div>';				
					}
				}				
			}
		?>		

		<div class="card">
		  <div class="card-header">Zmiana hasła</div>
		  <div class="card-body">

			  <div class="input-group mb-3">
				<div class="input-group-prepend">
				  <span class="input-group-text" style="width:200px">Stare hasło</span>
				</div>
				<input type="password" class="form-control" placeholder="" id="oldPassword">
			  </div>

			  <div class="input-group mb-3">
				<div class="input-group-prepend">
				  <span class="input-group-text" style="width:200px">Nowe hasło</span>
				</div>
				<input type="password" class="form-control" placeholder="" id="newPassword">
			  </div>
			  
			  <div class="input-group mb-3">
				<div class="input-group-prepend">
				  <span class="input-group-text" style="width:200px">Powtórz nowe hasło</span>
				</div>
				<input type="password" class="form-control" placeholder="" id="repeatPassword">
			  </div>			  
			  
		  </div> 
	  		<div class="card-footer">
				<button type="button" class="btn btn-primary" onclick="changePasswordClick();">Zmień</button>
				<button type="button" class="btn btn-primary" onclick="self.location.href=('./menu.php')">Powrót</button>
			</div>

		</div>

	</div>
  </div>
  
</div>

<script>
	document.getElementById("oldPassword").focus();
</script>
	
<?php Require_once 'pageFooter.php'; ?>