<?php

// No html errors. They break ajax
	ini_set('html_errors', 0);

// Set default settings here. Can be overridden in config.php.
    $settings = array();
    $settings['sqlColor'] = true;
	$settings['title'] = null;
	$settings['sampleLimit'] = 1;

	require_once('config.php');
	require_once('util.php');
	require_once('libs/Database/Database.php');
    
    $options = array('dsn'                              => $reviewhost['dsn'],
                     PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
                     );
    
	Database::connect(null, $reviewhost['user'], $reviewhost['password'], null, null, 'pdo', $options, 'review');

// Needed for SqlParser
	$dbh = new PDO($reviewhost['dsn'], $reviewhost['user'], $reviewhost['password'], $options);

    require_once('libs/sqlquery/SqlParser.php');
	require_once('classes/QueryRewrite.php');
