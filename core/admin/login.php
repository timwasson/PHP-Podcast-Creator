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

// define login form
$loginform ='
	<form class="form-horizontal" id="login" action="?p=admin" method="post">
		<div class="input-group">
	    	<span class="input-group-addon"><i class="icon-user"></i></span>
			<input type="text" id="user" name="user" placeholder="'.$L_user.'" class="form-control">
		</div>
		<br>
		<div class="input-group">
			<span class="input-group-addon"><i class="icon-lock"></i></span>
			<input type="password" name="password" id="password" placeholder="'.$L_password.'" class="form-control">
		</div>
		<br>
		<div class="form-actions">
			<input type="submit" class="btn btn-primary" value="'.$L_login.'">
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

	$PG_mainbody .= '
<div class="navbar">
	<ul class="nav navbar-nav pull-right">
		<li><a href="#" class="dropdown-toggle" data-toggle="dropdown">'.$username.' <i class="icon-caret-down"></i></a>';

	$PG_mainbody .= '<ul class="dropdown-menu">
						<li><a href="?p=admin"><i class="icon-reply"></i> '.$L_menu_backadmin.'</a></li>
						<li><a href="?p=admin&action=logout"><i class="icon-signout"></i> '.$L_logout.'</a></li></ul>';
	$PG_mainbody .= '
		</li>
	</ul>
</div>';

}else{

	if(isset($_POST["user"]) AND $_POST["user"]==$username AND isset($_POST["password"]) AND md5($_POST["password"])==$userpassword){ //if user and pwd are valid

		$PG_mainbody .= '<div class="alert alert-success">'.$L_welcome.' <em>'.$username.'</em> (<a href="?p=admin&action=logout">'.$L_logout.'</a>)</div>';

		$_SESSION["user_session"] = $_POST["user"];
		$_SESSION["password_session"] = $_POST["password"];

	}else{

		if(isset($_POST["user"]) AND isset($_POST["password"])){ //if user and pwd are not correct
			//display AGAIN login form if usr/pwd not correct
			$PG_mainbody .= '<div class="alert alert-danger">'.$L_notvalid.'</div>'.$loginform;

		}else{ 
			//display login form
			$PG_mainbody .= '<h2>'.$L_login.'</h2>'.$loginform;
		}
	}
}
?>