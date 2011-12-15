
<?php include('templates/header.php'); ?>


<div class="accordion">
    <h3><a href="#">Filters</a></h3>
    <div>
        <p>Search Sample Query <input type="text" id="searchSampleQuery" name="searchSampleQuery"></p>
        <p>Last seen between <input type="text" id="lastSeenStart" class="datepicker"> and <input type="text" id="lastSeenEnd" class="datepicker">
    </div>
</div>

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
