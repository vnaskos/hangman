<?php
if(!isset($_POST['action'])) {
	header('Location: ./index.php');
}

include("db_connect.php");
include("session.php");

$username = $_POST['username'];
$password = $_POST['password'];

/**
 *	Check if username exists
 */
$query = sprintf("SELECT username FROM player WHERE username = '%s'",
		mysql_real_escape_string($username));
$result = mysql_query($query) or die("Query error: " . mysql_error());

if(mysql_num_rows($result) > 0) {
	die("Username $username already exists!");
}

mysql_free_result($result);

/**
 *	Add user to the database
 */
$query = sprintf("INSERT INTO player VALUES (NULL, '%s', '%s', 0)",
		mysql_real_escape_string($username),
		mysql_real_escape_string($password));
$result = mysql_query($query) or die("Query error: " . mysql_error());

/**
 *	Automatic login for the new user
 */
$uid = mysql_insert_id();
$user = array("id" => $uid,
				"username" => $username,
				"role" => 0);
$_SESSION['user'] = $user;

mysql_close();
header('Location: ./index.php');
?>
