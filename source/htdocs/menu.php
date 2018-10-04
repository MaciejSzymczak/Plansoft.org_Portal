<?php Require_once 'pageHeader.php'; ?>
<?php Require_once 'impet/engine.php'; ?>

<?php checkAccess('#localhost#index.php#newClass.php'); ?>  

<style>

.btn-circle {
  width: 30px;
  height: 30px;
  text-align: center;
  padding: 6px 0;
  font-size: 12px;
  line-height: 1.428571429;
  border-radius: 15px;
}
.btn-circle.btn-lg {
  width: 50px;
  height: 50px;
  padding: 10px 16px;
  font-size: 18px;
  line-height: 1.33;
  border-radius: 25px;
}
.btn-circle.btn-xl {
  width: 70px;
  height: 70px;
  padding: 10px 16px;
  font-size: 24px;
  line-height: 1.33;
  border-radius: 35px;
}

</style>

<div class="container">
  <div class="row" style="margin: 5px">
    <div class="col-sm-1" >
		<button type="button" style="margin: 15px" class="btn btn-primary btn-circle btn-xl" onclick="self.location.href=('./newClass.php')"  ><strong>+</strong></button>
	</div>
    <div class="col-sm-11">
	<div class="alert alert-info"><strong>Witaj w portalu Plansoft.org!</strong>
	Użyj przycisku z lewej strony aby dodać nowe zajęcie.<br/>
	Widzisz zajęcia, które udostępnił Ci planista. Jeżeli uważasz, że część zajęć jest niewidoczna, wówczas skontaktuj się z planistą.
	Możesz usuwać rekordy, które udostępnił Ci planista oraz rekordy utworzone przez Ciebie.
	</div>	
	</div>
  </div>
  <div class="row">
    <div class="col-sm-12">

	<?php
	  if(isset($_POST['command']) && $_POST['command']=='COMMAND_DELETE_RECORD'){
		global $dbconn;
		$sql = 'BEGIN api.delete_class ( :recordId ); END;';
		$stmt = oci_parse($dbconn,$sql);
		oci_bind_by_name($stmt,':recordId',$_POST['recordId'],32);
		$success = oci_execute($stmt);
		if (!$success) {
			$m = oci_error();
			echo '<div class="alert alert-warning"><strong>Skasowanie rekordu '.$_POST['recordName'].' nie powiodło się! </strong></div>';				
		} else {
			echo '<div class="alert alert-warning"><strong>Rekord '.$_POST['recordName'].' został skasowany!</strong></div>';				
		}		
		}
	?>

	<?php 						
		//echo $_SESSION['cal_id'];
		//echo '<p/>';
		//echo $_SESSION['lec_id'];							
		sqlDisplayTable(
			 sqlReadFile("queryClasses.sql")
			,array(':confineCalId='.$_SESSION['cal_id'], ':PLA_ID='.$_SESSION['rol_id'])
			,[""/*id*/,""/*RECORD_NAME*/,"Dzień","Dzień tyg.","Godzina","Przedmiot","Forma","Wykładowcy","Grupy","Zasoby","zajęcia","Info dla studentów",""/*owner*/,""/*CAN_MODIFY*/]
		);  //łąkaśźćn
	?>	 
	</div>
	</div>
  </div>
  
</div>
	
<?php Require_once 'pageFooter.php'; ?>


