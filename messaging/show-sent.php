<?php
echo '<p><form action="messages.php" method="post"><button type="submit">Back to inbox</button></form></p>';
$result = mysql_query("SELECT * FROM `msglist` WHERE `from` = '" . $userid . "' ORDER BY `time` DESC");
while ($row = mysql_fetch_assoc($result)) {
	echo '<p class="topline">';
	echo '<br>Time: ' . gmdate('Y-m-d g:i A', $row['time'] - $timeOffsetSeconds) . '<br>To: <a href="https://forums.dropbox.com/profile.php?id=' . htmlspecialchars($row['to']) . '" target="_blank">' . htmlspecialchars($row['to']) . '</a><br>Message:<br>' . nl2br(htmlspecialchars($row['msg']));
	echo '</p>';
}
?>
