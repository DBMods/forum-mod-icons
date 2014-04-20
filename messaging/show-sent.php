<?php
require 'header.php';
if ($userAuthenticated) {
	if ($action == 'addressbook')
		include 'address-book.php';
	elseif ($action == 'compose' || $action == 'send')
		include 'compose-message.php';
	if ($showinbox) {echo '<h2>Sent Messages</h2>';
		$page = 'showsent';
		navform();
		$result = mysqli_query($db, "SELECT * FROM `msglist` WHERE `from` = '" . $userid . "' ORDER BY `time` DESC");
		while ($row = mysqli_fetch_assoc($result)) {
			echo '<p class="topline"><br>Time: ' . gmdate('Y-m-d g:i A', $row['time'] - $timeOffsetSeconds) . '<br>To: <a href="https://forums.dropbox.com/profile.php?id=' . htmlspecialchars($row['to']) . '" target="_blank">' . htmlspecialchars($row['to']) . '</a><br>Message:<br>' . nl2br(htmlspecialchars($row['msg'])) . '</p>';
		}
	}
}
require 'footer.php';
?>