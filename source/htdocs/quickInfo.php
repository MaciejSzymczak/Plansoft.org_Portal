<?php Require_once 'pageHeader.php'; ?>
<?php Require_once 'impet/quickInfoHeader.php'; ?>

<!--  No login access is required -->
<!--?php checkAccess('#localhost#index.php#newClass.php'); ?-->  

<?php
	$search = '';
	if (isset($_GET["search"])) $search=$_GET["search"];	


	$when = 'today';
	if (isset($_GET["when"])) $when=$_GET["when"];	
	$b1='default';
	$b2='default';
	$b3='default';

	switch ($when) {
	case 'today':
		$b1='primary';
		$daysFrom=0;
		$daysTo=0;
		break;
	case 'tomorrow':
		$b2='primary';
		$daysFrom=1;
		$daysTo=1;
		break;
	case 'thisweek':
		$b3='primary';
		$daysFrom=0;
		$daysTo=6;
		break;
	}	
?>

<div class="container">
  <div class="row">
    <div class="col-sm-12">
	<center><h2>Przeglądanie rozkładu zajęć</h2></center>	
	</div>
  </div>
	
	<script>
	  function go(e) {
		document.getElementById("when").value=e;
		document.getElementById("action").submit();
	  }	 
	</script>	
	<form id="action" action="/quickInfo.php" method="get">
		<input type="hidden" id="when" name="when" value="<?php echo htmlspecialchars($when, ENT_QUOTES, 'UTF-8'); ?>"/>
		<div class="row">
		<div class="col-sm-6">
  			<input type="text" id="search" name="search" class="form-control" placeholder="Szukaj" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
		</div>  		
		<div class="col-sm-6">
			<div class="btn-group">
			  <button type="button" class="btn btn-<?php echo $b1; ?>" onclick="go('today');">Dziś</button>
			  <button type="button" class="btn btn-<?php echo $b2; ?>" onclick="go('tomorrow');">Jutro</button>
			  <button type="button" class="btn btn-<?php echo $b3; ?>" onclick="go('thisweek');">Najbliższy tydzień</button>
			</div>	
		</div>

		</div>
	</form>	
	
	<script>
	document.getElementById("search").focus();
	</script>
	
  <div class="row">
    <div class="col-sm-12">

	<?php 
		sqlDisplayTable(
			 sqlReadFile("quickInfo.sql")
			,array(':daysFrom='.$daysFrom, ':daysTo='.$daysTo, ':search='.'%'.$search.'%')
			,[""/*id*/,"Dzień","Dzień tyg.","Godzina","Przedmiot","Forma","Wykładowcy","Grupy","Zasoby","zajęcia","Info dla studentów",""/*owner*/]
		); 
	?>	 
	</div>
	</div>
</div>
  

	
<?php Require_once 'pageFooter.php'; ?>
