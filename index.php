<?php
	session_start();
	$thisPage = "index.php";
	include 'upcomingEvents.php';
    include 'showAnnouncements.php';
	
	//data validation for logging in
	include 'loginFile.php';
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		
		if(!empty($_POST['type'])){$type = $_POST['type'];}
		if(strcmp($type,'privateEvt')==0)//Creating private event
		{
			$eventSuccess = $evtCreation = true;
			$name = $url = $room = $desc = "";
			$startDate = new DateTime($_POST['evtStartDate']);
			$endDate = new DateTime($_POST['evtEndDate']);
			$startTime = new DateTime($_POST['evtStartTime']);
			$endTime = new DateTime($_POST['evtEndTime']);
			if($endDate < $startDate)
			{
				$endDateEarly = true;
				$eventSuccess = false;
			}
			elseif($endDate == $startDate)
			{
				if($endTime <= $startTime)
				{
					$endTimeEarly = true;
					$eventSuccess = false;
				}
			}
			if(!$eventSuccess)
			{
				if(!empty($_POST['evtName'])){$name = $_POST['evtName'];}
				if(!empty($_POST['evtUrl'])) {$url  = $_POST['evtUrl'];}
				if(!empty($_POST['evtBuildingRoom'])){$room = $_POST['evtBuildingRoom'];}
				if(!empty($_POST['evtDesc'])){$desc = $_POST['evtDesc'];}
			}
			else
			{
				$name = cleanInput($_POST['evtName'],$conn);
				$room = cleanInput($_POST['evtBuildingRoom'],$conn);
				$desc = cleanInput($_POST['evtDesc'],$conn);
				$sql = "INSERT INTO LTUEvents (org_id,is_private, evt_name, evt_room, evt_category,evt_start_date,evt_end_date,evt_start_time,evt_end_time,evt_desc,evt_url,evt_visible)
					VALUES (-{$_SESSION["userId"]},1, '{$name}', '{$room}', 'User Created',
					'{$_POST["evtStartDate"]}','{$_POST["evtEndDate"]}', '{$_POST["evtStartTime"]}', '{$_POST["evtEndTime"]}', '{$desc}','N/A',1)";
			
				if ($conn->query($sql) === TRUE) {
				} else {
					echo "Error: " . $sql . "<br>" . $conn->error;
				}
			}
		}
		if(strcmp($type,'addToCal')==0)//Adding event to calendar
		{
			$sql = "INSERT INTO user_event_join (userId,eventId) VALUES ({$_SESSION['userId']},{$_POST['toggleOnCalEvtId']});";
			if ($conn->query($sql) === TRUE) 
			{} 
			else 
			{
					echo "Error: " . $sql . "<br>" . $conn->error;
			}
		}
		if(strcmp($type,'removeFromCal')==0)//Adding event to calendar
		{
			$sql = "DELETE FROM user_event_join WHERE userId = {$_SESSION['userId']} AND eventId = {$_POST['toggleOnCalEvtId']};";
			if ($conn->query($sql) === TRUE) 
			{
			} 
			else 
			{
					echo "Error: " . $sql . "<br>" . $conn->error;
			}
		}
		if(strcmp($type,'deleteEvent')==0)//Adding event to calendar
		{
			$sql = "DELETE FROM ltuevents WHERE org_id = -{$_SESSION['userId']} AND eventId = {$_POST['toggleOnCalEvtId']};";
			if ($conn->query($sql) === TRUE) 
			{
			} 
			else 
			{
					echo "Error: " . $sql . "<br>" . $conn->error;
			}
		}
	}
	
	if($loggedInAsUser){
		$userInfo['events'] = array();
		$sql = "SELECT eventId FROM user_event_join WHERE userId = {$userInfo['userId']};";
		$result=$conn->query($sql);
		if($result->num_rows==0){}
		else
		{
			while($row = $result->fetch_array())
			{
				array_push($userInfo['events'],$row['eventId']);
			}
		}
	}
	$conn->close();
	
	//Checking if mobile user
	require_once 'mobile_detect.php';//required file for checking for mobile
	$detect = new Mobile_Detect;//variable for mobile detection
	$isMobile = $detect->isMobile();
	if($detect->isMobile()){echo "ismobile";}//if mobile
	if($detect->isTablet()){}//if tablet
	//http://mobiledetect.net/
	$filterSet= isset($_SESSION['filter']);
	if($filterSet)
		$filter = $_SESSION['filter'];
?>
<!DOCTYPE html>
<html>
<head>
	<title>LTU Billboard</title>
	<link href="fcStylesheet.css" rel="stylesheet" type="text/css" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	
	<link href="bootstrap.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="jquery-2.2.2.min.js"></script>
	<link rel='stylesheet' href='fullcalendar.css' />
	<script src='jquery.min.js'></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.15.0/jquery.validate.min.js"></script>
	<script src='moment.min.js'></script>
	<script src='fullcalendar.js'></script>
	<script type="text/javascript" src="bootstrap.min.js"></script>
	<link rel="icon" href="favicon.ico"/>

	<script type="text/javascript">
			var eventsAddedToCal = [ 
		<?php if($loggedInAsUser): ?>
				<?php foreach($userInfo['events'] as $anEventId)
						{
							echo "'{$anEventId}',";
						}?>
			<?php endif;?>
			];
		$(document).ready(function() {
			
			
			$('#calendar').fullCalendar({
				eventClick:  function(event, jsEvent, view) {
					var privateEvent = event.org_id < 0;
					$('#eventModalLabel').html(event.title);
					$('#modalDesc').html(event.desc);
					$('#modalDate').html(event.date);
					$('#modalStartTime').html(event.start_time);
					$('#modalEndtime').html(event.end_time);
					$('#modalOrgName').html(event.org_name);
					$('#modalRoom').html(event.room)
					if(privateEvent)
					{
						$('#modalLinkDiv').html("User Created Event");
						$('#modalEvtLink').hide();
					}
					else
					{
						$('#modalLinkDiv').html("Link: <a id='modalEvtLink' target='_blank'>Click Here</a>");
						$('#modalEvtLink').attr('href',event.link);
						//$('#modalEvtLink').show();
					}
					$('.toggleOnCalEvtId').val(event.id);
					var added = $.inArray(event.id,eventsAddedToCal)>=0 ? true : false;
					$('#eventAdded').val(added);
					$('.toggleOnCal').hide();
					if(added)
						$("#removeFromCal").show();
					else if(privateEvent)
						$("#delete").show();
					else
						$("#addToCal").show();
					$('#eventModal').modal();
					return false;
				},
				header:{
				  left: "prev,next today",
				  center:"title",
				  right:"month,basicWeek,basicDay"
				},
				eventLimit:true,
				eventColor:"#004299",
				timeFormat:"h:mmt",
				eventSources:[
				  {
					url:'fcEvents.php',	
				  },
				]
			})//end of full calendar
			
			//used for create account panel radio buttons
			$("#orgAct").hide();
			$("input[name=actType]").on( "change", function() {
				var target = $(this).val();
				$(".chooseActType").hide();
				$("#"+target).show();
			});
			
			//validation for student account creation
			$("#createStuAct").validate({
				"rules" : {
					"confirmStuPassword" : {
						"equalTo" : "#stuCreatePassword"}
				}
			});
			$("#createOrgAct").validate({
				rules : {
					confirmOrgPassword : {
						equalTo : "#orgCreatePassword"}
				}
			});
			$("#studentForm").validate({});
			$("#orgForm").validate({});
			
			
			$("#createAccountLink").on("click", function(){
				$('#loginModal').modal('show');
				$('#loginTabs a:last').tab('show');
			});
			<?php if($loginAttempted):?>
				<?php if(strcmp($type,'stu')==0):?>
					<?php if(!$loginSuccess):?>
					//login student fail
					$('#loginModal').modal('show');
					$('#stuLoginMessage').html("Login Failed");
					$('#stuLoginMessage').toggleClass('error');
					<?php endif; ?>
				<?php elseif(strcmp($type,'org')==0): ?>
					<?php if(!$loginSuccess):?>
					$('#loginModal').modal('show');
					$('#loginTabs a[href="#loginAsOrg"]').tab('show')
					$('#orgLoginMessage').html("Login Failed");
					$('#orgLoginMessage').toggleClass('error');
					<?php endif;?>
				<?php endif;?>
			<?php endif?>
			
			//used for the calendar filter
			$("#selectId").on( "change", function(){
				$("#dropdown").submit();
			});
			<?php if($evtCreation):?>
				<?php if($eventSuccess):?>
					//private event success
					$('#privateEventModal').modal('show');
					$('#privateEvtMessage').html("Event Creation Successful");
					$('#privateEvtMessage').toggleClass('success');
				<?php else: ?>
					//private event success
					$('#privateEventModal').modal('show');
					$('#privateEvtMessage').html("Event Creation Failed");
					$('#privateEvtMessage').toggleClass('error');
				<?php endif; ?>
			<?php endif;?>
			
			
			
		});//end of doc.ready
	</script>	
	</head>
	<body>
<?php require 'requiredHeader.php';?>
  <div id="theWrap">
    <div id="topWrap">
      <div class="subheader" id="firstSubheader">
        <span class="subTitle">Announcements</span>
        <ul id="currAnnounce"><?php showAnnounce(); ?></ul>
      </div>
      <div class="subheader">
        <span class="subTitle">Upcoming Events</span>
        <ul id="currEvents"><?php upcomingEvents(); ?></ul>
      </div>
    </div>
    <div id="calendarWrapper">
      <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" id="dropdown">
        Event Filter:&nbsp;
        <select id="selectId" name="filter">
			<option value="non" <?php if($filterSet){if(strcmp($filter,'non')==0){echo "selected";}}?> >Show All</option>
			<option value="arc" <?php if($filterSet){if(strcmp($filter,'arc')==0){echo "selected";}}?> >Architecture + Design</option>
			<option value="mcs" <?php if($filterSet){if(strcmp($filter,'mcs')==0){echo "selected";}}?> >Arts + Science</option>
			<option value="eng" <?php if($filterSet){if(strcmp($filter,'eng')==0){echo "selected";}}?> >Engineering</option>
			<option value="stu" <?php if($filterSet){if(strcmp($filter,'stu')==0){echo "selected";}}?> >Student Interests</option>
			<?php if($loggedInAsUser){
					if($filterSet){
						if(strcmp($filter,'add')==0)
						{
							echo "<option value='add' selected>Added to Calendar</option>";
							echo "<option value='org'>My Organizations</option>";
							echo "<option value='mine'>My Events</option>";
						}
						elseif(strcmp($filter,'org')==0)
						{
							echo "<option value='add'>Added to Calendar</option>";
							echo "<option value='org' selected>My Organizations</option>";
							echo "<option value='mine'>Private Events</option>";
						}
						elseif(strcmp($filter,'mine')==0)
						{
							echo "<option value='add'>Added to Calendar</option>";
							echo "<option value='org'>My Organizations</option>";
							echo "<option value='mine' selected>Private Events</option>";
						}
						else
						{ 
							echo "<option value='add'>Added to Calendar</option>";
							echo "<option value='org'>My Organizations</option>";
							echo "<option value='mine'>Private Events</option>";
						}
					}
					else
					{
						echo "<option value='add'>Added to Calendar</option>";
						echo "<option value='org'>My Organizations</option>";
						echo "<option value='mine'>Private Events</option>";
					}
				}?>
        </select>
      </form>
	  <?php if ($loggedInAsUser): ?>
	  <div id = "privateEvtId">
		Add your own event to the calendar here: <button id="privateEvtButton" data-toggle="modal" data-target="#privateEventModal">Add Event</button>
	  </div>
	  <?php endif?>
    </div>
    <div id="calWrap">
      <div id='calendar'></div>
    </div>
  </div>
	<div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="eventModalLabel">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h2 class="modal-title" id="eventModalLabel"></h2>
		  </div>
		  <div class="modal-body">
		  <input type="hidden" name="eventAdded" id="eventAdded" value="">
			<div id="modalDesc" align="center"></div>
			<br />
			<div class="row">
				<div class="col-sm-6" align="center"><span id="modalDate"></span><br /><span id="modalStartTime"></span> to <span id="modalEndtime"></span></div>
				<div class="col-sm-6" align="center">Organization: <span id="modalOrgName"></span></div>
			</div>
			<br />
			<div class="row">
				<div class="col-sm-6" align="center">Room: <span id="modalRoom"></span></div>
				<div class="col-sm-6" align="center" id="modalLinkDiv">Link: <a id="modalEvtLink" target="_blank">Click Here</a></div>
			</div>
		  </div>
		  <div class="modal-footer">
			<?php if($loggedInAsUser): ?>
			<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" role="form" id="addToCal" class="toggleOnCal">
				<input type="hidden" name="toggleOnCalEvtId"  class="toggleOnCalEvtId" value="">
				<button type="submit" class="btn btn-primary" name="type" value="addToCal">Add To Calendar</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</form>
			<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" role="form" id="removeFromCal" class="toggleOnCal">
				<input type="hidden" name="toggleOnCalEvtId"  class="toggleOnCalEvtId" value="">
				<button type="submit" class="btn btn-primary" name="type" value="removeFromCal">Remove From Calendar</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</form>
			<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" role="form" id="delete" class="toggleOnCal">
				<input type="hidden" name="toggleOnCalEvtId"  class="toggleOnCalEvtId" value="">
				<button type="submit" class="btn btn-primary" name="type" value="deleteEvent">Delete Event</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</form>
			<?php else:?>
				Log in to add to calendar
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			<?php endif;?>
		  </div>
		</div>
	  </div>
	</div>
	<div class="modal fade" id="privateEventModal" tabindex="-1" role="dialog" aria-labelledby="privateEventModalLabel">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h2 class="modal-title" id="privateEventModalLabel">Create Private Event</h2>
		  </div>
		  <div class="modal-body">
			<div align="center">Here you can add your own event to your calendar.<br />It won't show up on anyone else's calendar.</div>
			<br />
			<div id="privateEvtMessage" class="message" align="center"></div><br />
			<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" role="form" id="privateEventForm">
				
				<div class="form-group row">
					<label for="evtName" class="col-sm-3 form-control-label" align="right">Name</label>
					<div class="col-sm-3">
						<input required type="text" class="form-control" id="evtName" name ="evtName" placeholder="Name" />
					</div>
					<label for="evtBuildingRoom" class="col-sm-2 form-control-label" align="right">Location:</label>
					<div class="col-sm-3">
						<input type="text" class="form-control" id="evtBuildingRoom" name="evtBuildingRoom" placeholder="Location" />
					</div>
				</div>
				<div class="form-group row">
					<label for="evtStartDate" class="col-sm-3 form-control-label" align="right">Start/End Date:</label>
					<div class="col-sm-4">
						<input type="date" class="form-control" id="evtStartDate" name="evtStartDate" />
					</div>
					<div class="col-sm-4">
						<input type="date" class="form-control" id="evtEndDate" name="evtEndDate" />
					</div>
				</div>
				<?php if($endDateEarly): ?>
				<div class="form-group row">
					<span class="col-sm-5 error" align="right">End date must be after start date</span>
				</div>
				<?php endif; ?>
				<div class="form-group row">
					<label for="evtStartTime" class="col-sm-3 form-control-label" align="right">Start/End Time:</label>
					<div class="col-sm-4">
						<input type="time" class="form-control" id="evtStartTime" name="evtStartTime" min="0"/>
					</div>
					<div class="col-sm-4">
						<input type="time" class="form-control" id="evtEndTime" name="evtEndTime" min ="0" />
					</div>
				</div>
				<?php if($endTimeEarly): ?>
				<div class="form-group row">
					<span class="col-sm-5 error" align="right">End time must be after time date</span>
				</div>
				<?php endif; ?>
				<div class="form-group row">
					<label for="evtDesc" class="col-sm-3 form-control-label" align="right">Description:</label>
					<div class="col-sm-8">
						<textarea class="form-control" id="evtDesc" name="evtDesc" rows="3" placeholder="Add any extra information for yourself."></textarea>
					</div>
				<div class="col-sm-1">
					<input type="hidden" class="form-control" id="evtUserId" name="evtUserId" value=<?php echo $userId;?>></input>
				</div>
				</div>
		  </div>
		  <div class="modal-footer">
			<button type = "submit" class ="btn btn-primary" id = "submit" name="type" value="privateEvt">Add to Calendar</button>
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		  </div>
		  </form>
		</div>
	  </div>
	</div>		
  <div id="bottomWrapper">
    <footer>
      Created By Matthew Castaldini
    </footer>
  </div>
</body>
</html>
