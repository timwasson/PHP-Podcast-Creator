<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella
# Improved by Tim Wasson
# 
# This is Free Software released under the GNU/GPL License.
############################################################

include ('checkconfigexistence.php');

$PG_mainbody = NULL; //define

if(isset($_POST['setuplanguage'])) {
	$_SESSION['setuplanguage'] = $_POST['setuplanguage'];
}

$PG_mainbody = "<h2>mySQL Setup</h2>";

$PG_mainbody .= "<p>mySQL is used for tracking episode and feed downloads.</p>";

$PG_mainbody .= "
	<fieldset>
    <legend>mySQL Login Information</legend>
        
	<form action=\"index.php?step=3\" method=\"post\">
	
	<label>Server</label>
	<input type=\"text\" name=\"server\" value=\"localhost\">
	
	<label>Database</label>
	<input type=\"text\" name=\"database\" placeholder=\"Database\">
	
	<label>Database User</label>
	<input type=\"text\" name=\"db_user\" placeholder=\"Database User\">
	
	<label>Database Password</label>
	<input type=\"password\" name=\"db_pass\" placeholder=\"Database Password\">
	<div class=\"form-actions\">
		<input type=\"submit\" class=\"btn btn-primary\">
	</div>
	</form>
	</fieldset>
";



//print output

echo $PG_mainbody;

?>