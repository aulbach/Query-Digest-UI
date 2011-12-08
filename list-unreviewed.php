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
                         WHERE review.reviewed_on IS NULL
                      GROUP BY review.checksum
                            ');
$list->execute();

?>

<?php include('templates/header.php'); ?>

<div>
    <table id="Queries">
        <thead>
            <tr>
                <th class="count">Count</th>
                <th class="avgTime">Avg ms</th>
                <th class="lastSeen">Last Seen</th>
                <th class="sample">Sample Query</th>
                <th class="details">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            <?php
                while ($row = $list->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td class='count number'>".$row['count']."</td>";
                    echo "<td class='avgTime number'>".round(($row['time'] / $row['count']) * 1000, 0)."</td>";
                    echo "<td class='lastSeen date'>".$row['last_seen']."</td>";
                    echo "<td class='sample'>".substr($row['sample'], 0, 99999)."</td>";
                    echo '<td class="details"><a href="review.php?checksum='.$row['checksum'].'"><img src="images/details_open.png"></a></td>';
                    echo "</tr>";
                }
            ?>
        </tbody>
        <tfoot>
        </tfoot>
    </table>
</div>
<script type="text/javascript">
$(function() {
    $('#Queries').dataTable({
        "bJQueryUI":        true,
        "bStateSave":       true,
        "bProcessing":      true,
        "aaSort":           []
    });
} );
</script>

<?php include('templates/footer.php');
