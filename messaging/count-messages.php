<?php
require_once 'db-login.php';
require_once 'functions.php';

//If userToken and userid is posted, check login
if ($_GET['to'] && $_GET['token']) {
	$userToken = htmlspecialchars($_GET['token']);
	$userid = htmlspecialchars($_GET['to']);
	$result = mysqli_query($db, "SELECT * FROM `users` WHERE (ext_token = '" . sqlesc($userToken) . "' AND userid = '" . sqlesc($userid) . "') LIMIT 1");
	$row = mysqli_fetch_array($result);

	if ($row) {
		//User is authenticated, so we should be good
		$cfg = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM `config` WHERE setting = 'default_uid_origin' LIMIT 1"));

		//Check to make sure our UID origin is from the right place
		if ($row['uid_origin'] == $cfg['val']) {
			//If origin is correct, return message count
			$result = mysqli_query($db, "SELECT * FROM `msglist` WHERE `to` = '" . sqlesc($row['username']) . "' AND `archived` = 0 AND `unread` = 1");
			$msgcount = mysqli_num_rows($result);
			echo $msgcount;
		} else {
			//UID origin is wrong, so return error
			echo 'Bad UID';
		}
	} else {
		//Bad auth, so either the token or the UID is bad
		echo 'Bad auth';
	}
} else {
	//Not enough/incorrect info provided
	echo 'Bad auth';
}
mysqli_close($db);
?>
