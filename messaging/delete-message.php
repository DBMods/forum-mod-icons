<?php
$result = mysqli_query($db, "DELETE FROM `msglist` WHERE `to` = '" . mysqli_real_escape_string($db, $userid) . "' AND `from` = '" . mysqli_real_escape_string($db, $_POST['from']) . "' AND `time` = '" . mysqli_real_escape_string($db, $_POST['time']) . "' AND `msg` = '" . mysqli_real_escape_string($db, $_POST['msg']) . "'");
echo '<div class="alert-center"><div id="alert-fade" class="alert alert-warning"><p><strong>Message deleted.</strong></p></div></div>';
?>