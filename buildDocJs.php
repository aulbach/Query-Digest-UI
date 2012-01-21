#!/usr/bin/env php
<?php

	require ('init.php');
	$cnt = 0;
	
	try {
		$query = $dbh->prepare('SELECT COUNT(0) FROM mysql.help_topic');
		$query->execute();
		list($cnt) = $query->fetch(PDO::FETCH_NUM);
		unset($query);
	}
	catch (exception $e) {}
	
	if ($cnt < 2) {
		echo "table mysql.help_topic doesn't exist or appears to be empty. Failing";
		exit(1);
	}

	echo "Building js/docData.js...\n";
	
//SELECT CONCAT("'", LOWER(name), "' : '", url, "',") AS k FROM mysql.help_topic WHERE name NOT LIKE '% %' AND LENGTH(url) > 0;
	
	$fp = fopen('js/docData.js', 'w+');
	fputs($fp, "var help_topic = {\n");
	
	$query = $dbh->prepare("SELECT LOWER(name), url FROM mysql.help_topic WHERE name NOT LIKE '% %' AND LENGTH(url) > 0");
	$query->execute();
	while ($row = $query->fetch(PDO::FETCH_NUM)) {
		list($name, $url) = $row;
		fputs($fp, "    '{$name}' : '{$url}', \n");
	}
// Fix for IE
	fputs($fp, "    '' : '' \n");
	fputs($fp, "}\n");
	
	fclose($fp);
	echo "\n";
