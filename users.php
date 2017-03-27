<?php
include("session.php");

/**
 *	Logged in users can't access this page
 */
if($authenticated) {
	header('Location: ./index.php');
}
?>

<html>
<head>
	<title>Hangman</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="css/style.css">
</head>
<body>
	<div id="game-board">
		<h1>Hangman</h1>
		<hr />
		<div class="col50">
			<h2>Login</h2>
			<form action="login.php" method="POST">
				<label for="username">Username:</label>
				<input type="text" class="field" name="username" />
				<label for="password">Password:</label>
				<input type="password" class="field" name="password" />
				<input type="submit" class="btn" name="action" value="Login" />
				<div class="clear"></div>
			</form>
		</div>
		<div class="col50">
			<h2>Register</h2>
			<form action="register.php" method="POST">
				<label for="username">Username:</label>
				<input type="text" class="field" name="username" />
				<label for="password">Password:</label>
				<input type="password" class="field" name="password" />
				<input type="submit" class="btn" name="action" value="Register" />
				<div class="clear"></div>
			</form>
		</div>
	</div>
</div>
