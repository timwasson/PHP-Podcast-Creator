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
	';

include	('set_permissions.php');


//print output

echo $PG_mainbody;

?>