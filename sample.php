<?php

	require_once('init.php');
	
	$return = array(
		'sample' 	=> '',
		'primary'	=> array(),
		'offset'	=> 0,
					);

	if (strlen($reviewhost['history_table'])) {
		$primaryKey = '';
		if (count($reviewhost['history_table_primary']) > 0) {
			foreach ($reviewhost['history_table_primary'] AS $field)
				$primaryKey .= ", $field ";
		}
		
		$offset = 0;
		if (isset($_REQUEST['offset']) && $_REQUEST['offset'] > 0)
			$offset = intval($_REQUEST['offset']);
		
		$res = Database::find('review')->query('SELECT review.sample '.$primaryKey.'
								  FROM '.Database::escapeField($reviewhost['history_table']).' AS review
								 WHERE review.checksum = ?
							  ORDER BY review.ts_max DESC
							     LIMIT 1
								OFFSET '.$offset,
							  $_REQUEST['checksum']
							  );
		$row = $res->fetch_assoc();
		$return['sample'] = $row['sample'];
		foreach ($reviewhost['history_table_primary'] AS $field)
			$return['primary'][$field] = $row[$field];
		$return['offset'] = $offset;
	}
	else {
		$return['sample'] = Database::find('review')->query_col('SELECT review.sample
									FROM '.Database::escapeField($reviewhost['review_table']).' AS review
								   WHERE review.checksum = ?',
								$_REQUEST['checksum']
								);
	}
	
	if (strlen($return['sample']))
		$return['sample'] = SqlParser::html($return['sample']);

	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Content-type: application/json');
	echo json_encode($return);
