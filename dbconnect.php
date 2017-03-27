<?php
mysql_connect("localhost", "root", "") or die(mysql_error()); 
mysql_select_db("hangtest") or die(mysql_error());
mysql_query("SET NAMES 'utf8_general_ci'");
mysql_query("SET CHARACTER SET 'utf8_general_ci'");
?>
