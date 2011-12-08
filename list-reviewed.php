<?php

require('config.php');

$dbh = new PDO("mysql:host={$reviewhost['db_host']};dbname={$reviewhost['db_database']}", $reviewhost['db_user'], $reviewhost['db_password']);

$list = $dbh->prepare('SELECT review.checksum                            AS checksum,
                              review.fingerprint                         AS sample,
                              SUM(history.ts_cnt)                        AS count,
                              SUM(history.query_time_sum)                AS time,
                              review.last_seen                           AS last_seen
                         FROM '.$reviewhost['review_table'].'            AS review
                    LEFT JOIN '.$reviewhost['review_history_table'].'    AS history
                           ON history.checksum = review.checksum
                        WHERE review.reviewed_on IS NOT NULL
                     GROUP BY review.checksum
                            ');
$list->execute();

include('list.php');
