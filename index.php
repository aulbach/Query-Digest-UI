<?php
    require_once('init.php');

	$users = Database::find('review')->query('SELECT DISTINCT reviewed_by FROM '.Database::escapeField($reviewhost['review_table']).' WHERE reviewed_by IS NOT NULL');
    $Reviewers = " 'None' ";
    while(($user = $users->fetch_col()) !== false)
		if (strlen($user))
			$Reviewers .= ",'$user' ";
    $Reviewers .= " ";
    unset($users);

	require_once('templates/header.php');
?>

<table id="Queries">
	<thead>
		<tr>
			<th id="queriesChecksum"      	class="checksum"    > Checksum     </th>
			<th id="queriesColCount"      	class="count"       > Count        </th>
			<th id="queriesColTime"       	class="time"        > Total ms     </th>
			<th id="queriesColAvgTime"    	class="avgTime"     > Avg ms       </th>
			<th id="queriesColFirstSeen"  	class="firstSeen"   > First Seen   </th>
			<th id="queriesColLastSeen"   	class="lastSeen"    > Last Seen    </th>
			<th id="queriesColfingerprint"	class="fingerprint"	> Query Fingerprint</th>
			<th id="queriesColReviewedOn" 	class="reviewed_on" > Reviewed On  </th>
			<th id="queriesColReviewedBy" 	class="reviewed_by" > Reviewed By  </th>
			<th id="queriesColComments"   	class="comments"    > Comments     </th>
			<th id="queriesColDetails"    	class="details"     > &nbsp;       </th>
		</tr>
	</thead>
	<tbody>
	</tbody>
	<tfoot>
		<tr>
			<th class=""></th>
			<th class="number"></th>
			<th class="number"></th>
			<th class="number"></th>
			<th class="date"></th>
			<th class="date"></th>
			<th class=""></th>
			<th class="date"></th>
			<th class=""></th>
			<th class=""></th>
			<th class=""></th>
		</tr>
	</tfoot>
</table>

<script type="text/javascript">
	 $(function() {

		 oTable = $('#Queries').dataTable({
			"sPaginationType": 	"full_numbers",
            "bDeferRender":     true,
			"bServerSide": 		true,
			"sAjaxSource": 		"list-ajax.php",
			"sDom":             '"R<"H"Cpr>t<"F"il>"',
			"bJQueryUI":        true,
			"bStateSave":       true,
        // Store the cookie for one year
            "iCookieDuration":  31556926,
			"bProcessing":      true,
			"aaSort":           [],
			"bAutoWidth": 		true,
			"aoColumnDefs": [
					{ "sClass": "checksum",		    "bSearchable": true,  "aTargets": [  0 ] },
					{ "sClass": "count number",		"bSearchable": false, "aTargets": [  1 ] },
					{ "sClass": "time number",      "bSearchable": false, "aTargets": [  2 ] },
					{ "sClass": "avgTime number",	"bSearchable": false, "aTargets": [  3 ] },
					{ "sClass": "firstSeen date",	"bSearchable": false, "aTargets": [  4 ] },
					{ "sClass": "lastSeen date",	"bSearchable": false, "aTargets": [  5 ] },
					{ "sClass": "fingerprint", 		"bSearchable": true,  "aTargets": [  6 ] },
					{ "sClass": "reviewed_on date", "bSearchable": false, "aTargets": [  7 ] },
					{ "sClass": "reviewed_by", 		"bSearchable": true,  "aTargets": [  8 ] },
					{ "sClass": "comments", 		"bSearchable": true,  "aTargets": [  9 ] },
					{ "sClass": "details", 			"bSearchable": false, "aTargets": [ 10 ], "bSortable": false }
				],
			"oColVis": {
				"aiExclude": [ 10 ]
			},
            "fnDrawCallback" : function() {
                $("a.details").fancybox({
                    type:           'iframe',
                    width:          '98%',
                    height:         '98%',
                    centerOnScroll: true,
                    padding:        0,
                    margin:         10
                });
                return true;
            },
            "fnInitComplete": function(oSettings, json) {
                if (typeof oSettings.saved_aaSorting != 'object') {
                    oTable.fnSetColumnVis(  0, <?php echo ($settings['defaultColumnVis']['Checksum']    ? 'true' : 'false'); ?>, false);
                    oTable.fnSetColumnVis(  1, <?php echo ($settings['defaultColumnVis']['Count']       ? 'true' : 'false'); ?>, false);
                    oTable.fnSetColumnVis(  2, <?php echo ($settings['defaultColumnVis']['TotalMS']     ? 'true' : 'false'); ?>, false);
                    oTable.fnSetColumnVis(  3, <?php echo ($settings['defaultColumnVis']['AvgMS']       ? 'true' : 'false'); ?>, false);
                    oTable.fnSetColumnVis(  4, <?php echo ($settings['defaultColumnVis']['FirstSeen']   ? 'true' : 'false'); ?>, false);
                    oTable.fnSetColumnVis(  5, <?php echo ($settings['defaultColumnVis']['LastSeen']    ? 'true' : 'false'); ?>, false);
                    oTable.fnSetColumnVis(  6, <?php echo ($settings['defaultColumnVis']['Fingerprint'] ? 'true' : 'false'); ?>, false);
                    oTable.fnSetColumnVis(  7, <?php echo ($settings['defaultColumnVis']['ReviewedOn']  ? 'true' : 'false'); ?>, false);
                    oTable.fnSetColumnVis(  8, <?php echo ($settings['defaultColumnVis']['ReviewedBy']  ? 'true' : 'false'); ?>, false);
                    oTable.fnSetColumnVis(  9, <?php echo ($settings['defaultColumnVis']['Comments']    ? 'true' : 'false'); ?>, true);
                }
            },
		}).columnFilter({
			bUseColVis: true,
			sPlaceHolder: 'tfoot',
			aoColumns: [
				{ type: "text" },
				{ type: "number-range" },
				{ type: "number-range" },
				{ type: "number-range" },
				{ type: "date-range" },
				{ type: "date-range" },
				{ type: "text" },
				{ type: "date-range" },
				{ type: "select", values: [<?php echo $Reviewers; ?>] },
				{ type: "text" },
				null
			]
		});

		$(window).bind('resize', function () {
			oTable.fnAdjustColumnSizing();
		} );
	 });
</script>

<?php
	require_once('templates/footer.php');
