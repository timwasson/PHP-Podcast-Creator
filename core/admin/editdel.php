<?php
############################################################
# PHP PODCAST CREATOR
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

if (isset($_GET['p'])) if ($_GET['p']=="admin") { // if admin is called from the script in a GET variable - security issue

	$PG_mainbody .= "<h3>$L_admin_editdel</h3>";

  /* Open a connection */
	$mysqli = new mysqli($server, $db_user, $db_pass, $database);
	
	/* check connection */
	if (mysqli_connect_errno()) {
	    printf("Connect failed: %s\n", mysqli_connect_error());
	    exit();
	}
	
	$sql = "SELECT * FROM Episodes ORDER BY DATE(date) DESC";

  $result = $mysqli->query($sql);
  
  if ($result->num_rows > 0) {
    $PG_mainbody .= '<table class="table table-striped">
				<tr>
					<th>Image</th>
					<th>Title</th>
					<th>Episode Date</th>
					<th>Edit</th>
					<th>Delete</th>
				</tr>';
    
    // output data of each row
    while($row = $result->fetch_assoc()) {
      $PG_mainbody .= '
							<tr>
							<td>';
							
      if(!empty($row['image'])) {
		    $PG_mainbody .= '<img src="/images/'.$row['image'].'" style="height:100px" />';
		  } else {
		    $PG_mainbody .= '<img src="http://placehold.it/100x100&text=No+Image">';
		  }

      $PG_mainbody .= '</td>
							<td>'.$row['title'].'</td>
							<td>'.$row['date'].'</td>
							<td><a href="?p=admin&do=edit&amp;name='.$row['filename'].'"><i class="icon-edit"></i></a></td>';
									
		  // Generate the Delete URL
		  $delURL = '?p=admin&do=delete&file='.$row['filename'];
      
		  if ($row['image'] != NULL) {
		    $delURL .= '&img='.$row['image'];
		  }

      $PG_mainbody .= '
							<td><a href="#myModal" data-toggle="modal" class="delep" data-delurl='.$delURL.'><i class="icon-remove"></i></a></td>
							</tr>';
		}
		$PG_mainbody .= "</table>";
	} else { 
		$PG_mainbody .= '<div class="topseparator"><p>'.$L_dir.' <b>'.$upload_dir.'</b> '.$L_empty.'</p><p><a href="?p=admin&do=upload">'.$L_uploadanepisode.'</a></p></div>';
	}
	$PG_mainbody .= '

  <!-- Modal -->
  <div class="modal fade" id="myModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">'.$L_deleteconfirmation.'</h4>
        </div>
        <div class="modal-body">
         <p>You cannot undo this.</p>
        </div>
        <div class="modal-footer">
          <a href="#" class="btn btn-link" data-dismiss="modal">Close</a>
          <a id="delurl" class="btn btn-danger" href=""><i class="icon-warning-sign"></i> '.$L_delete.'</a>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->';

} //end if admin
?>