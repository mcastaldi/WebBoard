<?php
	session_start();//session control
	$thisPage = "organizations.php";//page for logout redirection
	
	include 'loginFile.php';
	//get org id to show info
	if (!empty($_GET['orgId'])) {
		$orgInfo['id'] = $_GET['orgId'];
	}
	
	//data validation for logging in
	$endDateEarly = $endTimeEarly = false;
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		/*get the type of post request. Option: Request
			stu: 				for logging in as student
			org: 				for logging in as organization
			orgCreate: 			for creating organization account
			stuCreate:			for creating student account
			changeOrgEmail: 	for changing organization email
			changeOrgPassword: 	for changing organization password
		*/
		if(!empty($_POST['type'])){$type = $_POST['type'];}
		if(strcmp($type,'changeOrgPassword')==0)
		{
			$newPassword = cleanInput($_POST['changeOrgPassword'],$conn);
			$sql = "UPDATE ltuorganization SET login_password = '{$newPassword}' WHERE org_email='{$_SESSION['orgEmail']}' AND login_password='{$_SESSION['orgPassword']}';";
			if ($conn->query($sql) === TRUE) {
				$orgInfo['password'] = $newPassword;
				$_SESSION['orgPassword'] = $newPassword;
			} else {
				//echo "Error: " . $sql . "<br>" . $conn->error;
			}
		}
		if(strcmp($type,'changeOrgDesc')==0)
		{
			$newDesc = cleanInput($_POST['changeOrgDesc'],$conn);
			$sql = "UPDATE ltuorganization SET login_password = '{$newDesc}' WHERE org_email='{$_SESSION['orgEmail']}' AND login_password='{$_SESSION['orgPassword']}';";
			if ($conn->query($sql) === TRUE) {
				$orgInfo['desc'] = $newDesc;
				$_SESSION['orgDesc'] = $newDesc;
			} else {
				//echo "Error: " . $sql . "<br>" . $conn->error;
			}
		}
		if(strcmp($type,'deleteEvent')==0)
		{
			$evtId = cleanInput($_POST['deleteEvtId'],$conn);
			$sql = "DELETE FROM ltuevents where eventId={$evtId};";
			if ($conn->query($sql) === TRUE) {} 
			else {
				//echo "Error: " . $sql . "<br>" . $conn->error;
			}
		}
		if(strcmp($type,'unfollowOrg')==0)
		{
			$sql = "DELETE FROM user_org_join WHERE userId={$_SESSION['userId']} AND orgId = {$_POST['unFollowOrgId']};";
			if ($conn->query($sql) === TRUE) {
			} 
			else {
				echo "Error: " . $sql . "<br>" . $conn->error;
			}
		}
		if(strcmp($type,'followOrg')==0)
		{
			$sql = "INSERT INTO user_org_join (userId,orgId) VALUES ({$_SESSION['userId']},{$_POST['followOrgId']});";
			if ($conn->query($sql) === TRUE) {}
			else {
				echo "Error: " . $sql . "<br>" . $conn->error;
			}
		}
	}
	require 'getOrgsScript.php';
	$followsOrg = false;
	//check session to see if logged in and user and get info if true
	
	if($loggedInAsUser){
		$followedOrgs = getOrgs($userInfo['userId'],$conn);//get followed orgs information
		$numFollowedOrgs = 0;
		if(!empty($followedOrgs) && !empty($orgInfo))//get how many orgs the user follows
		{
			$numFollowedOrgs = count($followedOrgs);
			foreach($followedOrgs as $followedOrgInfo){
				if($followedOrgInfo['orgId']==$orgInfo['id'])
					$followsOrg = true;
				
			}
		}
	}
	//get events hosted by either org that is logged in, or one the user wants to see
	$numEvents = 0;
	if(!empty($orgInfo['id']))
	{
		$sql = "SELECT * FROM ltuevents WHERE org_id = {$orgInfo['id']} AND evt_start_date > CURDATE() ORDER BY evt_start_date;";
		$result = $conn->query($sql);
		$numEvents  =$result->num_rows;
		if($numEvents==0){}
		else
		{
			$orgInfo['eventArray']= array();
			while($row=$result->fetch_assoc())
			{
				array_push($orgInfo['eventArray'],$row);//put event info into array
			}
		}
	}
	else{$orgInfo['id']=0;}
	
	//get all organizations
	$numOrgs = 0;
	$sql = "SELECT orgId, org_name, org_description, org_website, org_email, org_accepted FROM ltuorganization;";
	$result = $conn->query($sql);
	$numOrgs = $result->num_rows;
	if($numOrgs==0){}
	else
	{
		$orgsArray= array();
		while($row=$result->fetch_assoc())
		{
			array_push($orgsArray,$row);//put event info into array
			if(empty($orgInfo['name'])){
				if($row['orgId']==$orgInfo['id'])
					$orgInfo['name'] = $row['org_name'];
			}
		}
		if(empty($orgInfo['name']))
			$orgInfo['name'] = "No organization selected";
	}
	$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<link rel="icon" href="favicon.ico"/>
		<link href="stylesheet.css" rel="stylesheet" type="text/css" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<link href="bootstrap.css" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="jquery-2.2.2.min.js"></script>
		<script type="text/javascript" src="bootstrap.min.js"></script>
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.15.0/jquery.validate.min.js"></script>
		<script type="text/javascript">
			$(document).ready(function() {
				
				//used for choosing org
				$("#chooseOrgId").on( "change", function(){
					$("#selectOrgForm").submit();
				});
				
				$("#orgAct").hide();
				$("input[name=actType]").on( "change", function() {
					var target = $(this).val();
					$(".chooseActType").hide();
					$("#"+target).show();
				});
				
				$("#passwordForm").validate({
					"rules" : {
						"confirmChangeOrgPassword" : {
							"equalTo" : "#changeOrgPassword"}
					}
				});
				
				$("#receiveEmails").on( "change", function(){
					$("#receiveEmailsForm").submit();
				});
				
				//validation for account creation and logging in
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
				
				//directly open the create account tab
				$("#createAccountLink").on("click", function(){
					$('#loginModal').modal('show');
					$('#loginTabs a:last').tab('show');
				});
				
				//open the login modal and show error if login fails
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
						//login org fail
						$('#loginModal').modal('show');
						$('#loginTabs a[href="#loginAsOrg"]').tab('show')
						$('#orgLoginMessage').html("Login Failed");
						$('#orgLoginMessage').toggleClass('error');
						<?php endif;?>
					<?php endif;?>
				<?php endif?>
			});
		</script>
		<title>Account Settings</title>
	</head>
	<body>
		<?php require 'requiredHeader.php'?>
		
		<?php if($loggedInAsOrg)://section for if logged in as organization. Has options to change account info and delete events.?>
		<div class="form-group row pageMessage">
			<div class="col-sm-5" align="center">
				You're logged in as: <?php echo $message?>,<br />Below you can change your organization's information.<br/>
				<?php echo $orgInfo['orgAccepted'] ? "Your organization have been approved by administration" : "Your organization is still awaiting approval.";?>
			</div>
		</div>
		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method ="post" role="form" id="emailForm">
			<!-- Username row -->
			<div class="form-group row">
				<label for="changeOrgEmail" class="col-sm-1 form-control-label" align="right">Email:</label>
				<div class="col-sm-2">
					<input required type="email" class="form-control" id="changeOrgEmail" name="changeOrgEmail" value="<?php echo $orgInfo['email']?>" />
				</div>
				<div class="col-sm-1">
					<button type = "submit" class ="btn btn-primary" id = "submit" name="type" value="changeOrgEmail">Change</button>
				</div>
			</div>
		</form>
		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method ="post" role="form" id="passwordForm">
			<!-- Password row -->
			<div class="form-group row">
				<label for="changeOrgPassword" class="col-sm-1 form-control-label" align="right">Password:</label>
				<div class="col-sm-2">
					<input required type="password" class="form-control" id="changeOrgPassword" name="changeOrgPassword" value="<?php echo $orgInfo['password']?>" />
				</div>
				<div class="col-sm-2">
					<input required type="password" class="form-control" id="confirmChangeOrgPassword" name="confirmChangeOrgPassword" placeholder="Repeat new password" />
				</div>
				<div class="col-sm-1">
					<button type = "submit" class ="btn btn-primary" id = "submit" name="type" value="changeOrgPassword">Change</button>
				</div>
			</div>
		</form>
		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method ="post" role="form" id="orgDescForm">
			<div class="form-group row">
				<label for="changeOrgDesc" class="col-sm-1 form-control-label" align="right">Description:</label>
				<div class="col-sm-4">
					<textarea required type="email" class="form-control" id="changeOrgEmail" name="changeOrgEmail"><?php echo $orgInfo['desc'];?></textarea>
				</div>
				<div class="col-sm-1">
					<button type = "submit" class ="btn btn-primary" id = "submit" name="type" value="changeOrgDesc">Change</button>
				</div>
			</div>
		</form>
		<?php else: //section for if logged in as user or not logged in. Shows dropdown to select an org to view information, or shows information of the org chosen.?>
		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="get" role="form" id="selectOrgForm">
			<div class="form-group row pageMessage">
				<div class="col-sm-5" align="center">
				Pick an Organization: 
					<select id="chooseOrgId" name="orgId">
					<option value="0">Pick One</option>
					<?php foreach($orgsArray as $org):?>
						<option value="<?php echo $org['orgId']?>" <?php echo $org['orgId']==$orgInfo['id'] ? "selected" : ""?>><?php echo $org['org_name']?></option>
						<?php if($org['orgId']==$orgInfo['id']){$orgInfo['name']=$org['org_name'];}?>
						
					<?php endforeach;?>
					</select>
				</div>
			</div>
		</form>
		<?php endif;?>
		
		<div class="form-group row">
			<div class="col-sm-4 pageMessage" align="right">
				Below are events hosted by: <?php echo $orgInfo['name'];?>.
			</div>
			<?php if($loggedInAsUser && (strcmp($orgInfo['name'],"No organization selected")!=0)):?>
			<?php if($followsOrg):?>
			<div class="col-sm-1" align="left">
				<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method ="post" role="form" id="unfollow">
					<input type="hidden" name="unFollowOrgId" value="<?php echo $orgInfo['id'];?>" />
					<button class="button" id='followButton' type="submit" name="type" value="unfollowOrg">Unfollow</button>
				</form>
			</div>
			<?php else:?>
			<div class="col-sm-1" align="left">
				<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method ="post" role="form" id="follow">
					<input type="hidden" name="followOrgId" value="<?php echo $orgInfo['id'];?>" />
					<button class="button" id='followButton' type="submit" name="type" value="followOrg">Follow</button>
				</form>
			</div>
			<?php endif;?>
			<?php endif;?>
		</div>
		<?php if($numEvents>0):?>
			<?php foreach($orgInfo['eventArray'] as $eventInfo)://loops through each org followed?>
			<div class="form-group row">
				<div class="col-sm-2" align="right">
					Name: <?php echo $eventInfo['evt_name'];?>
				</div>
				<div class="col-sm-2" align="right">
					Start Date: <?php echo $eventInfo['evt_start_date'];?>
				</div>
				<div class="col-sm-2" align="left">
					<a href='index.php'><button class="button" id='infoButton'>More Info</button></a>
				</div>
		
				<?php if($loggedInAsOrg):?>
				<div class="col-sm-1" align="left">
					<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method ="post" role="form" id="deleteEvent">
						<input type="hidden" name="deleteEvtId" value="<?php echo $eventInfo['eventId'];?>" />
						<button class="button" id='deleteButton' type="submit" name="type" value="deleteEvent">Delete</button>
					</form>
				</div>
				<?php endif;?>
			</div>
			<?php endforeach;?>
		<?php else:?>
		<div class="form-group row pageMessage">
			<div class="col-sm-5" align="center">
				This organization doesn't host any events
			</div>
		</div>
		<?php endif;?>
		<!--<pre>
		<?php //print_r($orgInfo['eventArray']);?>
		</pre>-->
		<div id="bottomWrapper">
			<footer>
			  Created By Matthew Castaldini
			</footer>
		</div>
	</body>
</html>
