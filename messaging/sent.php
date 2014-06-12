<?php
require 'header.php';
if ($userAuthenticated) {
	getMessages();
	
	if ($showinbox) {
		echo '<h2>Sent Messages</h2>';
		$result = mysqli_query($db, "SELECT * FROM `msglist` WHERE `from` = '" . $userid . "' ORDER BY `time` DESC");
		while ($row = mysqli_fetch_assoc($result)) {
			echo '<p class="topline"><br>Time: ' . gmdate('Y-m-d g:i A', $row['time'] - $timeOffsetSeconds) . '<br>To: <a href="https://forums.dropbox.com/profile.php?id=' . htmlspecialchars($row['to']) . '" target="_blank">' . htmlspecialchars($row['to']) . '</a><br>Message:<br>' . nl2br(htmlspecialchars($row['msg'])) . '</p>';
		}
	}
}
require 'footer.php';
?>