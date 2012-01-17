<?php

    require('init.php');

    if (@$_REQUEST['Review'] == 'Review' ) {
        $query = $dbh->prepare('UPDATE review SET reviewed_by = ?, reviewed_on = NOW(), comments = ? WHERE checksum = ?');
        $query->execute(array($_REQUEST['reviewed_by'], $_REQUEST['comments'], $_REQUEST['checksum']));
        header( "Location: review.php?checksum={$_REQUEST['checksum']}" ) ;
        exit;
    }

    $query = $dbh->prepare('SELECT review.*
                              FROM '.$reviewhost['review_table'].' AS review
                             WHERE review.checksum = ?
                          GROUP BY review.checksum
                        ');
    $query->execute(array($_REQUEST['checksum']));
    $reviewData = $query->fetch(PDO::FETCH_ASSOC);

    foreach ($reviewData as $key=>&$val) {
        if (in_array($key, array('checksum')))
            continue;
        if (is_numeric($val)) {
            if (stripos($key, 'time') !== false) {
                $val *= 1000;
                $val = round($val, 0);
            }
            else
                $val = round($val, 2);
            $val = number_format($val);
        }
    }
    unset ($key, $val);

    $query = $dbh->prepare('SELECT review.*
                              FROM '.$reviewhost['history_table'].' AS review
                             WHERE review.checksum = ?
                          ORDER BY review.ts_max DESC
                              ');
    $query->execute(array($_REQUEST['checksum']));
    $historyData = $query->fetch(PDO::FETCH_ASSOC);

    while ($newData = $query->fetch(PDO::FETCH_ASSOC)) {
        foreach ($newData as $key=>$value) {
            if (!$value)
                continue;
            if ( is_null($historyData[$key])) {
                $historyData[$key] = $value;
                continue;
            }
            if (stripos($key, '_sum') !== false)
                $historyData[$key] += $value;
            else if (stripos($key, '_cnt') !== false)
                $historyData[$key] += $value;
            else if (stripos($key, '_min') !== false)
                $historyData[$key] = min($value, $historyData[$key]);
            else if (stripos($key, '_max') !== false)
                $historyData[$key] = max($value, $historyData[$key]);
            else if (   stripos($key, '_pct_95') !== false
                     || stripos($key, '_stddev') !== false
                     || stripos($key, '_median') !== false
                     || stripos($key, '_rank') !== false
                     ){
                $historyData[$key] = (($value * $newData['ts_cnt'])
                                         + ($historyData[$key] * $historyData['ts_cnt']))
                                         / ($newData['ts_cnt']+$historyData['ts_cnt']);
            }
        }
    }
    unset($newData);

    foreach ($historyData as $key=>&$val) {
        if (in_array($key, array('checksum')))
            continue;
        if (is_numeric($val)) {
            if (stripos($key, 'time') !== false) {
                $val *= 1000;
                $val = round($val, 0);
            }
            else
                $val = round($val, 2);
            $val = number_format($val);
        }
    }
    unset ($key, $val);

?>

<?php include('templates/header.php'); ?>

<div class="tabs">
    <ul>
        <li><a href="#queryFingerprint">Fingerprint</a></li>
        <li><a href="#querySample">Example</a></li>
        <li><a href="#normalizedQuery">Normalized</a></li>
        <li><a href="pt-query-advisor.php?checksum=<?php echo $_REQUEST['checksum']; ?>">Advisor</a></li>
        <li><a href="#queryReview">Review</a></li>
    </ul>
    <div id="queryFingerprint"><?php echo str_replace(',', ', ', $reviewData['fingerprint']); ?></div>
    <div id="querySample">
        <?php echo str_replace(',', ', ', $reviewData['sample']); ?>
    </div>
    <div id="normalizedQuery">Please explain the query to view the normalized query.</div>
    <div id="queryReview">
        <form method="post">
            <label for="reviewed_by">Reviewed by </label> <input type="text" name="reviewed_by" value="<?php echo $reviewData['reviewed_by']; ?>">
            <label for="comments">Comments</label> <textarea name="comments" rows="1" style="vertical-align: bottom;"><?php echo $reviewData['comments']; ?></textarea>
            <input type="hidden" id="checksum" name="checksum" value="<?php echo $_REQUEST['checksum']; ?>">
            <input type="submit" name="Review" value="Review">
        </form>
    </div>
</div>

<div class="accordion" id="explainAccordion">
    <h3><a href="#">Explain</a></h3>
    <div>

        <div>
            <select id="explainDb" class="explainDb">
                <?php
                    foreach ($explainhosts AS $label => $host) {
                        ?>
                        <optgroup label="<?php echo $label; ?>">
                            <?php
                                foreach ($host['databases'] AS $database) {
                                    ?>
                                        <option value="<?php echo "$label.$database"; ?>"> <?php echo "$label.$database"; ?></option>
                                    <?php
                                }
                                ?>
                        </optgroup>
                        <?php
                    }
                ?>
            </select>
            <input type="submit" value="Explain" id="doExplain">
        </div>

        <table class="dataTable" id="explainPlan">
            <thead>
                <tr>
                    <th>id</th>
                    <th>select_type</th>
                    <th>table</th>
                    <th>type</th>
                    <th>possible_keys</th>
                    <th>key</th>
                    <th>key_len</th>
                    <th>ref</th>
                    <th>rows</th>
                    <th>Extra</th>
                </tr>
            </thead>
            <tbody></tbody>
            <tfoot></tfoot>
        </table>

    </div>
</div>

<div class="accordionOpen">
    <h3><a href="#">Detailed Stats</a></h3>
    <div>
        Seen between <?php echo $reviewData['first_seen']; ?> and <?php echo $reviewData['last_seen']; ?>.
        <p>
            <?php
                $timeDiff = strtotime($reviewData['last_seen']) - strtotime($reviewData['first_seen']);
                if ($timeDiff > 0 ) {
                    $qps = $historyData['ts_cnt'] / $timeDiff;
                    $qpm = $qps * 60;
                    $qph = $qpm * 60;
                    $qpd = $qph * 24;
                    $qpw = $qpd * 7;
                    $qpM = $qpw * 30;
                    $qpq = $qpM * 3;
                    $qpy = $qpd * 265;

                        if ($qps > 2) echo round($qps, 0).' queries per second.';
                    elseif ($qpm > 2) echo round($qpm, 0).' queries per minute.';
                    elseif ($qph > 2) echo round($qph, 0).' queries per hour.';
                    elseif ($qpd > 2) echo round($qpd, 0).' queries per day.';
                    elseif ($qpw > 2) echo round($qpw, 0).' queries per week.';
                    elseif ($qpM > 2) echo round($qpM, 0).' queries per month.';
                    elseif ($qpq > 2) echo round($qpq, 0).' queries per quarter.';
                    else              echo round($qpy, 0).' queries per year.';
                }

            ?>
        </p>
        <table class="dataTable">
            <thead>
                <tr>
                    <th>Boolean Attributes</th>
                    <th>Count</th>
                    <th>Sum</th>
                    <th>%</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>Query Count</td>
                    <td class="number"><?php echo $historyData['ts_cnt']; ?></td>
                    <td class="center">-</td>
                    <td class="center">-</td></tr>
                <tr><td>Query Cache</td>
                    <td class="number"><?php echo $historyData['QC_Hit_cnt']; ?></td>
                    <td class="number"><?php echo $historyData['QC_Hit_sum']; ?></td>
                    <td class="number"><?php if ($historyData['QC_Hit_cnt']) echo round($historyData['QC_Hit_sum']/$historyData['QC_Hit_cnt']*100, 0); ?></td></tr>
                <tr><td>Full Scan</td>
                    <td class="number"><?php echo $historyData['Full_scan_cnt']; ?></td>
                    <td class="number"><?php echo $historyData['Full_scan_sum']; ?></td>
                    <td class="number"><?php if ($historyData['Full_scan_cnt']) echo round($historyData['Full_scan_sum']/$historyData['Full_scan_cnt']*100, 0); ?></td></tr>
                <tr><td>Full Join</td>
                    <td class="number"><?php echo $historyData['Full_join_cnt']; ?></td>
                    <td class="number"><?php echo $historyData['Full_join_sum']; ?></td>
                    <td class="number"><?php if ($historyData['Full_join_cnt']) echo round($historyData['Full_join_sum']/$historyData['Full_join_cnt']*100, 0); ?></td></tr>
                <tr><td>Temporary Tables</td>
                    <td class="number"><?php echo $historyData['Tmp_table_cnt']; ?></td>
                    <td class="number"><?php echo $historyData['Tmp_table_sum']; ?></td>
                    <td class="number"><?php if ($historyData['Tmp_table_cnt']) echo round($historyData['Tmp_table_sum']/$historyData['Tmp_table_cnt']*100, 0); ?></td></tr>
                <tr><td>On Disk Temporary Tables</td>
                    <td class="number"><?php echo $historyData['Disk_tmp_table_cnt']; ?></td>
                    <td class="number"><?php echo $historyData['Disk_tmp_table_sum']; ?></td>
                    <td class="number"><?php if ($historyData['Disk_tmp_table_cnt']) echo round($historyData['Disk_tmp_table_sum']/$historyData['Disk_tmp_table_cnt']*100, 0); ?></td></tr>
                <tr><td>File Sorts</td>
                    <td class="number"><?php echo $historyData['Filesort_cnt']; ?></td>
                    <td class="number"><?php echo $historyData['Filesort_sum']; ?></td>
                    <td class="number"><?php if ($historyData['Filesort_cnt']) echo round($historyData['Filesort_sum']/$historyData['Filesort_cnt']*100, 0); ?></td></tr>
                <tr><td>On Disk File Sorts</td>
                    <td class="number"><?php echo $historyData['Disk_filesort_cnt']; ?></td>
                    <td class="number"><?php echo $historyData['Disk_filesort_sum']; ?></td>
                    <td class="number"><?php if ($historyData['Disk_filesort_cnt']) echo round($historyData['Disk_filesort_sum']/$historyData['Disk_filesort_cnt']*100, 0); ?></td></tr>
            </tbody>
        </table>
        <br>
        <br>
        <table class="dataTable">
            <thead>
                <tr>
                    <th>Attribute</th>
                    <th>Median</th>
                    <th>95%</th>
                    <th>StdDev</th>
                    <th>Min</th>
                    <th>Max</th>
                    <th>Sum</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>Query Time (ms)</td>
                    <td class="number"><?php echo $historyData['Query_time_median']; ?></td>
                    <td class="number"><?php echo $historyData['Query_time_pct_95']; ?></td>
                    <td class="number"><?php echo $historyData['Query_time_stddev']; ?></td>
                    <td class="number"><?php echo $historyData['Query_time_min']; ?></td>
                    <td class="number"><?php echo $historyData['Query_time_max']; ?></td>
                    <td class="number"><?php echo $historyData['Query_time_sum']; ?></td>
                </tr><tr><td>Lock Time (ms)</td>
                    <td class="number"><?php echo $historyData['Lock_time_median']; ?></td>
                    <td class="number"><?php echo $historyData['Lock_time_pct_95']; ?></td>
                    <td class="number"><?php echo $historyData['Lock_time_stddev']; ?></td>
                    <td class="number"><?php echo $historyData['Lock_time_min']; ?></td>
                    <td class="number"><?php echo $historyData['Lock_time_max']; ?></td>
                    <td class="number"><?php echo $historyData['Lock_time_sum']; ?></td>
                </tr><tr><td>Rows Sent</td>
                    <td class="number"><?php echo $historyData['Rows_sent_median']; ?></td>
                    <td class="number"><?php echo $historyData['Rows_sent_pct_95']; ?></td>
                    <td class="number"><?php echo $historyData['Rows_sent_stddev']; ?></td>
                    <td class="number"><?php echo $historyData['Rows_sent_min']; ?></td>
                    <td class="number"><?php echo $historyData['Rows_sent_max']; ?></td>
                    <td class="number"><?php echo $historyData['Rows_sent_sum']; ?></td>
                </tr><tr><td>Rows Examined</td>
                    <td class="number"><?php echo $historyData['Rows_examined_median']; ?></td>
                    <td class="number"><?php echo $historyData['Rows_examined_pct_95']; ?></td>
                    <td class="number"><?php echo $historyData['Rows_examined_stddev']; ?></td>
                    <td class="number"><?php echo $historyData['Rows_examined_min']; ?></td>
                    <td class="number"><?php echo $historyData['Rows_examined_max']; ?></td>
                    <td class="number"><?php echo $historyData['Rows_examined_sum']; ?></td>
                </tr><tr><td>Rows Affected</td>
                    <td class="number"><?php echo $historyData['Rows_affected_median']; ?></td>
                    <td class="number"><?php echo $historyData['Rows_affected_pct_95']; ?></td>
                    <td class="number"><?php echo $historyData['Rows_affected_stddev']; ?></td>
                    <td class="number"><?php echo $historyData['Rows_affected_min']; ?></td>
                    <td class="number"><?php echo $historyData['Rows_affected_max']; ?></td>
                    <td class="number"><?php echo $historyData['Rows_affected_sum']; ?></td>
                </tr><tr><td>Rows Read</td>
                    <td class="number"><?php echo $historyData['Rows_read_median']; ?></td>
                    <td class="number"><?php echo $historyData['Rows_read_pct_95']; ?></td>
                    <td class="number"><?php echo $historyData['Rows_read_stddev']; ?></td>
                    <td class="number"><?php echo $historyData['Rows_read_min']; ?></td>
                    <td class="number"><?php echo $historyData['Rows_read_max']; ?></td>
                    <td class="number"><?php echo $historyData['Rows_read_sum']; ?></td>
                </tr><tr><td>Merge_passes</td>
                    <td class="number"><?php echo $historyData['Merge_passes_median']; ?></td>
                    <td class="number"><?php echo $historyData['Merge_passes_pct_95']; ?></td>
                    <td class="number"><?php echo $historyData['Merge_passes_stddev']; ?></td>
                    <td class="number"><?php echo $historyData['Merge_passes_min']; ?></td>
                    <td class="number"><?php echo $historyData['Merge_passes_max']; ?></td>
                    <td class="number"><?php echo $historyData['Merge_passes_sum']; ?></td>
                </tr><tr><td>InnoDB IO Read Ops</td>
                    <td class="number"><?php echo $historyData['InnoDB_IO_r_ops_median']; ?></td>
                    <td class="number"><?php echo $historyData['InnoDB_IO_r_ops_pct_95']; ?></td>
                    <td class="number"><?php echo $historyData['InnoDB_IO_r_ops_stddev']; ?></td>
                    <td class="number"><?php echo $historyData['InnoDB_IO_r_ops_min']; ?></td>
                    <td class="number"><?php echo $historyData['InnoDB_IO_r_ops_max']; ?></td>
                    <td class="number"></td>
                </tr><tr><td>InnoDB IO Read Bytes</td>
                    <td class="number"><?php echo $historyData['InnoDB_IO_r_bytes_median']; ?></td>
                    <td class="number"><?php echo $historyData['InnoDB_IO_r_bytes_pct_95']; ?></td>
                    <td class="number"><?php echo $historyData['InnoDB_IO_r_bytes_stddev']; ?></td>
                    <td class="number"><?php echo $historyData['InnoDB_IO_r_bytes_min']; ?></td>
                    <td class="number"><?php echo $historyData['InnoDB_IO_r_bytes_max']; ?></td>
                    <td class="number"></td>
                </tr><tr><td>InnoDB IO Read Wait</td>
                    <td class="number"><?php echo $historyData['InnoDB_IO_r_wait_median']; ?></td>
                    <td class="number"><?php echo $historyData['InnoDB_IO_r_wait_pct_95']; ?></td>
                    <td class="number"><?php echo $historyData['InnoDB_IO_r_wait_stddev']; ?></td>
                    <td class="number"><?php echo $historyData['InnoDB_IO_r_wait_min']; ?></td>
                    <td class="number"><?php echo $historyData['InnoDB_IO_r_wait_max']; ?></td>
                    <td class="number"></td>
                </tr><tr><td>InnoDB Record Lock Wait</td>
                    <td class="number"><?php echo $historyData['InnoDB_rec_lock_wait_median']; ?></td>
                    <td class="number"><?php echo $historyData['InnoDB_rec_lock_wait_pct_95']; ?></td>
                    <td class="number"><?php echo $historyData['InnoDB_rec_lock_wait_stddev']; ?></td>
                    <td class="number"><?php echo $historyData['InnoDB_rec_lock_wait_min']; ?></td>
                    <td class="number"><?php echo $historyData['InnoDB_rec_lock_wait_max']; ?></td>
                    <td class="number"></td>
                </tr><tr><td>InnoDB Queue Wait</td>
                    <td class="number"><?php echo $historyData['InnoDB_queue_wait_median']; ?></td>
                    <td class="number"><?php echo $historyData['InnoDB_queue_wait_pct_95']; ?></td>
                    <td class="number"><?php echo $historyData['InnoDB_queue_wait_stddev']; ?></td>
                    <td class="number"><?php echo $historyData['InnoDB_queue_wait_min']; ?></td>
                    <td class="number"><?php echo $historyData['InnoDB_queue_wait_max']; ?></td>
                    <td class="number"></td>
                </tr><tr><td>InnoDB Distinct Pages</td>
                    <td class="number"><?php echo $historyData['InnoDB_pages_distinct_median']; ?></td>
                    <td class="number"><?php echo $historyData['InnoDB_pages_distinct_pct_95']; ?></td>
                    <td class="number"><?php echo $historyData['InnoDB_pages_distinct_stddev']; ?></td>
                    <td class="number"><?php echo $historyData['InnoDB_pages_distinct_min']; ?></td>
                    <td class="number"><?php echo $historyData['InnoDB_pages_distinct_max']; ?></td>
                    <td class="number"></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
    $(function() {
        $('.dataTable').each(function(index, table) {
            $(table).dataTable({
                "bStateSave":       false,
                "bJQueryUI":        true,
                "bPaginate":        false,
                "bLengthChange":    false,
                "bFilter":          false,
                "bSort":            false,
                "bInfo":            false,
                "sDom":             "t"
            });
        });
    });
</script>

<?php include('templates/footer.php'); ?>
