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

$PG_mainbody = "<h2>mySQL Setup</h2>";

$PG_mainbody .= "<p>This is set up your mySQL login and password. For now this doesn't work.</p>";

$PG_mainbody .= "<form action=\"index.php?step=3\" method=\"post\">
	<input type=\"text\">
	<input type=\"text\">
	<input type=\"text\">
	<input type=\"hidden\" name=\"setuplanguage\" value=\"".$setuplang."\">
	<div class=\"form-actions\">
		<input type=\"submit\" class=\"btn btn-primary\">
	</div>
	</form>
";



//print output

echo $PG_mainbody;

?>