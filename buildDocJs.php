#!/usr/bin/env php
<?php

	require ('init.php');
	$cnt = 0;
	
	try {
		$cnt = Database::find('review')->query_col('SELECT COUNT(0) FROM mysql.help_topic');
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
	$res = Database::find('review')->query("SELECT LOWER(name), url FROM mysql.help_topic WHERE name NOT LIKE '% %' AND LENGTH(url) > 0");
	while ($row = $res->fetch_row()) {
		list($name, $url) = $row;
		fputs($fp, "    '{$name}' : '{$url}', \n");
	}
// Fix for IE
	fputs($fp, "    '' : '' \n");
	fputs($fp, "}\n");
	
	fclose($fp);
	echo "\n";
