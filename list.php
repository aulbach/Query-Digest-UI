
<?php include('templates/header.php'); ?>

<table id="Queries">
    <thead>
        <tr>
            <th id="queriesColCount"      class="count"       > Count        </th>
            <th id="queriesColTime"       class="time"        > Total ms     </th>
            <th id="queriesColAvgTime"    class="avgTime"     > Avg ms       </th>
            <th id="queriesColFirstSeen"  class="firstSeen"   > First Seen   </th>
            <th id="queriesColLastSeen"   class="lastSeen"    > Last Seen    </th>
            <th id="queriesColSample"     class="sample"      > Sample Query </th>
            <th id="queriesColReviewedOn" class="reviewed_on" > Reviewed On  </th>
            <th id="queriesColReviewedBy" class="reviewed_by" > Reviewed By  </th>
            <th id="queriesColComments"   class="comments"    > Comments     </th>
            <th id="queriesColDetails"    class="details"     > &nbsp;       </th>
        </tr>
    </thead>
    <tbody>
        <?php
            while ($row = $list->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td class='count number'>".$row['count']."</td>";
                echo "<td class='time number'>".$row['time']."</td>";
                echo "<td class='avgTime number'>".$row['time_avg']."</td>";
                echo "<td class='firstSeen date'>".$row['first_seen']."</td>";
                echo "<td class='lastSeen date'>".$row['last_seen']."</td>";
                echo "<td class='sample'>".$row['sample']."</td>";
                
                echo "<td class='reviewed_on'>".$row['reviewed_on']."</td>";
                echo "<td class='reviewed_by'>".$row['reviewed_by']."</td>";
                echo "<td class='comments'>".$row['comments']."</td>";
                
                echo '<td class="details"><a class="details" href="review.php?checksum='.$row['checksum'].'"><img src="images/details_open.png"></a></td>';
                echo "</tr>";
            }
        ?>
    </tbody>
    <tfoot>
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
    </tfoot>
</table>

<script type="text/javascript">
$(function() {

    $('#Queries').dataTable({
        "sDom":             '"R<"H"Cfr>t<"F"ilp>"',
        "bJQueryUI":        true,
        "bStateSave":       true,
        "bProcessing":      true,
        "aaSort":           [],
        "aoColumnDefs": [
                { "bSearchable": false, "bVisible": true,  "aTargets": [ 0 ] },
                { "bSearchable": false, "bVisible": true, "aTargets": [ 1 ] },
                { "bSearchable": false, "bVisible": true,  "aTargets": [ 2 ] },
                { "bSearchable": false, "bVisible": true, "aTargets": [ 3 ] },
                { "bSearchable": false, "bVisible": true,  "aTargets": [ 4 ] },
                { "bSearchable": true,  "bVisible": true,  "aTargets": [ 5 ] },
                { "bSearchable": false, "bVisible": true, "aTargets": [ 6 ] },
                { "bSearchable": true,  "bVisible": true, "aTargets": [ 7 ] },
                { "bSearchable": true,  "bVisible": true, "aTargets": [ 8 ] },
                { "bSearchable": false, "bVisible": true,  "aTargets": [ 9 ], "bSortable": false },
            ],
        "oColVis": {
            "aiExclude": [ 9 ]
        }
    }).columnFilter({
        sPlaceHolder: 'tfoot',
        aoColumns: [
            { type: "number-range" },
            { type: "number-range" },
            { type: "number-range" },
            { type: "date-range" },
            { type: "date-range" },
            { type: "text" },
            { type: "date-range" },
            { type: "select" },
            { type: "text" },
            null
        ]
    });
});

</script>

<?php include('templates/footer.php');
