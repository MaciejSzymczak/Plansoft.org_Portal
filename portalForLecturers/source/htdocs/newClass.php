<?php Require_once 'pageHeader.php';?>
<?php Require_once 'impet/engine.php'; ?>

<?php checkAccess('#localhost#menu.php'); ?>  

<script>
  function insertClassClick() {
	errorFlag = false;
	Day = document.getElementById("Day");
	Hour = document.getElementById("Hour");
	Subject = document.getElementById("Subject");
	Form = document.getElementById("Form");
	Lecturer = document.getElementById("Lecturer");
	Group = document.getElementById("Group");
	Room = document.getElementById("Room");
	InfoExternal = document.getElementById("InfoExternal");
	InfoInternal = document.getElementById("InfoInternal");

	Day.classList.remove('invalid');
	Hour.classList.remove('invalid');
	Subject.classList.remove('invalid');
	Form.classList.remove('invalid');
	Lecturer.classList.remove('invalid');
	Group.classList.remove('invalid');
	Room.classList.remove('invalid');
	
    if (Hour.value=="") {
		Hour.classList.add('invalid');
		errorFlag = true;
	}	
    if (Day.value=="") {
		Day.classList.add('invalid');
		errorFlag = true;
	}	
    if (Subject.value=="") {
		Subject.classList.add('invalid');
		errorFlag = true;
	}	
    if (Form.value=="") {
		Form.classList.add('invalid');
		errorFlag = true;
	}	
    if (Lecturer.value=="" && Group.value=="" && Room.value=="") {
		Lecturer.classList.add('invalid');
		Group.classList.add('invalid');
		Room.classList.add('invalid');
		errorFlag = true;
	}	
	
	if (errorFlag)
	    return;
	  
    document.getElementById("command").value="COMMAND_INSERT_CLASS";
    document.getElementById("recordName").value=Day.value+" Godz:"+Hour.value+" Przedmiot:"+Subject.value+" Forma:"+Form.value+" Wykładowca:"+Lecturer.value+" Grupa:"+Group.value+" Sala:"+Room.value+" "+InfoExternal.value+" "+InfoInternal.value;
	document.getElementById("pDay").value=Day.value;
	document.getElementById("pHour").value=nameToId[Hour.value];
	document.getElementById("pFill").value="100";
	document.getElementById("pSubject").value=getIdByName(Subject.value);
	document.getElementById("pForm").value=nameToId[Form.value];
	document.getElementById("pOwner").value='<?php echo $_SESSION['supervisor'].";".$_SESSION['username'];?>';
	document.getElementById("pLec").value=getIdByName(Lecturer.value);
	document.getElementById("pGro").value=getIdByName(Group.value);
	document.getElementById("pRes").value=getIdByName(Room.value);
	document.getElementById("pCol").value="";
	document.getElementById("pDesc1").value=InfoInternal.value;
	document.getElementById("pDesc2").value=InfoExternal.value;
	document.getElementById("pDesc3").value="";
	document.getElementById("pDesc4").value="";

    document.getElementById("action").submit();
  }
</script>

<form id="action" action="newClass.php" method="POST">
  <input type="hidden" id="command" name="command" value="COMMAND_INSERT_CLASS"/>
  <input type="hidden" id="recordName" name="recordName" value=""/>
  <input type="hidden" id="pDay" name="pDay" value=""/>
  <input type="hidden" id="pHour" name="pHour" value=""/>
  <input type="hidden" id="pFill" name="pFill" value=""/>
  <input type="hidden" id="pSubject" name="pSubject" value=""/>
  <input type="hidden" id="pForm" name="pForm" value=""/>
  <input type="hidden" id="pOwner" name="pOwner" value=""/>
  <input type="hidden" id="pLec" name="pLec" value=""/>
  <input type="hidden" id="pGro" name="pGro" value=""/>
  <input type="hidden" id="pRes" name="pRes" value=""/>
  <input type="hidden" id="pCol" name="pCol" value=""/>
  <input type="hidden" id="pDesc1" name="pDesc1" value=""/>
  <input type="hidden" id="pDesc2" name="pDesc2" value=""/>
  <input type="hidden" id="pDesc3" name="pDesc3" value=""/>
  <input type="hidden" id="pDesc4" name="pDesc4" value=""/>
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
		<!--div class="alert alert-info">DODAWANIE NOWEGO ZAJECIA - NIEBAWEM</div-->
		<?php
		  if(isset($_POST['command']) && $_POST['command']=='COMMAND_INSERT_CLASS'){
			global $dbconn;
			$sql = 'BEGIN api.insert_class(2,TO_DATE(:DAY,\'YYYY-MM-DD\'),:HOUR,:FILL,:SUBJECT,:FORM,:OWNER,:LEC,:GRO,:RES,:COL,:DESC1,:DESC2,:DESC3,:DESC4); END;';
			$stmt = oci_parse($dbconn,$sql);
			oci_bind_by_name($stmt,':DAY',$_POST['pDay']);
			oci_bind_by_name($stmt,':HOUR',$_POST['pHour']);
			oci_bind_by_name($stmt,':FILL',$_POST['pFill']);
			oci_bind_by_name($stmt,':SUBJECT',$_POST['pSubject']);
			oci_bind_by_name($stmt,':FORM',$_POST['pForm']);
			oci_bind_by_name($stmt,':OWNER',$_POST['pOwner']);
			oci_bind_by_name($stmt,':LEC',$_POST['pLec']);
			oci_bind_by_name($stmt,':GRO',$_POST['pGro']);
			oci_bind_by_name($stmt,':RES',$_POST['pRes']);
			oci_bind_by_name($stmt,':COL',$_POST['pCol']);
			oci_bind_by_name($stmt,':DESC1',$_POST['pDesc1']);
			oci_bind_by_name($stmt,':DESC2',$_POST['pDesc2']);
			oci_bind_by_name($stmt,':DESC3',$_POST['pDesc3']);
			oci_bind_by_name($stmt,':DESC4',$_POST['pDesc4']);
			debug('--- insert class: '
			.'Day:'.$_POST['pDay']
			.'Hour:'.$_POST['pHour']
			.'Fill:'.$_POST['pFill']
			.'Subject:'.$_POST['pSubject']
			.'Form:'.$_POST['pForm']
			.'Owner:'.$_POST['pOwner']
			.'Lec:'.$_POST['pLec']
			.'Gro:'.$_POST['pGro']
			.'Res:'.$_POST['pRes']
			.'Col:'.$_POST['pCol']
			.'Desc1:'.$_POST['pDesc1']
			.'Desc2:'.$_POST['pDesc2']
			.'Desc3:'.$_POST['pDesc3']
			.'Desc4:'.$_POST['pDesc4']
			);			
			//oci_bind_by_name($stmt,':ptt_comb_ids','');
			$success = oci_execute($stmt);
			if (!$success) {
				$m = oci_error();
				echo '<div class="alert alert-warning"><strong>Wstawienie rekordu '.$_POST['recordName'].' nie powiodło się! Termin został w międzyczasie zarezerwowany. </strong></div>';				
			} else {
				echo '<div class="alert alert-warning"><strong>Rekord '.$_POST['recordName'].' został utworzony!</strong></div>';				
			}		
			}
		?>	
	
		<div class="card">
		  <div class="card-header">Dodawanie nowego zajęcia</div>
		  <div class="card-body">
		  

		  <div class="input-group mb-3">
			<div class="input-group-prepend">
			  <span class="input-group-text" style="width:150px">Dzień</span>
			</div>
			<input type="text" class="form-control" placeholder="" id="Day" readonly>
			<div class="input-group-append">
				<button type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown">
				  Wybierz
				</button>
				<div class="dropdown-menu" id="DayItems">
				  <a class="dropdown-item" href="#">Link 1</a>
				  <a class="dropdown-item" href="#">Link 2</a>
				  <a class="dropdown-item" href="#">Link 3</a>
				</div>		
			</div>
		  </div>

		  <div class="input-group mb-3">
			<div class="input-group-prepend">
			  <span class="input-group-text"  style="width:150px">Godzina</span>
			</div>
			<input type="text" class="form-control" placeholder="" id="Hour" readonly>
			<div class="input-group-append">
				<button type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown">
				  Wybierz
				</button>
				<div class="dropdown-menu" id="HourItems">
				  <a class="dropdown-item" href="#">Link 1</a>
				  <a class="dropdown-item" href="#">Link 2</a>
				  <a class="dropdown-item" href="#">Link 3</a>
				</div>		
			</div>
		  </div>

		  <div class="input-group mb-3">
			<div class="input-group-prepend">
			  <span class="input-group-text"  style="width:150px">Przedmiot</span>
			</div>
			<input type="text" class="form-control" placeholder="" id="Subject" readonly>
			<div class="input-group-append">
				<button type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown">
				  Wybierz
				</button>
				<div class="dropdown-menu" id="SubjectItems">
				  <a class="dropdown-item" href="#">Link 1</a>
				  <a class="dropdown-item" href="#">Link 2</a>
				  <a class="dropdown-item" href="#">Link 3</a>
				</div>		
			</div>
		  </div>

		  <div class="input-group mb-3">
			<div class="input-group-prepend">
			  <span class="input-group-text"  style="width:150px">Forma</span>
			</div>
			<input type="text" class="form-control" placeholder="" id="Form" readonly>
			<div class="input-group-append">
				<button type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown">
				  Wybierz
				</button>
				<div class="dropdown-menu" id="FormItems">
				  <a class="dropdown-item" href="#">Link 1</a>
				  <a class="dropdown-item" href="#">Link 2</a>
				  <a class="dropdown-item" href="#">Link 3</a>
				</div>		
			</div>
		  </div>

		  <div class="input-group mb-3">
			<div class="input-group-prepend">
			  <span class="input-group-text"  style="width:150px">Wykładowca</span>
			</div>
			<input type="text" class="form-control" placeholder="" id="Lecturer" readonly>
			<div class="input-group-append">
				<button type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown">
				  Wybierz
				</button>
				<div class="dropdown-menu" id="LecturerItems">
				  <a class="dropdown-item" href="#">Link 1</a>
				  <a class="dropdown-item" href="#">Link 2</a>
				  <a class="dropdown-item" href="#">Link 3</a>
				</div>		
			</div>
		  </div>
	  
		  <div class="input-group mb-3">
			<div class="input-group-prepend">
			  <span class="input-group-text"  style="width:150px">Sala</span>
			</div>
			<input type="text" class="form-control" placeholder="" id="Room" readonly>
			<div class="input-group-append">
				<button type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown">
				  Wybierz
				</button>
				<div class="dropdown-menu" id="RoomItems">
				  <a class="dropdown-item" href="#">Link 1</a>
				  <a class="dropdown-item" href="#">Link 2</a>
				  <a class="dropdown-item" href="#">Link 3</a>
				</div>		
			</div>
		  </div>
		  
		  <div class="input-group mb-3">
			<div class="input-group-prepend">
			  <span class="input-group-text"  style="width:150px">Grupa</span>
			</div>
			<input type="text" class="form-control" placeholder="" id="Group" readonly>
			<div class="input-group-append">
				<button type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown">
				  Wybierz
				</button>
				<div class="dropdown-menu" id="GroupItems">
				  <a class="dropdown-item" href="#">Link 1</a>
				  <a class="dropdown-item" href="#">Link 2</a>
				  <a class="dropdown-item" href="#">Link 3</a>
				</div>		
			</div>
		  </div>

		  <div class="input-group mb-3">
			<div class="input-group-prepend">
			  <span class="input-group-text"  style="width:150px">Info dla studentów</span>
			</div>
			<input type="text" class="form-control" placeholder="" id="InfoExternal">
		  </div>

		  <div class="input-group mb-3">
			<div class="input-group-prepend">
			  <span class="input-group-text"  style="width:150px">Info dla planisty</span>
			</div>
			<input type="text" class="form-control" placeholder="" id="InfoInternal">

		  </div>
		  
			</div> 
	  		<div class="card-footer">
				<button type="button" class="btn btn-primary" onclick="insertClassClick();">Dodaj</button>
				<button type="button" class="btn btn-primary" onclick="self.location.href=('./menu.php')">Anuluj</button>		
				<button type="button" class="btn btn-primary" onclick="self.location.href=('./newClass.php')">Wyczyść</button>
			</div>

		</div>

	</div>
  </div>
  
</div>

		  <script>		
				Days = [];
				//Days.push({Id:"n/a", Name:"2018-10-01"}); 
				//Days.push({Id:"n/a", Name:"2018-10-02"}); 
				//Days.push({Id:"n/a", Name:"2018-10-03"}); 
				<?php
				sqlDisplayRecords( 
					  'select unique to_char(day,\'yyyy-mm-dd\') DAY from res_hints where res_id=:confineCalId order by 1'
					, 'Days.push({Id:"n/a", Name:"%s"});'
					, array(':confineCalId='.$_SESSION['cal_id'])
				);
				?>

				Hours = [];
				//Hours.push({Id:"n/a", Name:"07.00-08.00"}); 
				//Hours.push({Id:"n/a", Name:"08.00-09.00"}); 		  
				//Hours.push({Id:"n/a", Name:"12.00-13.00"}); 		  
				<?php
				sqlDisplayRecords( 
					  'select unique hour,(select caption from grids where no = hour) from res_hints where res_id=:confineCalId order by 2'
					, 'Hours.push({Id:"%s", Name:"%s"});'
					, array(':confineCalId='.$_SESSION['cal_id'])
				);
				?>
		  
				var availableDays = [];
				//availableDays["2018-10-01:07.00-08.00"]="1"; 
				//availableDays["2018-10-01:08.00-09.00"]="1"; 
				//availableDays["2018-10-01:09.00-10.00"]="1"; 
				//availableDays["2018-10-02:07.00-08.00"]="1"; 
				//availableDays["2018-10-03:12.00-13.00"]="1"; 
				<?php
				sqlDisplayRecords( 
					  'select unique to_char(day,\'yyyy-mm-dd\')||\':\'||(select caption from grids where no = hour),hour from res_hints where res_id=:confineCalId order by 2'
					, 'availableDays["%s"]="1";'
					, array(':confineCalId='.$_SESSION['cal_id'])
				);
				?>
				
				Lecturers = [];
				//Lecturers.push({Id:"1", Name:"Maciej Szymczak"}); 
				//Lecturers.push({Id:"2", Name:"Marian Konwicki"}); 
				<?php
				sqlDisplayRecords( 
					  'select id, title||\' \'||first_name||\' \'||last_name from lecturers where LECTURERS.ID IN (SELECT LEC_ID FROM LEC_PLA WHERE PLA_ID = :PLA_ID) order by last_name'
					, 'Lecturers.push({Id:"%s", Name:"%s"});'
					, array(':PLA_ID='.$_SESSION['rol_id'])
				);
				?>

				Groups = [];
				//Groups.push({Id:"3", Name:"G1"}); 
				//Groups.push({Id:"4", Name:"G2"}); 
				<?php
				sqlDisplayRecords( 
					  'select id, Name from groups where GROUPS.ID IN (SELECT GRO_ID FROM GRO_PLA WHERE PLA_ID = :PLA_ID) order by name'
					, 'Groups.push({Id:"%s", Name:"%s"}); '
					, array(':PLA_ID='.$_SESSION['rol_id'])
				);
				?>

				Rooms = [];
				//Rooms.push({Id:"5", Name:"R1"}); 
				//Rooms.push({Id:"6", Name:"R2"}); 
				<?php
				sqlDisplayRecords( 
					  'select id,name||\' \'||attribs_01 from rooms     where ROOMS.ID IN (SELECT ROM_ID FROM ROM_PLA WHERE PLA_ID = :PLA_ID) order by name||\' \'||attribs_01'
					, 'Rooms.push({Id:"%s", Name:"%s"});'
					, array(':PLA_ID='.$_SESSION['rol_id'])
				);
				?>

				Subjects = [];
				//Subjects.push({Id:"1", Name:"S1"}); 
				//Subjects.push({Id:"2", Name:"S2"}); 
				<?php
				sqlDisplayRecords( 
					  'select id, Name from subjects where SUBJECTS.ID IN (SELECT SUB_ID FROM SUB_PLA WHERE PLA_ID = :PLA_ID) order by name'
					, 'Subjects.push({Id:"%s", Name:"%s"});'
					, array(':PLA_ID='.$_SESSION['rol_id'])
				);
				?>

				Forms = [];
				//Forms.push({Id:"1", Name:"F1"}); 
				//Forms.push({Id:"2", Name:"F2"}); 
				<?php
				sqlDisplayRecords( 
					  'select id, Name from forms where FORMS.ID IN (SELECT FOR_ID FROM FOR_PLA WHERE PLA_ID = :PLA_ID) order by name'
					, 'Forms.push({Id:"%s", Name:"%s"});'
					, array(':PLA_ID='.$_SESSION['rol_id'])
				);
				?>

				OccupiedByObject = [];
				//OccupiedByObject["2018-10-01:07.00-08.00:1"]="1"; 
				//OccupiedByObject["2018-10-02:07.00-08.00:1"]="1"; 
				//OccupiedByObject["2018-10-01:07.00-08.00:3"]="1"; 
				//OccupiedByObject["2018-10-02:07.00-08.00:4"]="1"; 
				//OccupiedByObject["2018-10-01:09.00-10.00:5"]="1"; 
				//OccupiedByObject["2018-10-02:09.00-10.00:5"]="1"; 
				<?php
				sqlDisplayRecords( 
					  'select to_char(day,\'yyyy-mm-dd\')||\':\'||(select caption from grids where no = hour)||\':\'||lec_id from lec_cla where lec_id in (SELECT LEC_ID FROM LEC_PLA WHERE PLA_ID = :PLA_ID)  and (DAY, HOUR) in (select day, hour from res_hints where res_id=:confineCalId)
						union all
						select to_char(day,\'yyyy-mm-dd\')||\':\'||(select caption from grids where no = hour)||\':\'||gro_id from gro_cla where gro_id in (SELECT gro_ID FROM gro_PLA WHERE PLA_ID = :PLA_ID)  and (DAY, HOUR) in (select day, hour from res_hints where res_id=:confineCalId)
						union all
						select to_char(day,\'yyyy-mm-dd\')||\':\'||(select caption from grids where no = hour)||\':\'||rom_id from rom_cla where rom_id in (SELECT rom_ID FROM rom_PLA WHERE PLA_ID = :PLA_ID)  and (DAY, HOUR) in (select day, hour from res_hints where res_id=:confineCalId)'
					, 'OccupiedByObject["%s"]="1"; '
					, array(':PLA_ID='.$_SESSION['rol_id'], ':confineCalId='.$_SESSION['cal_id'])
				);
				?>

				nameToId = [];
				function getIdByName(name) {
					if (name=="") return "";
					return nameToId[name];
				}
				
				function initNameToId () {
					allObjects = [];
					allObjects = allObjects.concat(Lecturers);
					allObjects = allObjects.concat(Groups);
					allObjects = allObjects.concat(Rooms);
					allObjects = allObjects.concat(Subjects);
					allObjects = allObjects.concat(Forms);
					allObjects = allObjects.concat(Hours);

					for (i = 0; i < allObjects.length; i++) {
						nameToId[ allObjects[i].Name ] = allObjects[i].Id;
					}
				}	
				
				function GeneratePickList( limitation, ObjectValues, fieldName ) {
					//console.log( limitation +":"+ fieldName )
					pickListName = fieldName + "Items";
					var items = document.getElementById(pickListName);
					var currentSelection = document.getElementById(fieldName).value;
					var currentSelectionFound = false;
					while (items.firstChild) {
						items.removeChild(items.firstChild);
					}
					for (i = 0; i < ObjectValues.length; i++) {
						var occupationKey;
						var occupationKey1;
						var occupationKey2;
						var occupationKey3;
						var skipHour = false;
						var skipDay = false;
						if ("Lecturer#Group#Room".includes(fieldName) ) {
							occupationKey1 = limitation+":"+ObjectValues[i].Id;	
							occupationKey2 = "N/A";
							occupationKey3 = "N/A";
						}
						if ("Day".includes(fieldName) ) {
							occupationKey1 = ObjectValues[i].Name+":"+ document.getElementById("Hour").value +":"+nameToId [document.getElementById("Lecturer").value];							
							occupationKey2 = ObjectValues[i].Name+":"+ document.getElementById("Hour").value +":"+nameToId [document.getElementById("Group").value];							
							occupationKey3 = ObjectValues[i].Name+":"+ document.getElementById("Hour").value +":"+nameToId [document.getElementById("Room").value];
							if (document.getElementById("Hour").value!="")
							  skipDay = !(availableDays[ObjectValues[i].Name+":"+ document.getElementById("Hour").value]);					
						}
						if ("Hour".includes(fieldName) ) {
							occupationKey1 = document.getElementById("Day").value +":"+ ObjectValues[i].Name +":"+nameToId [document.getElementById("Lecturer").value];							
							occupationKey2 = document.getElementById("Day").value +":"+ ObjectValues[i].Name +":"+nameToId [document.getElementById("Group").value];							
							occupationKey3 = document.getElementById("Day").value +":"+ ObjectValues[i].Name +":"+nameToId [document.getElementById("Room").value];							
							if (document.getElementById("Day").value!="")
							  skipHour = !(availableDays[document.getElementById("Day").value +":"+ ObjectValues[i].Name]);					
						}
						if ( (limitation) && ( OccupiedByObject[ occupationKey1 ] || OccupiedByObject[ occupationKey2 ] || OccupiedByObject[ occupationKey3 ] ||skipHour || skipDay ) ) {
							//Do not add this value to the since it is occupied
						} 
						else {
							a = document.createElement('a');
							a.href =  '#';
							a.innerHTML = ObjectValues[i].Name;
							a.className ='dropdown-item';

							//display items next to each other
							a.style.display = 'inline';								
							if (i%5==0) {
								b = document.createElement('br');
								items.appendChild(b);
							}
													
							a.onclick=function() { 
								document.getElementById(fieldName).value = this.innerHTML; 
								if ("Day#Hour".includes(fieldName) ) {
									d = document.getElementById("Day").value;
									h = document.getElementById("Hour").value;
									GeneratePickList(d+":"+h,Lecturers,"Lecturer");
									GeneratePickList(d+":"+h,Groups,"Group");
									GeneratePickList(d+":"+h,Rooms,"Room");
								}
								if ("Day".includes(fieldName) ) {
									GeneratePickList("L or G or R",Hours,"Hour");
								}
								if ("Hour".includes(fieldName) ) {
									GeneratePickList("L or G or R",Days,"Day");
								}
								if ("Lecturer#Group#Room".includes(fieldName) ) {
									GeneratePickList("L or G or R",Days,"Day");
									GeneratePickList("L or G or R",Hours,"Hour");
								}
							};
							if (currentSelection==a.innerHTML) {
								currentSelectionFound = true;
							}
							items.appendChild(a);
						}
					}
					//clear selected value if the value is not on refreshed list
					if (!currentSelectionFound) {
						document.getElementById(fieldName).value = "";
					}
				};
				
				function setupPickLists () {
					initNameToId ();
					GeneratePickList("",Days,"Day");
					GeneratePickList("",Hours,"Hour");
					GeneratePickList("",Lecturers,"Lecturer");
					GeneratePickList("",Groups,"Group");
					GeneratePickList("",Rooms,"Room");
					GeneratePickList("",Subjects,"Subject");
					GeneratePickList("",Forms,"Form");	
				}

				window.onload = setupPickLists;
		  </script>

	
<?php Require_once 'pageFooter.php'; ?>


