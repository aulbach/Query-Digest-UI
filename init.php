<?php

	require('config.php');

	$dbh = new PDO($reviewhost['dsn'], $reviewhost['user'], $reviewhost['password']);
