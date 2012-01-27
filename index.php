<?php
    require_once('init.php');

	$users = Database::find('review')->query('SELECT DISTINCT IFNULL(reviewed_by, "") FROM '.Database::escapeField($reviewhost['review_table']));
    $Reviewers = "[ 'None' ";
    while($user = $users->fetch_col())
        $Reviewers .= ",'$user' ";
    $Reviewers .= " ]";
    unset($users);

	require_once('templates/header.php');
?>

<table id="Queries">
	<thead>
		<tr>
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
					{ "sClass": "count number",		"bSearchable": false, "bVisible": true, "aTargets": [ 0 ] },
					{ "sClass": "time number",      "bSearchable": false, "bVisible": true, "aTargets": [ 1 ] },
					{ "sClass": "avgTime number",	"bSearchable": false, "bVisible": true, "aTargets": [ 2 ] },
					{ "sClass": "firstSeen date",	"bSearchable": false, "bVisible": true, "aTargets": [ 3 ] },
					{ "sClass": "lastSeen date",	"bSearchable": false, "bVisible": true, "aTargets": [ 4 ] },
					{ "sClass": "fingerprint", 		"bSearchable": true,  "bVisible": true, "aTargets": [ 5 ] },
					{ "sClass": "reviewed_on date", "bSearchable": false, "bVisible": true, "aTargets": [ 6 ] },
					{ "sClass": "reviewed_by", 		"bSearchable": true,  "bVisible": true, "aTargets": [ 7 ] },
					{ "sClass": "comments", 		"bSearchable": true,  "bVisible": true, "aTargets": [ 8 ] },
					{ "sClass": "details", 			"bSearchable": false, "bVisible": true, "aTargets": [ 9 ], "bSortable": false }
				],
			"oColVis": {
				"aiExclude": [ 9 ]
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
			"fnStateSaveCallback": function(oSettings, oData) {
				oData.aoSearchCols = [];
				oData.oFilter = [];
				oData.oSearch = [];
				return true;
			},
			"fnStateLoadCallback": function(oSettings, oData) {
				oData.aoSearchCols = [];
				oData.oFilter = [];
				oData.oSearch = [];
				oldVis = [];
				if (typeof oData.abVisCols == "object")
					oldVis = oData.abVisCols;
				oData.abVisCols = [];
				return true;
			}
		}).columnFilter({
			bUseColVis: true,
			sPlaceHolder: 'tfoot',
			aoColumns: [
				{ type: "number-range" },
				{ type: "number-range" },
				{ type: "number-range" },
				{ type: "date-range" },
				{ type: "date-range" },
				{ type: "text" },
				{ type: "date-range" },
				{ type: "select", values: <?php echo $Reviewers; ?> },
				{ type: "text" },
				null
			]
		});

    // Set visability defaults
        if (typeof oldVis === 'undefined')
            oldVis = [true, false, true, false, true, true, false, true, false, true];

		if (oldVis.length == oTable.fnSettings().aoColumns.length)
			for (var i=0; i < oldVis.length; i++)
				oTable.fnSetColumnVis(i, oldVis[i]);

		$(window).bind('resize', function () {
			oTable.fnAdjustColumnSizing();
		} );
	 });
</script>

<?php
	require_once('templates/footer.php');
