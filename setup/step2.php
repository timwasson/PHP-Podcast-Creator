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


$PG_mainbody .= '<h3>'.$SL_checkperm.'</h3>
	<p>'.$SL_step1.'</p>
	<form method="post" action="index.php?step=3">
	<br />
	<input type="hidden" name="setuplanguage" value="'.$_POST['setuplanguage'].'">
	<input type="submit" value="'.$SL_next.'" class="btn btn-primary">
	</form>
	';


//print output

echo $PG_mainbody;

?>