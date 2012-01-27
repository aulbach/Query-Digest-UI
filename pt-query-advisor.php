<?php

    require('init.php');

	$fingerprint = Database::find('review')->query_col('SELECT review.fingerprint
                              FROM '.Database::escapeField($reviewhost['review_table']).' AS review
                             WHERE review.checksum = ?
                          GROUP BY review.checksum',
						  $_REQUEST['checksum']
						  );

	$checkPaths = array(
		"/opt/local/bin",
		"/opt/local/sbin",
		"/opt/local/libexec/gnubin/",
		"/usr/bin",
		"/bin",
		"/usr/sbin",
		"/sbin",
		"/usr/local/bin",
		"/usr/X11/bin",
		"/usr/local/MacGPG2/bin",
		"/usr/X11R6/bin",
		"/usr/libexec"
	);

	$advisorRules = array(
		'ALI.001' => 'Note: Aliasing without the AS keyword. Explicitly using the AS keyword in column or table aliases, such as "tbl AS alias," is more readable than implicit aliases such as "tbl alias".',
		'ALI.002' => 'Warn: Aliasing the \'*\' wildcard. Aliasing a column wildcard, such as "SELECT tbl.* col1, col2" probably indicates a bug in your SQL. You probably meant for the query to retrieve col1, but instead it renames the last column in the *-wildcarded list.',
		'ALI.003' => 'Note: Aliasing without renaming. The table or column\'s alias is the same as its real name, and the alias just makes the query harder to read.',
		'ARG.001' => 'Warn: Argument with leading wildcard. An argument has a leading wildcard character, such as "%foo". The predicate with this argument is not sargable and cannot use an index if one exists.',
		'ARG.002' => 'Note: LIKE without a wildcard. A LIKE pattern that does not include a wildcard is potentially a bug in the SQL.',
		'CLA.001' => 'Warn: SELECT without WHERE. The SELECT statement has no WHERE clause.',
		'CLA.002' => 'Note: ORDER BY RAND(). ORDER BY RAND() is a very inefficient way to retrieve a random row from the results.',
		'CLA.003' => 'Note: LIMIT with OFFSET. Paginating a result set with LIMIT and OFFSET is O(n^2) complexity, and will cause performance problems as the data grows larger.',
		'CLA.004' => 'Note: Ordinal in the GROUP BY clause. Using a number in the GROUP BY clause, instead of an expression or column name, can cause problems if the query is changed.',
		'CLA.005' => 'Warn: ORDER BY constant column.',
		'CLA.006' => 'Warn: GROUP BY or ORDER BY different tables will force a temp table and filesort.',
		'CLA.007' => 'Warn: ORDER BY different directions prevents index from being used. All tables in the ORDER BY clause must be either ASC or DESC, else MySQL cannot use an index.',
		'COL.001' => 'Note: SELECT *. Selecting all columns with the * wildcard will cause the query\'s meaning and behavior to change if the table\'s schema changes, and might cause the query to retrieve too much data.',
		'COL.002' => 'Note: Blind INSERT. The INSERT or REPLACE query doesn\'t specify the columns explicitly, so the query\'s behavior will change if the table\'s schema changes; use "INSERT INTO tbl(col1, col2) VALUES..." instead.',
		'LIT.001' => 'Warn: Storing an IP address as characters. The string literal looks like an IP address, but is not an argument to INET_ATON(), indicating that the data is stored as characters instead of as integers. It is more efficient to store IP addresses as integers.',
		'LIT.002' => 'Warn: Unquoted date/time literal. A query such as "WHERE col<2010-02-12" is valid SQL but is probably a bug; the literal should be quoted.',
		'KWR.001' => 'Note: SQL_CALC_FOUND_ROWS is inefficient. SQL_CALC_FOUND_ROWS can cause performance problems because it does not scale well; use alternative strategies to build functionality such as paginated result screens.',
		'JOI.001' => 'Crit: Mixing comma and ANSI joins. Mixing comma joins and ANSI joins is confusing to humans, and the behavior differs between some MySQL versions.',
		'JOI.002' => 'Crit: A table is joined twice. The same table appears at least twice in the FROM clause.',
		'JOI.003' => 'Warn: Reference to outer table column in WHERE clause prevents OUTER JOIN, implicitly converts to INNER JOIN.',
		'JOI.004' => 'Warn: Exclusion join uses wrong column in WHERE. The exclusion join (LEFT OUTER JOIN with a WHERE clause that is satisfied only if there is no row in the right-hand table) seems to use the wrong column in the WHERE clause. A query such as "... FROM l LEFT OUTER JOIN r ON l.l=r.r WHERE r.z IS NULL" probably ought to list r.r in the WHERE IS NULL clause.',
		'RES.001' => 'Warn: Non-deterministic GROUP BY. The SQL retrieves columns that are neither in an aggregate function nor the GROUP BY expression, so these values will be non-deterministic in the result.',
		'RES.002' => 'Warn: LIMIT without ORDER BY. LIMIT without ORDER BY causes non-deterministic results, depending on the query execution plan.',
		'STA.001' => 'Note: != is non-standard. Use the <> operator to test for inequality.',
		'SUB.001' => 'Crit: IN() and NOT IN() subqueries are poorly optimized. MySQL executes the subquery as a dependent subquery for each row in the outer query. This is a frequent cause of serious performance problems. This might change version 6.0 of MySQL, but for versions 5.1 and older, the query should be rewritten as a JOIN or a LEFT OUTER JOIN, respectively.',
	);

	$binary = false;
	foreach ($checkPaths as $path) {
		if (is_executable("$path/pt-query-advisor")) {
			$binary = "$path/pt-query-advisor";
			break;
		}
	}

	if (!$binary) {
		echo "<p>I can't find the pt-query-advisor binary.</p>";
		exit;
	}

	$command = escapeshellcmd($binary);
	$command .= ' --query '.escapeshellarg($fingerprint);
	$output = explode("\n", shell_exec($command));

	$rules = array();

	foreach ($output as $line) {
		if (substr($line, 0, 1) == '#' || strlen($line) == 0)
			continue;
		list($rule, $fingerprint) = explode(" ", $line);
		$rules[] = $rule;
	}

	if (count($rules) == 0)
		echo "<p>All good! Nothing found!</p>\n";
	else {
		foreach ($rules as $rule)
			echo "<p>{$advisorRules[$rule]}</p>\n";
	}
