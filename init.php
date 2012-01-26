<?php

// Set default settings here. Can be overridden in config.php.
    $settings = array();
    $settings['sqlColor'] = true;

	require_once('config.php');

	$dbh = new PDO($reviewhost['dsn'], $reviewhost['user'], $reviewhost['password']);

    require_once('libs/sqlquery/SqlParser.php');
	require_once('classes/QueryRewrite.php');
