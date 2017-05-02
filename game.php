<?php
include("session.php");

include("db_connect.php");
$game_id = null;

/**
 *	Increase or decrease the difficulty
 *	by the value of $step variable
 */
function adjustDifficulty($step) {
	global $game_id;
	
	$query = sprintf("UPDATE game SET difficulty = difficulty + '%d' WHERE game_id = %d", $step, $game_id);
	$result = mysql_query($query) or die("Query error: " . mysql_error());
}

/**
 *	Remove all given letters except,
 *	the first and last of the hidden word
 */
function resetLetters() {
	global $game_id;
	
	$query = sprintf("SELECT word FROM word INNER JOIN game ON word.word_id = game.word_id WHERE game_id = '%d'", $game_id);
	$result = mysql_query($query) or die("Query error: " . mysql_error());
	$row = mysql_fetch_assoc($result);
	$word = $row['word'];
	mysql_free_result($result);
	
	$letters = array();
	$strlen = mb_strlen( $word, "utf-8" );
	$first_char = mb_substr( $word, 0, 1, "utf-8" );
	$last_char = mb_substr( $word, $strlen-1, 1, "utf-8" );
	$letters[] = $first_char;
	$letters[] = $last_char;
	$letters_str = implode(",", $letters);
	
	$query = sprintf("UPDATE game SET given_letters = '%s' WHERE game_id = %d",
			mysql_real_escape_string($letters_str), $game_id);
	$result = mysql_query($query) or die("Query error: " . mysql_error());
}

/**
 *	Decrease score by 15 and start over the game
 */
function resetGame() {
	global $game_id;
	
	$query = sprintf("UPDATE game SET given_letters = '', score = score -15, fails=0 WHERE game_id = %d", $game_id);
	$result = mysql_query($query) or die("Query error: " . mysql_error());
	
	resetLetters();
}

/**
 *	Fetch and return all rows about the current game
 */
function getGameInfo() {
	global $game_id;
	
	$query = sprintf("SELECT * FROM game INNER JOIN word ON game.word_id = word.word_id WHERE game_id = '%s'", $game_id);
	$result = mysql_query($query) or die("Query error: " . mysql_error());
	
	$row = mysql_fetch_assoc($result);
	
	mysql_free_result($result);
	
	return $row;
}

/**
 *	Return all the needed information to display on the game board
 */
function play() {
	$error = "";

	$info = getGameInfo();
	$word = $info['word'];
	$letters = explode(",", $info['given_letters']);
	
	//Submit a letter
	if(isset($_GET['letter'])) {
		$error = submitLetter($word, $letters);
	}
	
	$info  = getGameInfo();
	$word = $info['word'];
	$letters = explode(",", $info['given_letters']);
	$displayedWord = getDisplayedWord($word, $letters);
	
	return array("word" => $displayedWord[0],
					"success" => $displayedWord[1],
					"letters" => $info['given_letters'],
					"score" => $info['score'],
					"fails" => $info['fails'],
					"difficulty" => $info['difficulty'],
					"error" => $error);
}

/**
 *	Check the validity of the given letter
 *	Check if it's a hit or a miss
 *	and update the database
 */
function submitLetter($word, $letters) {
	global $game_id;
	$letter =  mb_strtoupper($_GET['letter'], "utf-8");
	
	$len = mb_strlen( $letter, "utf-8" );
	
	if($len !=  1) {
		return "One letter per turn!";
	}
	if(in_array($letter, $letters)) {
		return "Letter '" . $letter . "' was already given";
	}

	$fail = 0;
	
	if(mb_strpos($word, $letter, 0, "utf-8") !== false) {
		$score = 5;
	} else {
		$score = -1;
		$fail = 1;
	}
	
	$letters[] = $letter;
	$letters_str = implode(",", $letters);
	$query = sprintf("UPDATE game SET given_letters = '%s', fails = fails + '%d', score = score + '%d' WHERE game_id = '%s'",
				mysql_real_escape_string($letters_str),
				$fail, $score,
				mysql_real_escape_string($game_id));
	$result = mysql_query($query) or die("Query error: " . mysql_error());
	
	return "";
}

/**
 *	Get the word with hidden letters
 *	Example U_____A
 *
 * @param $word String      the hidden word
 * @param $letters array    the letters which the player gave
 * @return array            returns the masked word and a completion flag
 */
function getDisplayedWord($word, $letters) {
	$strlen = mb_strlen( $word, "utf-8" );
	$displayed_word = "";
	$complete = true;
	
	for($i=0; $i<$strlen; $i++) {
		$char = mb_substr( $word, $i, 1, "utf-8" );
		
		if(in_array($char, $letters)) {
			$displayed_word .= sprintf(" %s ", $char);
		} else {
			$displayed_word .= " _ ";
			$complete = false;
		}
	}
	
	return array($displayed_word, $complete);
}
?>

<html>
<head>
	<title>Hangman</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="css/style.css">
</head>
<body>
	<?php
	/**
	 *	Check if an action and a game id are selected
	 */
	if(isset($_GET['action']) && isset($_GET['id'])) {
		$action = $_GET['action'];
		$game_id = $_GET['id'];
		
		/**
		 *	Act accordingly to the selected action
		 *	-reset given letters / new game
		 *	-reset game
		 *	-adjust difficulty
		 */
		if($action == "new") {
			resetLetters();
		} elseif($action == "reset") {
			resetGame();
		} elseif($action == "adjust") {
			adjustDifficulty($_GET['diff']);
		}
		
		$response = play(); ?>
		
		<div id="game-board">
			<h1 class="title"><a href="index.php">Hangman</a></h1>
			<hr />
			<div id="stats">
				<div class="col30">
					<h3>Βαθμολογία</h3>
					<p><?php echo $response['score']; ?></p>
				</div>
				<div class="col30">
					<h3>Λάθη</h3>
					<p><?php echo $response['fails']; ?></p>
				</div>
				<div class="col30">
					<h3>Δυσκολία</h3>
					<p><?php echo $response['difficulty']; ?></p>
				</div>
			</div>
			<div id="word">
				<?php echo $response['word']; ?>
			</div>
			<?php if($response['fails'] < $response['difficulty'] && !$response['success']) { ?>
				<form method="GET" accept-charset="utf-8">
					<input type="hidden" name="action" value="play" />
					<input type="hidden" name="id" value="<?php echo $game_id; ?>" />
					<label for="letter">Letter:</label>
					<input type="text" class="field" id="letter" name="letter" autofocus/>
					<input type="submit" class="btn" />
				</form>
				<div class="clear"></div>
			<?php } ?>
			
			<?php $letters = $response['letters']; ?>
			<label for="letters">Given letters:</label>
			<input type="text" class="field" id="letters" value="<?php echo $letters; ?>" readonly />
			
			<div id="actions">
				<a href="?action=reset&id=<?php echo $game_id; ?>" class="btn btn-action">Reset</a>
				<a href="new_game.php?id=<?php echo $game_id; ?>" class="btn btn-action">Next</a>
				<a href="?action=adjust&id=<?php echo $game_id; ?>&diff=1" class="btn btn-action">Easier</a>
				<a href="?action=adjust&id=<?php echo $game_id; ?>&diff=-1" class="btn btn-action">Harder</a>
				<div class="clear"></div>
			</div>
			
			<?php
			/**
			 *	Informational Messages
			 *	Error, Success, Lose
			 */
			if($response['error']) { ?>
				<div id="error">
					<p><?php echo $response['error']; ?></p>
				</div>
			<?php }
			if($response['success']) { ?>
				<div id="success">
					<p><?php echo "Congratulations" ?></p>
				</div>
			<?php } ?>
			<?php if($response['fails'] >= $response['difficulty']) { ?>
				<div id="error">
					<p><?php echo "You lost"; ?></p>
				</div>
			<?php } ?>
		</div>
	<?php } ?>
</body>
</html>
