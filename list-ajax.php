<?php

    require('init.php');

    $aColumns = array( 'count',  'time',   'time_avg', 'first_seen', 'last_seen', 'fingerprint', 'reviewed_on', 'reviewed_by', 'comments' );
    $having   = array(    true,    true,         true,        false,       false,         false,         false,         false,      false );

    $query  = 'SELECT SQL_CALC_FOUND_ROWS ';
    $query .= '       review.checksum                                                       AS checksum,';
    $query .= '       review.fingerprint                                                    AS fingerprint,';
    $query .= "       IFNULL(review.reviewed_by, '')                                        AS reviewed_by,";
    $query .= '       DATE(review.reviewed_on)                                              AS reviewed_on,';
    $query .= '       review.comments                                                       AS comments,';
    if (strlen($reviewhost['history_table'])) {
        $query .= '       DATE(MIN(history.ts_min))                                         AS first_seen,';
        $query .= '       DATE(MAX(history.ts_max))                                         AS last_seen,';
        $query .= '       SUM(history.ts_cnt)                                               AS `count`,';
        $query .= '       ROUND(SUM(history.query_time_sum), 2)*1000                        AS `time`,';
        $query .= '       ROUND(SUM(history.query_time_sum)*1000/SUM(history.ts_cnt), 2)    AS time_avg';
        $query .= '  FROM '.Database::escapeField($reviewhost['review_table']).'            AS review';
        $query .= '  JOIN '.Database::escapeField($reviewhost['history_table']).'           AS history';
        $query .= '        ON history.checksum = review.checksum';
    }
    else {
        $query .= '       DATE(review.first_seen)                                           AS first_seen,';
        $query .= '       DATE(review.last_seen)                                            AS last_seen,';
        $query .= '       0                                                                 AS `count`,';
        $query .= '       0                                                                 AS `time`,';
        $query .= '       0                                                                 AS time_avg';
        $query .= '  FROM '.$reviewhost['review_table'].'                                   AS review';
    }

    $sWhere = "";
    if ( @$_GET['sSearch'] != "" ) {
        $sWhere = "WHERE (";
        for ( $i=0 ; $i<count($aColumns) ; $i++ )
            $sWhere .= Database::escapeField($aColumns[$i])." LIKE '%".Database::find('review')->escape_string( $_GET['sSearch'] )."%' OR ";
        $sWhere = substr_replace( $sWhere, "", -3 );
        $sWhere .= ')';
    }

    /* Individual column filtering */
    for ( $i=0 ; $i<count($aColumns) ; $i++ ) {
        if ( @$_GET['bSearchable_'.$i] == "true" && @$_GET['sSearch_'.$i] != '' ) {
            if ( $sWhere == "" )
                $sWhere = "WHERE ";
            else
                $sWhere .= " AND ";

            if ($aColumns[$i] == 'reviewed_by') {
                if ($_GET['sSearch_'.$i] == 'None')
                    $sWhere .= "(".Database::escapeField($aColumns[$i])." IS NULL OR LENGTH(`".$aColumns[$i]."`) = 0 )";
                else
                    $sWhere .= Database::escapeField($aColumns[$i])." = '".($_GET['sSearch_'.$i])."' ";
            }
            else
                $sWhere .= "`".$aColumns[$i]."` LIKE '%".($_GET['sSearch_'.$i])."%' ";
        }
        elseif (!$having[$i] && isset($_GET['sRangeSeparator']) && strpos($_GET['sSearch_'.$i], $_GET['sRangeSeparator']) !== false) {

            list($min, $max) = explode($_GET['sRangeSeparator'], $_GET['sSearch_'.$i]);
            if ($min == 'undefined')
                $min = '';
            if ($max == 'undefined')
                $max = '';
            if (strlen($min) == 0 && strlen($max) == 0)
                continue;

            if ( $sWhere == "" )
                $sWhere = "WHERE ";
            else
                $sWhere .= " AND ";
            $inner = '';

            if (strlen($min))
                $inner .= "`".$aColumns[$i]."` >= '".($min)."' ";

            if (strlen($max)) {
                if (strlen($inner))
                    $inner .= ' AND ';
                $inner .= "`".$aColumns[$i]."` <= '".($max)."' ";
            }

            $sWhere .= " ( {$inner} ) ";
        }
    }

    $query .= " $sWhere ";

    $query .= ' GROUP BY review.checksum';

    $sHaving = '';

    /* Individual column filtering */
    for ( $i=0 ; $i<count($aColumns) ; $i++ ) {
        if ($having[$i] && isset($_GET['sRangeSeparator']) && strpos($_GET['sSearch_'.$i], $_GET['sRangeSeparator']) !== false) {

            list($min, $max) = explode($_GET['sRangeSeparator'], $_GET['sSearch_'.$i]);
            if ($min == 'undefined')
                $min = '';
            if ($max == 'undefined')
                $max = '';
            if (strlen($min) == 0 && strlen($max) == 0)
                continue;

            if ( $sHaving == "" )
                $sHaving = "HAVING ";
            else
                $sHaving .= " AND ";
            $inner = '';

            if (strlen($min))
                $inner .= "`".$aColumns[$i]."` >= '".($min)."' ";

            if (strlen($max)) {
                if (strlen($inner))
                    $inner .= ' AND ';
                $inner .= "`".$aColumns[$i]."` <= '".($max)."' ";
            }

            $sHaving .= " ( {$inner} ) ";
        }
    }

    $query .= " $sHaving ";

    if ( isset( $_GET['iSortCol_0'] ) )
    {
        $sOrder = "ORDER BY  ";
        for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
            if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
                $sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]
                ." ". $_GET['sSortDir_'.$i] .", ";

        $sOrder = substr_replace( $sOrder, "", -2 );
        if ( $sOrder == "ORDER BY" )
            $sOrder = "";
        $query .= " $sOrder ";
    }


    if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
        $query .= " LIMIT ".( $_GET['iDisplayStart'] )
                .", ".( $_GET['iDisplayLength'] );

    $list        = Database::find('review')->query($query);
    $rowCount   = (int)Database::find('review')->query_col('SELECT FOUND_ROWS()');
    $totalCount = (int)Database::find('review')->query_col('SELECT COUNT(review.checksum) FROM '.Database::escapeField($reviewhost['review_table']).' AS review');

    $data = array();
    $data['query']                  = $query;
    $data['sEcho']                  = intval(@$_GET['sEcho']);
    $data['iTotalRecords']          = $totalCount;
    $data['iTotalDisplayRecords']   = $rowCount;
    $data['aaData']                 = array();
    //$data['query']                  = $query;

    while ($row = $list->fetch_assoc()) {
        $row['fingerprint'] = SqlParser::htmlPreparedStatement($row['fingerprint'], true);
        $dr = array();
        foreach ($aColumns as $col)
            $dr[] = $row[$col];
        $dr[] = '<a class="details" href="review.php?checksum='.$row['checksum'].'"><img src="images/details_open.png"></a>';
        $data['aaData'][] = $dr;
    }

    header('Content-type: application/json');
    echo json_encode( $data );
