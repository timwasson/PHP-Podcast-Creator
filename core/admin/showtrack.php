<?
$loadjavascripts .= '
	<script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          [\'Month\', \'Downloads\'],';
    
    /* Open a connection */
	$mysqli = new mysqli($server, $db_user, $db_pass, $database);
	
	/* check connection */
	if (mysqli_connect_errno()) {
	    printf("Connect failed: %s\n", mysqli_connect_error());
	    exit();
	}
   
	$i = -6;
	while ($i <= 0):
	    
	    $query = "SELECT * FROM feed WHERE YEAR(time) = YEAR(CURRENT_DATE + INTERVAL ".$i." MONTH) AND MONTH(time) = MONTH(CURRENT_DATE + INTERVAL ".$i." MONTH)";
		if ($stmt = $mysqli->prepare($query)) {
		
		    /* execute query */
		    $stmt->execute();
		
		    /* store result */
		    $stmt->store_result();

			$loadjavascripts .= '["'.date("F",strtotime($i." Months")).'",'.$stmt->num_rows.'],';
			
		    /* close statement */
		    $stmt->close();
		}
		
	    $i++;
	endwhile;
	
	$mysqli->close();
	
	$loadjavascripts .= '
        ]);

        var options = {
          title: \'Feed Downloads\',
          colors: [\'#c00\']
        };

        var chart = new google.visualization.LineChart(document.getElementById(\'feeddown\'));
        chart.draw(data, options);
      }
    </script>';
    
    
    
    // This loads all the Google Chart on the front page of the admin panel for the total number of Episode downloads.
    // Get the Episode Array
    
	function directoryToArray($directory) {
	    $array_items = array();
	    if ($handle = opendir($directory)) {
	        while (false !== ($file = readdir($handle))) {
	        	$fileext = explode(".", $file);
	        	// Ignore .XML files and .DS_Store
	            if ($file != "." && $file != ".." && $fileext[1] != "xml" && $file != ".DS_Store") {
	                if (is_dir($directory. "/" . $file)) {
	                    $file = $file;
	                    $array_items[] = preg_replace("/\/\//si", "/", $file);
	                } else {
	                    $file = $file;
	                    $array_items[] = preg_replace("/\/\//si", "/", $file);
	                }
	            }
	        }
	        closedir($handle);
	    }
	    return $array_items;
	}
    $files = directoryToArray("./media");

	$loadjavascripts .= '
	<script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          [\'Month\', 
          ';
          	foreach ($files as $file) {
				$loadjavascripts .= '\''.$file.'\',';
			}
          
    $loadjavascripts .= '],';
    
    /* Open a connection */
	$mysqli = new mysqli($server, $db_user, $db_pass, $database);
	
	/* check connection */
	if (mysqli_connect_errno()) {
	    printf("Connect failed: %s\n", mysqli_connect_error());
	    exit();
	}


	$i = -6;
	while ($i <= 0):
	    
	    $loadjavascripts .= '["'.date("F",strtotime($i." Months")).'",';
	    
	    foreach ($files as $file) {   
		    $query = "SELECT * FROM media WHERE YEAR(time) = YEAR(CURRENT_DATE + INTERVAL ".$i." MONTH) AND MONTH(time) = MONTH(CURRENT_DATE + INTERVAL ".$i." MONTH) AND file = '".$file."'";
			if ($stmt = $mysqli->prepare($query)) {
			
			    /* execute query */
			    $stmt->execute();
			
			    /* store result */
			    $stmt->store_result();
	
				$loadjavascripts .= $stmt->num_rows.',';
				
			    /* close statement */
			    $stmt->close();
			}
			
		}
		$loadjavascripts .= '],';
	    $i++;
	endwhile;
	
	$mysqli->close();
	
	$loadjavascripts .= '
        ]);

        var options = {
          title: \'Episode Downloads\'
        };

        var chart = new google.visualization.LineChart(document.getElementById(\'epdown\'));
        chart.draw(data, options);
      }
    </script>';
?>