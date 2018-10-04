<?php Require_once '../secret/settings.php'; ?>
<?php 
	//open Oracle connection
	$dbconn = oci_connect(
		  $db_username
		, $db_password
		, $db_database
		, 'AL32UTF8');
	 
	if (!$dbconn) {
		$m = oci_error();
		trigger_error('Could not connect to database: '. $m['message'], E_USER_ERROR);
	}	

// Use this function to fetch single record from database
// $fields = sqlSelect( sprintf("select password_sha1, getsha1('%s') from planners where name = '%s'",$_POST['password'],$_POST['username']));
// echo $fields[0]
function sqlSelect ($query) {
	global $dbconn;
	$s = oci_parse($dbconn, $query);
	if (!$s) {
		$m = oci_error($dbconn);
		trigger_error('Could not parse statement '.$query.': '. $m['message'], E_USER_ERROR);
	}
	$r = oci_execute($s);
	if (!$r) {
		$m = oci_error($s);
		trigger_error('Could not execute statement '.$query.': '. $m['message'], E_USER_ERROR);
	}
	 
	$fields = array();
	$c = 0;
	while (($row = oci_fetch_array($s, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
		foreach ($row as $item) {
			$fields[$c] = $item!==null?htmlspecialchars($item, ENT_QUOTES|ENT_SUBSTITUTE):"";
			$c++;
		}
		break;
	}
	
	if ($c==0) {
		$fields[0]="";
		$fields[1]="";
		$fields[2]="";
		$fields[3]="";
		$fields[4]="";
	}
	
	return $fields;
}	
	
function sqlReadFile ($fileName) {
	$myfile = fopen($fileName, "r") or trigger_error('sqlReadFile.Unable to open file: '.$fileName, E_USER_ERROR);
	$filecontent= fread($myfile,filesize($fileName));
	fclose($myfile);
	return $filecontent;	
}
	
//Renders a table on the screen
//Example call: sqlDisplayTable(sqlReadFile("queryClasses.sql"));
//queryClasses.sql is your query. Keep your complex SQL outside php code to aviod problems with escape characters like ' or ", also for clarity.
//it is assumed 1st value is always column ID (hidden)
function sqlDisplayTable ($query, $params = array(), $headers = '') {
	global $dbconn;
		
	$s = oci_parse($dbconn, $query);
	if (!$s) {
		$m = oci_error($dbconn);
		trigger_error('SQL parse statement:'.$query.' ERROR:'. $m['message'], E_USER_ERROR);
	}
	
	$arrlength = count($params);
	for($x = 0; $x < $arrlength; $x++) {
		$paramTokenized = explode('=',$params[$x]);
		oci_bind_by_name($s, $paramTokenized[0],$paramTokenized[1],32);
    }	
	
	$r = oci_execute($s);
	if (!$r) {
		$m = oci_error($s);
		trigger_error('SQL execute statement:'.$query.' ERROR:'. $m['message'], E_USER_ERROR);
	}
	
	
	echo '
<script>
  function deleteRecordClick(recordId, recordName) {
    document.getElementById("command").value="COMMAND_DELETE_RECORD";
    document.getElementById("recordId").value=recordId;
    document.getElementById("recordName").value=recordName;
    document.getElementById("action").submit();
  }
</script>
<form id="action" action="'.$_SERVER['PHP_SELF'].'" method="POST">
  <input type="hidden" id="command" name="command" value="COMMAND_DELETE_RECORD"/>
  <input type="hidden" id="recordName" name="recordName" value=""/>
  <input type="hidden" id="recordId" name="recordId" value=""/>
</form>  
';
		 
	echo "<table border='1'>\n";
	$ncols = oci_num_fields($s);
	$noHeaders = $headers == '';
	echo "<tr>\n";
	//index starts with 2 because column 1 is ID
	for ($i = 1; $i <= $ncols; ++$i) {
		$colname = oci_field_name($s, $i);
		if ($noHeaders) {
			echo "  <th><b>".htmlspecialchars($colname,ENT_QUOTES|ENT_SUBSTITUTE)."</b></th>\n";			
		} else {
			$ignoreCol = $headers[$i-1]=='';
			if (!$ignoreCol) {
				echo "  <th><b>".$headers[$i-1]."</b></th>\n";										
			}
		}
	}
	echo "    <th></th>";
	echo "</tr>\n";
	 
	while (($row = oci_fetch_array($s, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
		echo "<tr>\n";
		$i=1;
		foreach ($row as $item) {	
			if ($noHeaders) {
				$ignoreCol = false;			
			} else {
				$ignoreCol = $headers[$i-1]=='';
			}				
			if (!$ignoreCol) {
				echo "<td>";
				echo $item!==null?htmlspecialchars($item, ENT_QUOTES|ENT_SUBSTITUTE):"&nbsp;";
				echo "</td>\n";				
			}
			$i++;
		}
		if (	$_SESSION['can_delete']=='+' //permission on user role
			&&  $row['CAN_MODIFY']=='1'  //confine calendar
			&& strpos($row['OWNER'], $_SESSION['username']) !== false //owner on class
			) { 
			echo '<td><button type="button" class="btn btn-info" onclick="deleteRecordClick(\''.$row['ID'].'\',\''.$row['RECORD_NAME'].'\');"><span class="far fa-trash-alt"></button></td>';
		} else {
			echo '<td></td>';
		}
		echo "</tr>\n";
	}
	echo "</table>\n";	
}
	

function sqlDisplayRecords ($query, $displayAs, $params) {
	global $dbconn;		
	$s = oci_parse($dbconn, $query);
	if (!$s) {
		$m = oci_error($dbconn);
		trigger_error('SQL parse statement:'.$query.' ERROR:'. $m['message'], E_USER_ERROR);
	}
	
	$arrlength = count($params);
	for($x = 0; $x < $arrlength; $x++) {
		$paramTokenized = explode('=',$params[$x]);
		oci_bind_by_name($s, $paramTokenized[0],$paramTokenized[1],32);
    }
	
	$r = oci_execute($s);
	if (!$r) {
		$m = oci_error($s);
		trigger_error('SQL execute statement:'.$query.' ERROR:'. $m['message'], E_USER_ERROR);
	}
	while (($row = oci_fetch_array($s, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {		
		$p[0] = '';
		$p[1] = '';
		$p[2] = '';
		$p[3] = '';
		$p[4] = '';
		$p[5] = '';
		$p[6] = '';
		$p[7] = '';
		$p[8] = '';
		$p[9] = '';
		$x=0;
		foreach ($row as $item) {	
			$p[$x]= $item!==null?htmlspecialchars($item, ENT_QUOTES|ENT_SUBSTITUTE):"";
			$x++;
		}
		echo sprintf($displayAs . PHP_EOL, $p[0], $p[1], $p[2], $p[3], $p[4], $p[5], $p[6], $p[7], $p[8], $p[9]);
	}	
}	
	
?>

