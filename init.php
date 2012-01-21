<?php

	require('config.php');

	$dbh = new PDO($reviewhost['dsn'], $reviewhost['user'], $reviewhost['password']);

	unset($query);

	foreach ($explainhosts as $label => $host) {
		if (!key_exists('databases', $explainhosts[$label])) {
			$ebh = new PDO($host['dsn'], $host['user'], $host['password']);
			$query = $ebh->prepare('SHOW DATABASES');
			$query->execute();
			while (list($database) = $query->fetch(PDO::FETCH_NUM))
				$explainhosts[$label]['databases'][] = $database;
			$query->closeCursor();
			unset($ebh);
		}
	}

    require('libs/sqlquery/SqlParser.php');
