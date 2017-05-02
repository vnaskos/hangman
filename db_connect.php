<?php

require_once('configuration.php');

mysql_connect(
        HGM_CONF_DB_HOST,
        HGM_CONF_DB_USERNAME,
        HGM_CONF_DB_PASSWORD) or die(mysql_error());
mysql_select_db(HGM_CONF_DB_DBNAME) or die(mysql_error());
mysql_query("SET NAMES 'utf8_general_ci'");
mysql_query("SET CHARACTER SET 'utf8_general_ci'");