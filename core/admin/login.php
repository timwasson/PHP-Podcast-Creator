<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella
# http://podcastgen.sourceforge.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################

########### Security code, avoids cross-site scripting (Register Globals ON)
if (isset($_REQUEST['GLOBALS']) OR isset($_REQUEST['absoluteurl']) OR isset($_REQUEST['amilogged']) OR isset($_REQUEST['theme_path'])) { exit; } 
########### End

// define login form
$loginform ='<form class="form-horizontal" id="login" action="?p=admin" method="post">
  <div class="control-group">
    <label class="control-label" for="user">'.$L_user.'</label>
    <div class="controls">
      <input type="text" id="user" name="user" placeholder="'.$L_user.'">
    </div>
  </div>
  <div class="control-group">
    <label class="control-label" for="password">'.$L_password.'</label>
    <div class="controls">
      <input type="password" name="password" id="password" placeholder="'.$L_password.'">
    </div>
  </div>
  <div class="control-group">
    <div class="controls">
      <label class="checkbox">
        <input type="checkbox"> Remember me
      </label>
      <button type="submit" class="btn btn-primary">'.$L_login.'</button>
    </div>
  </div>
</form>';

// logout section
if(isset($_GET['action']) AND $_GET['action'] == "logout" ){

	$action = $_GET['action'];

	//session_start();

	session_unset();

	session_destroy();

}
// end logout section 


// check if user is already logged in (Thanks to Pavel Urusov for the MD5 password encoding suggestion)

if(isset($_SESSION["user_session"]) AND $_SESSION["user_session"]==$username AND md5($_SESSION["password_session"])==$userpassword){ //if so, keep displaying the page

	$PG_mainbody .= '<div class="alert alert-success">
		'.$L_welcome.' <em>'.$username.'</em> ';

	if (isset($_GET['do']) AND $_GET['do'] != NULL) {

		$PG_mainbody .= '(<a href="?p=admin">'.$L_menu_backadmin.'</a> - <a href="?p=admin&action=logout">'.$L_logout.'</a>)';
	}
	else {

		$PG_mainbody .= '(<a href="?p=admin&action=logout">'.$L_logout.'</a>)';

	}

	$PG_mainbody .= '</div>';

}else{

	if(isset($_POST["user"]) AND $_POST["user"]==$username AND isset($_POST["password"]) AND md5($_POST["password"])==$userpassword){ //if user and pwd are valid

		$PG_mainbody .= '<div class="alert alert-success">
			'.$L_welcome.' <em>'.$username.'</em> (<a href="?p=admin&action=logout">'.$L_logout.'</a>)
			</div>';

		$_SESSION["user_session"] = $_POST["user"];
		$_SESSION["password_session"] = $_POST["password"];

	}else{

		if(isset($_POST["user"]) AND isset($_POST["password"])){ //if user and pwd are not correct

			//display AGAIN login form if usr/pwd not correct

			$PG_mainbody .= '
				
				<div class="alert alert-error">'.$L_notvalid.'</div>
				'.$loginform;


		}else{ 


			//display login form

			$PG_mainbody .= '
				
				<h2>'.$L_login.'</h2>
				'.$loginform;

		}
	}
}
?>