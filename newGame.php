<?php
include("session.php");

/**
 *	Check if user is logged in
 *	else play annonymously
 */
if($authenticated) {
	$uid = $user['id'];
} else {
	$uid = 1;
}

include("dbconnect.php");

/**
 *	Create a new game
 *	Select a word for the game by the given id
 */
function newGame($word_id) {
	global $uid;
	
	$query = sprintf("INSERT INTO game VALUES (NULL, '%d', '%d', '', 0, 3, 0)", $uid, $word_id);
	$result = mysql_query($query) or die("Query error: " . mysql_error());
	
	$last_id = mysql_insert_id();
	mysql_close();
	
	return $last_id;
}

/**
 *	Select the next word, according to the given id,
 *	and create a new game
 */
function nextGame($game_id) {
	$query = sprintf("SELECT word_id FROM word WHERE word_id = (SELECT MIN(word_id) FROM word WHERE word_id > (SELECT word_id FROM game WHERE game_id = %d))", $game_id);
	$result = mysql_query($query) or die("Query error: " . mysql_error());
	$row = mysql_fetch_assoc($result);
	$word_id = $row['word_id'];
	mysql_free_result($result);
	
	return $word_id;
}

/**
 *	Find the first valid id from the words table
 */
function getFirstID() {
	$query = "SELECT MIN(word_id) AS min_id FROM word";
	$result = mysql_query($query) or die("Query error: " . mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row['min_id'];
}

/**
 *	Determine what to do, depending on the selected action
 *	-Create new game
 *	-Move to the next word
 */
if(isset($_GET['id']) && $_GET['id']) {
	$word_id = nextGame($_GET['id']);
	if(!$word_id) {
		$word_id = getFirstID();
	}
	$game_id = newGame($word_id);
} else {
	$game_id = newGame(getFirstID());
}
?>

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="refresh" content="1;url=game.php?action=new&id=<?php echo $game_id; ?>">
	<link rel="stylesheet" href="css/style.css">
</head>
<body>
	<div id="game-board">
		<h1 class="title"><a href="index.php">Hangman</a></h1>
		<hr />
		<h2>το παιχνίδι ξεκινάει ...</h2>
	</div>
</body>
</html>
