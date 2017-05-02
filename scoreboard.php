<?php include("session.php"); ?>

<html>
<head>
	<title>Hangman</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="css/style.css">
</head>
<body>
	<div id="game-board">
		<h1 class="title"><a href="index.php">Hangman</a></h1>
		<hr />
		<div class="game-row">
			<div class="col20">WORD ID</div>
			<div class="col20">GAME ID</div>
			<div class="col20">USERNAME</div>
			<div class="col20">SCORE</div>
		</div>
		<hr />
		<?php
		include("db_connect.php");
		
		$per_page = 10; //Number of rows displayed per page
		
		/**
		 *	Determine which page is selected
		 *	Default: page 1
		 */
		if(isset($_GET['page'])) {
			$page = $_GET['page'];
			$offset = $per_page * ($page-1);
		} else {
			$page = 1;
			$offset = 0;
		}
		
		/**
		 *	Fetch all games of the selected page
		 */
		$query = sprintf("SELECT * FROM game INNER JOIN player ON game.user_id = player.user_id ORDER BY score DESC LIMIT %d OFFSET %d", $per_page, $offset);
		$result = mysql_query($query) or die("Query error: " . mysql_error());
		
		/**
		 *	Display the results and a delete button
		 *	if the current user is an administrator
		 */
		while($row = mysql_fetch_assoc($result)) { ?>
			<div class="game-row">
				<div class="col20"><?php echo $row['word_id']; ?></div>
				<div class="col20"><?php echo $row['game_id']; ?></div>
				<div class="col20"><?php echo $row['username']; ?></div>
				<div class="col20"><?php echo $row['score']; ?></div>
				<?php
				if($authenticated && $user['role'] > 0) { ?>
					<div class="col10"><a href="delete_game.php?id=<?php echo $row['game_id']; ?>">ΔΙΑΓΡΑΦΗ</a></div>
				<?php }
				?>
			</div>
		<?php }
		mysql_free_result($result); ?>
		
		<div class="pages"><?php
			/**
			 *	PAGINATION
			 */
			$query = "SELECT * FROM game";
			$result = mysql_query($query) or die("Query error: " . mysql_error());
			$len = mysql_num_rows($result);
			
			// previous page buton
			$prev = $page-1;
			if($prev > 0)
				echo "<a href=\"?page=$prev\"><</a>";
			
			// page butons
			for($i=1,$j=1; $i<$len; $i+=$per_page,$j++) {
				if($page == $j)
					echo "<a class=\"current\" href=\"?page=$j\">$j</a>";
				else
					echo "<a href=\"?page=$j\">$j</a>";
			}
			
			// next page buton
			$next = $page+1;
			if($next <= ceil($len/$per_page))
				echo "<a href=\"?page=$next\">></a>";
			
			mysql_free_result($result);
			mysql_close(); ?>
		</div>
	</div>
</body>
</html>
