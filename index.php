<?php include("session.php"); ?>

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
		<?php
		/**
		 *	Display the main menu
		 *	if the user is logged, in add 'logout' option
		 *	if the user is an administrator, add 'words' option
		 */
		if($authenticated) { ?>
			<a class="btn wide-btn" href="logout.php">Logout <?php echo $user['username']; ?></a>
			<?php if($user['role'] > 0) { ?>
				<a class="btn wide-btn" href="words.php">Λέξεις</a>
			<?php } ?>
		<?php } else { ?>
			<a class="btn wide-btn" href="users.php">Login / Register</a>
		<?php } ?>
		<a class="btn wide-btn" href="newGame.php">Νέο παιχνίδι</a>
		<a class="btn wide-btn" href="scoreboard.php">Score Board</a>
	</div>
</body>
</html>
