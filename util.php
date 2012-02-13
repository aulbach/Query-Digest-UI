<?php

	function ifnull($var, $val) {
		if (is_null($var))
			return $val;
		return $var;
	}

	function linkTable($label, $database, $table) {
		return '<a class="table" onclick="lookupTable(\''.$label.'\', \''.$database.'\', \''.$table.'\');">`'.$table.'`</a>';
	}
