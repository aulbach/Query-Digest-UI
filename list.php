
<?php include('templates/header.php'); ?>


<div class="tabs">
    <ul>
        <li><a href="#tabs-data">Data</a></li>
		<li><a href="#tabs-filters">Filters</a></li>
	</ul>
    <div id="tabs-filters">
        <p>Last Seen between  <input type="text" id="lastSeenStart" class="datepicker"> and <input type="text" id="lastSeenEnd" class="datepicker"></p>
    </div>
    <div id="tabs-data">
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
            </tfoot>
        </table>
    </div>
</div>

<script type="text/javascript">
$(function() {
    $( "#tabs" ).tabs({
    });

    $('#Queries').dataTable({
        "sDom":             '"R<"H"Cfr>t<"F"ilp>"',
        "bJQueryUI":        true,
        "bStateSave":       true,
        "bProcessing":      true,
        "aaSort":           [],
        "aoColumnDefs": [
                { "bSearchable": false, "bVisible": true,  "aTargets": [ 0 ] },
                { "bSearchable": false, "bVisible": false, "aTargets": [ 1 ] },
                { "bSearchable": false, "bVisible": true,  "aTargets": [ 2 ] },
                { "bSearchable": false, "bVisible": false, "aTargets": [ 3 ] },
                { "bSearchable": false, "bVisible": true,  "aTargets": [ 4 ] },
                { "bSearchable": true,  "bVisible": true,  "aTargets": [ 5 ] },
                { "bSearchable": false, "bVisible": false, "aTargets": [ 6 ] },
                { "bSearchable": true,  "bVisible": false, "aTargets": [ 7 ] },
                { "bSearchable": true,  "bVisible": false, "aTargets": [ 8 ] },
                { "bSearchable": false, "bVisible": true,  "aTargets": [ 9 ], "bSortable": false },
            ]
    });

    var lastSeenDates = $( "#lastSeenStart, #lastSeenEnd" ).datepicker({
        defaultDate: "+0d",
        maxDate: '+0d',
        changeMonth: true,
        numberOfMonths: 1,
        onSelect: function( selectedDate ) {
            var option = this.id == "lastSeenStart" ? "minDate" : "maxDate",
                instance = $( this ).data( "datepicker" ),
                date = $.datepicker.parseDate(
                    instance.settings.dateFormat ||
                    $.datepicker._defaults.dateFormat,
                    selectedDate, instance.settings );
            lastSeenDates.not( this ).datepicker( "option", option, date );
        }
    });
});

</script>

<?php include('templates/footer.php');
