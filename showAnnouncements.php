<?php
//Shows current announcements
    function showAnnounce()
    {
    $servername="localhost";
    $username="root";
    $password="root";
    $dbname="LTUBillboard";

    $con=new mysqli($servername, $username, $password, $dbname);
    if($con->connect_error)
    {
        die("Connection failed:" . $con->connect_error);
    }
    $sql="SELECT * FROM admin_announcements WHERE start_date <= cast(now() as date) AND end_date >= cast(now() as date) ORDER BY start_date AND end_date";
    $result=$con->query($sql);
    if($result->num_rows==0)
    {
        echo "<h4>No announcements</h4>";
    }
    else
    {
        while($row=$result->fetch_assoc())
        {	
			if(strcmp($row['ann_type'],'text')==0){
				echo "<li>" . $row['announce_desc'] . "</li>";
				} 
			else if(strcmp($row['ann_type'],'link')==0&&!empty($row['ann_url'])){
				echo "<li><a href='" . $row['ann_url'] ."'>" . $row['announce_desc'] . "</a></li>";
			}
        }
    }
    $con->close();
    }
?>
