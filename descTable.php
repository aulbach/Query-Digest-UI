<?php

	require_once('init.php');

	$return = array();
	
	$label    			= $_REQUEST['label'];
	$database 			= $_REQUEST['db'];
	$table    			= $_REQUEST['table'];
	
	$return['label']    = $label;
	$return['database'] = $database;
	$return['table']    = $table;
	$return['column']   = $col;
	
	$return['title']    = "{$database}.${table}";

	$host = $explainhosts[$label];
	Database::connect(null, $host['user'], $host['password'], null, null, 'pdo', array('dsn' => $host['dsn']), $label);
	Database::find($label)->query('USE '.Database::escapeField($database));
	
	list($tmp, $return['create']) = Database::find($label)->query_row('SHOW CREATE TABLE '.Database::escapeField($table));
	
	$return['create'] = SqlParser::html($return['create']);

	
	$desc = Database::find($label)->query_list_assoc('DESC '.Database::escapeField($table));
	
	$return['desc'] = '<table><thead><tr>';
	foreach (array_keys($desc[0]) as $key)
		$return['desc'] .= '<th>'.$key.'</th>';
	$return['desc'] .= '</tr></thead><tbody>';
	foreach ($desc as $row) {
		$return['desc'] .= '<tr>';
		foreach ($row as $key => $val)
			$return['desc'] .= '<td>'.ifnull($val, 'NULL').'</td>';
		$return['desc'] .=  '</tr>';
	}
	$return['desc'] .= '</tbody></table>';
	unset($desc);
	
	$indexes = Database::find($label)->query_list_assoc('SHOW INDEXES FROM '.Database::escapeField($table));
	
	$return['indexes'] = '<table><thead><tr>';
	foreach (array_keys($indexes[0]) as $key)
		$return['indexes'] .= '<th>'.$key.'</th>';
	$return['indexes'] .= '</tr></thead><tbody>';
	foreach ($indexes as $row) {
		$return['indexes'] .= '<tr>';
		foreach ($row as $key => $val)
			$return['indexes'] .= '<td>'.ifnull($val, 'NULL').'</td>';
		$return['indexes'] .=  '</tr>';
	}
	$return['indexes'] .= '</tbody></table>';
	unset($indexes);
		
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Content-type: application/json');
	echo json_encode($return);
