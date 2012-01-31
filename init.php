<?php

// Set default settings here. Can be overridden in config.php.
    $settings = array();
    $settings['sqlColor'] = true;
	$settings['title'] = null;

	require_once('config.php');
	require_once('libs/Database/Database.php');
	Database::connect(null, $reviewhost['user'], $reviewhost['password'], null, null, 'pdo', array('dsn' => $reviewhost['dsn']), 'review');

// Needed for SqlParser
	$dbh = new PDO($reviewhost['dsn'], $reviewhost['user'], $reviewhost['password']);

    require_once('libs/sqlquery/SqlParser.php');
	require_once('classes/QueryRewrite.php');
