<?php
	//Add new announcement
	$sourcePage = $_POST['source'];
    $newAnnouncement=$_POST['addAnn'];
	$annStart = $_POST['announceStart'];
	$annEnd = $_POST['announceEnd'];
    $servername="localhost";
    $username="root";
    $password="root";
    $dbname="LTUBillboard";

    $con=new mysqli($servername, $username, $password, $dbname);
    if($con->connect_error)
    {
        die("Connection failed:" . $con->connect_error);
    }
	else
	{
        $sql="INSERT INTO admin_announcements (announce_desc,start_date,end_date) VALUES ('{$newAnnouncement}','{$annStart}','{$annEnd}')";
        if ($con->query($sql) === FALSE)
        {
            echo "Error: " . $sql . "<br>" . $con->error;
        } 
        else
        {
			//echo "New record created successfully";
			
        $con->close();
        header("Location: {$sourcePage}");
        }
	}
?>
