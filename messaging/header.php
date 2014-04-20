<?php
require 'db-login.php';

function sqlesc($string) {
	global $db;
	return mysqli_real_escape_string($db, $string);
}
function navform() {
	echo '<form action="" method="post" class="menu"><button type="submit" class="btn btn-success" name="action" value="compose">Compose</button></form>';
}
function deleteConfirm(){
	echo '<div class="modal fade in" id="alertDelete">';
	echo '<div class="modal-dialog">';
	echo '<div class="modal-content">';
	echo '<div class="modal-header">';
	echo '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>';
	echo '<h3 class="modal-title">Are you sure?</h3>';
	echo '</div>';
	echo '<div class="modal-body">';
	echo '<h4>If you delete this message, it is gone forever!</h4>';
	echo '</div>';
	echo '<div class="modal-footer">';
	echo '<button class="btn btn-default" data-dismiss="modal">Cancel</button>';
	echo '<form method="post" action="" class="menu"><input name="msgid" type="hidden" id="msgid" value="" /><button type="submit" class="btn btn-danger" name="action" value="delete">Delete</button></form>';
	echo '</div>';
	echo '</div>';
	echo '</div>';
	echo '</div>';
}

//Sets local time display
if (is_numeric($_POST['timeOffset'])) {
	setcookie('timeoffset', htmlspecialchars($_POST['timeOffset']), time() + 3600 * 24 * 30);
	$_COOKIE['timeoffset'] = $_POST['timeOffset'];
}

//Sets DB Forums page to return to
if ($_POST['returnto']) {
	setcookie('returnto', strip_tags($_POST['returnto']));
	$_COOKIE['returnto'] = strip_tags($_POST['returnto']);
}

//Sets cookies to blank on logoff
if ($_POST['action'] == "logoff") {
	setcookie('userToken', "");
	setcookie('userid', "");
	$_COOKIE['userToken'] = "";
	$_COOKIE['userid'] = "";
	$userLogoff = true;
}

//If userToken and userid are set, check login
if ($_COOKIE['userToken'] && $_COOKIE['userid']) {
	$userToken = htmlspecialchars($_COOKIE['userToken']);
	$userid = htmlspecialchars($_COOKIE['userid']);
	$result = mysqli_query($db, "SELECT * FROM `users` WHERE (ext_token = '" . sqlesc($userToken) . "' AND userid = '" . sqlesc($userid) . "') LIMIT 1");
	$row = mysqli_fetch_array($result);

	//If extension is trying to get a token, redirect to login - This is how everything knows the user is authenticated
	if ($row && $_POST['action'] != "create-account")
		$userAuthenticated = true;
	else
		$badAuth = true;
}

//If userToken and userid is posted, check login
if ($_POST['userToken'] && $_POST['userid']) {
	$userToken = htmlspecialchars($_POST['userToken']);
	$userid = htmlspecialchars($_POST['userid']);
	$result = mysqli_query($db, "SELECT * FROM `users` WHERE (ext_token = '" . sqlesc($userToken) . "' AND userid = '" . sqlesc($userid) . "') LIMIT 1");
	$row = mysqli_fetch_array($result);
	setcookie('userToken', $userToken, time() + 3600 * 24 * 30);
	$_COOKIE['userToken'] = $userToken;
	setcookie('userid', $userid, time() + 3600 * 24 * 30);
	$_COOKIE['userid'] = $userid;

	//This is how everything knows the user is authenticated
	if ($row)
		$userAuthenticated = true;
	else
		$badAuth = true;
}
//check login
if ($_POST['username'] && $_POST['password'] && $_POST['action'] != "pass-token") {
	$result = mysqli_query($db, "SELECT password FROM `users` WHERE username = '" . sqlesc($_POST['username']) . "'");
	$passwordHash = mysqli_fetch_row($result);
	$passwordHash = $passwordHash['0'];
	if (password_verify($_POST['password'], $passwordHash))
		$userAuthenticated = true;
	if ($userAuthenticated) {
		$result = mysqli_query($db, "SELECT ext_token FROM `users` WHERE username = '" . sqlesc($_POST['username']) . "'");
		$userToken = mysqli_fetch_row($result);
		$userToken = $userToken['0'];
		setcookie('userToken', $userToken, time() + 3600 * 24 * 30);
		$_COOKIE['userToken'] = $userToken;
		$result = mysqli_query($db, "SELECT userid FROM `users` WHERE username = '" . sqlesc($_POST['username']) . "'");
		$userid = mysqli_fetch_row($result);
		$userid = $userid['0'];
		setcookie('userid', $userid, time() + 3600 * 24 * 30);
		$_COOKIE['userid'] = $userid;
	} else//Authentication unsuccessful
		$badAuth = true;
}
$userid = htmlspecialchars($_COOKIE['userid']);
$userToken = htmlspecialchars($_COOKIE['userToken']);
$timeoffset = htmlspecialchars($_COOKIE['timeoffset']);
$timeOffsetSeconds = $timeoffset * 60;
$returnto = 'https://forums.dropbox.com';
if ($userAuthenticated)
	$showinbox = true;
if (isset($_COOKIE['returnto']))
	$returnto = $_COOKIE['returnto'];
$action = $_POST['action'];
if ($userAuthenticated)
	if ($action == '' || $action == 'showsent' || $action == 'stats' || $action == 'report' || $action == 'register' || $action == 'sign-in' && $userid) {
		$page = $action;
		setcookie('page', $page);
		$_COOKIE['page'] = $page;
	}
$indirectcall = true;
if ($action == 'adminlogin')
	include 'admin-auth.php';

if ($userAuthenticated) {
	//Gather messages in inbox
	$result = mysqli_query($db, "SELECT * FROM `msglist` WHERE `to` = '" . sqlesc($userid) . "' AND `archived` = 0 ORDER BY `time` DESC");
	$archive = mysqli_query($db, "SELECT * FROM `msglist` WHERE `to` = '" . sqlesc($userid) . "' AND `archived` = 1 ORDER BY `time` DESC");

	//Message counter navbar badges
	$count = mysqli_num_rows($result);
	if ($count > 0)
		$countBadge = ' <span class="badge">' . $count . '</span>';
	else
		$countBadge = '';
	$archCount = mysqli_num_rows($archive);
	if ($archCount > 0)
		$archBadge = ' <span class="badge">' . $archCount . '</span>';
	else
		$archBadge = '';
}
?>
<html>
	<head>
		<title>Forum Extender+ Messages</title>
		<link rel='stylesheet' href='css/style.css' />
		<link rel="stylesheet" href="css/bootstrap.css" />
		<link rel="stylesheet" href="css/bootstrap-theme.css" />
	</head>
	<body>
		<div id="wrapper" class="container">
			<div class="jumbotron" id="main">