<?php Require_once 'impet/dbconnection.php'; ?>

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
	</div>
  </div>
  <hr style="margin:0px"></hr>
</div>

