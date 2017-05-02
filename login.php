<?php
if(!isset($_POST['action'])) {
	header('Location: ./index.php');
}

include("db_connect.php");
include("session.php");

$username = $_POST['username'];
$password = $_POST['password'];

$query = sprintf("SELECT user_id,username,role FROM player WHERE username = '%s' AND password = '%s'",
		mysql_real_escape_string($username),
		mysql_real_escape_string($password));
$result = mysql_query($query) or die("Query error: " . mysql_error());

if(mysql_num_rows($result) <= 0) {
	header('Location: ./users.php');
	return;
}

$row = mysql_fetch_assoc($result);

$user = array("id" => $row['user_id'],
				"username" => $row['username'],
				"role" => $row['role']);

$_SESSION['user'] = $user;

mysql_free_result($result);
mysql_close();
header('Location: ./index.php');