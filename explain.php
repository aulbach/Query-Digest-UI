<?php

	require_once('init.php');

	$return = array();
	if (strlen($reviewhost['history_table'])) {
		$query = Database::find('review')->query_col('SELECT review.sample
								  FROM '.Database::escapeField($reviewhost['history_table']).' AS review
								 WHERE review.checksum = ?
							  ORDER BY review.ts_max DESC
							     LIMIT 1',
							  $_REQUEST['checksum']
							  );
	}
	else {
		$query = Database::find('review')->query_col('SELECT review.sample
								  FROM '.Database::escapeField($reviewhost['review_table']).' AS review
								 WHERE review.checksum = ?',
							  $_REQUEST['checksum']
							  );
	}

	$Query = new QueryRewrite();
    $Query->setQuery($query);
	$sample = $Query->asExtendedExplain();
	
    $return['QueryRewrite'] = (array) $Query;
	$return['oQuery'] = $query;
	$return['eQuery'] = $sample;
	
	if (is_null($sample)) {
		$return['Warnings'][] = array('Code' => '0', 'Level' => 'Error', 'Message' => "I can't explain this type of query yet");
	}
	else {
		list($label, $database) = explode('.', $_REQUEST['explainDb']);
		$host = $explainhosts[$label];
		Database::connect(null, $host['user'], $host['password'], null, null, 'pdo', array('dsn' => $host['dsn']), $label);
		Database::find($label)->query('USE '.Database::escapeField($database));
	
		Database::find($label)->disable_fatal_errors();
		$query = Database::find($label)->query($sample);
		Database::find($label)->enable_fatal_errors();
        
        if (is_null($query)) {
            $return['Warnings'][] = array('Code' => Database::find($label)->_errno(), 'Level' => 'Error', 'Message' => Database::find($label)->_errstr());
        }
        else {
            while ($row = $query->fetch_assoc()) {
                $row['possible_keys'] = str_replace(',', ', ', $row['possible_keys']);
                $row['ref'] = str_replace(',', ', ', $row['ref']);
                $row['Extra'] = str_replace(array('Using ', ';'), array('', ', '), $row['Extra']);
                foreach ($row as $key => $val) {
                    if (is_null($row[$key]))
                        $row[$key] = '';
                    $row[$key] = htmlentities($row[$key]);
                }
                        
                $return['Explain'][] = $row;
            }
        }
		$query = Database::find($label)->query('SHOW WARNINGS');
		while ($row = $query->fetch_assoc()) {
			if ($row['Code'] == 1003)
				$return['Query'] = str_replace(',', ', ', $row['Message']);
			else
				$return['Warnings'][] = $row;
		}
	
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
	}

	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Content-type: application/json');
	echo json_encode($return);
