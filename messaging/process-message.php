<?php
require 'db-login.php';
function sqlesc($string) {
	global $db;
	return mysqli_real_escape_string($db, $string);
}
$dest = $_POST['msgto'];
if (is_numeric($dest) && $dest != 0) {
	mysqli_query($db, 'INSERT INTO msglist (`to`, `from`, `msg`, `time`) VALUES("' . sqlesc($dest) . '", "' . sqlesc($_POST['msgfrom']) . '", "' . sqlesc($_POST['msgtext']) . '", "' . time() . '")');
	mysqli_close($db);
}
header('Location: ' . $_POST['returnto']);
?>
