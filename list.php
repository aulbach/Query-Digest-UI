
<?php include('templates/header.php'); ?>


<div class="tabs">
    <ul>
        <li><a href="#tabs-data">Data</a></li>
		<li><a href="#tabs-filters">Filters</a></li>
		<li><a href="#tabs-columns">Columns</a></li>
	</ul>
    <div id="tabs-filters">
        <p>Search Sample Query <input type="text" id="searchSampleQuery" name="searchSampleQuery"></p>
        <p>Last seen between <input type="text" id="lastSeenStart" class="datepicker"> and <input type="text" id="lastSeenEnd" class="datepicker">
    </div>
    <div id="tabs-columns">
    </div>
    <div id="tabs-data">
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
        "bJQueryUI":        true,
        "bStateSave":       true,
        "bProcessing":      true,
        "aaSort":           []
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
} );
</script>

<?php include('templates/footer.php');
