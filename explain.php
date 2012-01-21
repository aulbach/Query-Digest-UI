<?php

	require_once('init.php');

	$return = array();

	$query = $dbh->prepare('SELECT review.sample
                              FROM '.$reviewhost['review_table'].' AS review
                             WHERE review.checksum = ?
                          GROUP BY review.checksum
                        ');
    $query->execute(array($_REQUEST['checksum']));
    $reviewData = $query->fetch(PDO::FETCH_ASSOC);

	$sample = 'EXPLAIN EXTENDED '.$reviewData['sample'];

	list($label, $database) = explode('.', $_REQUEST['explainDb']);
	$host = $explainhosts[$label];
	$ebh = new PDO($host['dsn'], $host['user'], $host['password'], array( PDO::ATTR_PERSISTENT => true ));

	$query = $ebh->prepare("USE $database");
	$query->execute();

	$query = $ebh->prepare($sample);
	$query->execute();
	while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
		$row['possible_keys'] = str_replace(',', ', ', $row['possible_keys']);
		$return['Explain'][] = $row;
	}
	$query->closeCursor();
	$query = $ebh->prepare('SHOW WARNINGS');
	$query->execute();
	while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
		if ($row['Code'] == 1003)
			$return['Query'] = str_replace(',', ', ', $row['Message']);
		else
			$return['Warnings'][] = $row;
	}
	$query->closeCursor();

    if (array_key_exists('Query', $return)) {
        $return['Query'] = preg_replace("/`([-_a-zA-Z0-9]+)`\.`([-_a-zA-Z0-9]+)`\.`([-_a-zA-Z0-9]+)`/U",
                                        " <a class=\"database\" onclick=\"lookupDatabase ('$label', '\${1}')\">`\${1}`</a>"
                                        .".<a class=\"table\"    onclick=\"lookupTable   ('$label', '\${1}', '\${2}')\">`\${2}`</a>"
                                        .".<a class=\"column\"   onclick=\"lookupCol     ('$label', '\${1}', '\${2}', '\${3}')\">`\${3}`</a>",
                                        $return['Query']
                                       );

        $return['Query'] = preg_replace("/`([-_a-zA-Z0-9]+)`\.`([-_a-zA-Z0-9]+)`/U",
                                        " <a class=\"database\" onclick=\"lookupDatabase ('$label', '\${1}')\">`\${1}`</a>"
                                        .".<a class=\"table\"    onclick=\"lookupTable   ('$label', '\${1}', '\${2}')\">`\${2}`</a>",
                                        $return['Query']
                                       );
    }

	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Content-type: application/json');
	echo json_encode($return);
