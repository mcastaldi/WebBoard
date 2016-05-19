<?
	$email = $password = $type = "";
	$emailErr = $passwordErr = $loginMessage = "";
	$loginAttempted = $loginSuccess = $evtCreation = $loggedInAsUser = $loggedInAsOrg = false;
	$servername = "localhost";
	$dbusername = "root";
	$dbpassword = "root";
	$dbname = "LTUBillboard";
	// Create connection
	$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	if (!empty($_GET['filter'])) {
		$_SESSION['filter'] = $_GET['filter'];
	}
	$endDateEarly = $endTimeEarly = false;
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		if(!empty($_POST['type'])){$type = $_POST['type'];}
		if(strcmp($type,'stu')==0)//logging in as student
		{
			$loginAttempted = true;
			if(empty($_POST['studentEmail']))
				$emailErr = "Email is Required";
			elseif(empty($_POST['studentPassword']))
				$passwordErr = "Password is Required";
			else
			{
				$email = cleanInput($_POST['studentEmail'],$conn);
				$password = cleanInput($_POST['studentPassword'],$conn);
				$sql = "SELECT * FROM user_account WHERE user_email='{$email}' AND login_password='{$password}';";
				$result = $conn->query($sql);
				if($result->num_rows==0){$loginMessage="Login Failed";}
				else
				{
					$loginSuccess = true;
					$userInfo = $result->fetch_assoc();
					$_SESSION['userId'] = $userInfo['userId'];
					$_SESSION["firstName"] = $userInfo['first_name'];
					$_SESSION["lastName"] = $userInfo['last_name'];
					$_SESSION['isAdmin'] = $userInfo['is_admin'];
					$_SESSION['userEmail'] = $userInfo['user_email'];
					$loginMessage = "Login Successful";
					$loggedInAsUser = true;
				}
			}
		}
		if(strcmp($type,'org')==0)//logging in as an organization
		{
			$loginAttempted =true;
			if(empty($_POST['orgEmail']))
				$emailErr = "Email is Required";
			elseif(empty($_POST['orgPassword']))
				$passwordErr = "Password is Required";
			else
			{
				$email = cleanInput($_POST['orgEmail'],$conn);
				$password = cleanInput($_POST['orgPassword'],$conn);
				$sql = "SELECT * FROM ltuorganization WHERE org_email='{$email}' AND login_password='{$password}';";
				$result = $conn->query($sql);
				if($result->num_rows==0){$loginMessage="Login Failed";}
				else
				{
					$loginSuccess = true;
					$orgInfo=$result->fetch_assoc();
					$_SESSION['orgId'] = $orgInfo['orgId'];
					$_SESSION["orgName"] = $orgInfo['org_name'];
					$_SESSION["orgDesc"] = $orgInfo['org_description'];
					$_SESSION['orgWebsite'] = $orgInfo['org_website'];
					$_SESSION['orgEmail'] = $orgInfo['org_email'];
					$loginMessage = "Login Successful";
					$loggedInAsOrg = true;
				}
			}
		}
		if(strcmp($type,'orgCreate')==0)//Creating organiaztion
		{
			$loginAttempted = true;
			$orgName = cleanInput($_POST['orgName'],$conn);
			$orgDesc = cleanInput($_POST['orgDesc'],$conn);
			$orgUrl = cleanInput($_POST['orgUrl'],$conn);
			$orgPassword = cleanInput($_POST['orgCreatePassword'],$conn);
			$orgEmail = cleanInput($_POST['orgEmail'],$conn);
			$sql = "INSERT INTO ltuorganization (org_name,org_description,org_website,login_password,org_email,org_accepted)
			values ('{$orgName}','{$orgDesc}','{$orgUrl}','{$orgPassword}','{$orgEmail}',0);";
			$errorMessage = "";
			if ($conn->query($sql) === TRUE) {
				//echo "New record created successfully";
			} else {
				//echo "Error: " . $sql . "<br>" . $conn->error;
			}
			$sql = "SELECT orgId FROM ltuorganization WHERE org_email='{$orgEmail}' AND login_password='{$orgPassword}';";
			$result = $conn->query($sql);
			if($result->num_rows==0){$loginMessage="Login Failed";}
			else
			{
				$userInfo = $result->fetch_assoc();
				$_SESSION['orgId'] = $userInfo['orgId'];
				$_SESSION["orgName"] = $orgName;
				$_SESSION["orgDesc"] = $orgDesc;
				$_SESSION['orgWebsite'] = $orgWebsite;
				$_SESSION['orgEmail'] = $orgEmail;
				$loggedInAsOrg = true;
			}
		}
		if(strcmp($type,'stuCreate')==0)//Creating organiaztion
		{
			$loginAttempted = true;
			$firstName = cleanInput($_POST['firstName'],$conn);
			$lastName = cleanInput($_POST['lastName'],$conn);
			$stuPassword = cleanInput($_POST['stuCreatePassword'],$conn);
			$stuEmail = cleanInput($_POST['stuEmail'],$conn);
			$sql = "INSERT INTO user_account (first_name,last_name,login_password,is_admin,user_email)
			values ('{$firstName}','{$lastName}','{$stuPassword}',0,'{$stuEmail}');";
	
			if ($conn->query($sql) === TRUE) {
				//echo "New record created successfully";
			} else {
				//echo "Error: " . $sql . "<br>" . $conn->error;
			}
			
			$sql = "SELECT userId FROM user_account WHERE user_email='{$email}' AND login_password='{$password}';";
			$result = $conn->query($sql);
			if($result->num_rows==0){$loginMessage="Login Failed";}
			else
			{
				$userInfo = $result->fetch_assoc();
				$_SESSION['userId'] = $userInfo['userId'];
				$_SESSION["firstName"] = $firstName;
				$_SESSION["lastName"] = $lastName;
				$_SESSION['isAdmin'] = 0;
				$_SESSION['userEmail'] = $email;
				$loggedInAsUser = true;
			}
		}
	}
	else
	{
		//check session to see if logged in and user and get info if true
		if (isset($_SESSION['userId'])){
			$userInfo['userId'] = $_SESSION['userId'];
			$userInfo['firstName'] = $_SESSION["firstName"];
			$userInfo['lastName'] = $_SESSION["lastName"];
			$userInfo['isAdmin'] = $_SESSION['isAdmin'];
			$userId = $userInfo['userId'];
			$message  = $userInfo['firstName'] . " " . $userInfo['lastName'];
			$loggedInAsUser = true;
		} elseif (isset($_SESSION['orgId'])) {
			$orgInfo['id'] = $_SESSION['orgId'];
			$orgInfo['name'] = $_SESSION['orgName'];
			$orgInfo['desc'] = $_SESSION['orgDesc'];
			$orgInfo['website'] = $_SESSION['orgWebsite'];
			$loggedInAsOrg = true;
			$message = $orgInfo['name'];
		} else {
			$message = "No One";
		}
		
	}
	$loggedIn = $loggedInAsOrg || $loggedInAsUser;
	function cleanInput($input,$conn){
		$input = trim($input);
		$input = stripslashes($input);
		$input = htmlspecialchars($input);
		$input = mysqli_real_escape_string($conn,$input);
      	return $input;
	}
	$conn->close();
?>