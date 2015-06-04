<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella
# Improved by Tim Wasson
# 
# This is Free Software released under the GNU/GPL License.
############################################################

########### Security code, avoids cross-site scripting (Register Globals ON)
if (isset($_REQUEST['GLOBALS']) OR isset($_REQUEST['absoluteurl']) OR isset($_REQUEST['amilogged']) OR isset($_REQUEST['theme_path'])) { exit; } 
########### End

### Check if user is logged ###
	if ($amilogged != "true") { exit; }
###

if (isset($_GET['file']) AND $_GET['file']!=NULL) {

	$file = $_GET['file'];
  
  /* Open a connection */
	$mysqli = new mysqli($server, $db_user, $db_pass, $database);
	
	/* check connection */
	if (mysqli_connect_errno()) {
	    printf("Connect failed: %s\n", mysqli_connect_error());
	    exit();
	}
	
	$sql = "SELECT * FROM media WHERE file = '".$file."' GROUP BY file, time, ip, ref, agent";

  $result = $mysqli->query($sql);
    
  $PG_mainbody .= "Total Downloads: ".$result->num_rows;
  
  $PG_mainbody .= "<table class=\"table table-striped\">";
  $PG_mainbody .= "<tr><th>Download Date</th><th>IP</th><th>Agent</th><th>Location</th></tr>";
  
  if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
      //print_r($row['MONTH(time)']);
      
      if(empty($row['city'])) {
        //echo $row['city'];
        
        $dataloc = json_decode(file_get_contents("http://ip-api.com/json/".$row['ip']));
        
        $locu = "UPDATE media SET `lat`='".$dataloc->lat."', `long`='".$dataloc->lon."', `city`='".$dataloc->city."', `region`='".$dataloc->region."', `country`='".$dataloc->country."', `zip`='".$dataloc->zip."' WHERE `ip`='".$row['ip']."'";

        if ($mysqli->query($locu)) {
        //    echo "Record updated successfully";
        } else {
        //    echo "Error updating record: " . mysqli_error($mysqli);
        }
        
      }
      
      $PG_mainbody .= "<tr><td>".date('n/j/Y',strtotime($row['time']))."</td><td>". $row["ip"]. "</td><td>". $row["agent"]. "</td><td>".$row["city"].", ".$row["region"].", ".$row["country"]."</td></tr>";
    }
  }
  
  $PG_mainbody .= "</table>";
} else { 
	$PG_mainbody .="Hi.";
}
?>