<?php

require('config.php');

$dbh = new PDO("mysql:host={$reviewhost['db_host']};dbname={$reviewhost['db_database']}", $reviewhost['db_user'], $reviewhost['db_password']);

$list = $dbh->prepare('SELECT review.checksum                            AS checksum,
                              SUBSTR(review.fingerprint, 1, 99999)       AS sample,
                              
							  review.first_seen                          AS first_seen,
                              review.last_seen                           AS last_seen,
							  IFNULL(review.reviewed_by, "-")      	     AS reviewed_by,
							  review.reviewed_on						 AS reviewed_on,
							  review.comments						     AS comments,
							  
							  SUM(history.ts_cnt)                        AS count,
                              SUM(history.query_time_sum)                AS time,
							  ROUND(SUM(history.query_time_sum)/SUM(history.ts_cnt), 2) AS time_avg
							  
                         FROM '.$reviewhost['review_table'].'            AS review
                    LEFT JOIN '.$reviewhost['review_history_table'].'    AS history
                           ON history.checksum = review.checksum
                     GROUP BY review.checksum
                           ');
$list->execute();

include('list.php');
