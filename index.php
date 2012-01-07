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
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Query Digest UI</title>
        
		<script src="js/jquery-1.6.2.min.js" type="text/javascript"></script>
		
        <script src="js/jquery-ui-1.8.16.custom.min.js" type="text/javascript"></script>
		<link rel="stylesheet" href="css/cupertino/jquery-ui-1.8.16.custom.css" type="text/css" media="all" >
			
		<script src="js/DataTables/media/js/jquery.dataTables.js" type="text/javascript"></script>
		<script src="js/DataTables/extras/jquery-datatables-column-filter/media/js/jquery.dataTables.columnFilter.js" type="text/javascript"></script>
		
		<script src="js/DataTables/extras/ColVis-1.0.6/media/js/ColVis.min.js" type="text/javascript"></script>
		<link rel="stylesheet" href="js/DataTables/extras/ColVis-1.0.6/media/css/ColVis.css" media="screen" >
			
		<script src="js/DataTables/extras/ColReorder-1.0.4/media/js/ColReorder.min.js" type="text/javascript"></script>
		<link rel="stylesheet" href="js/DataTables/extras/ColReorder-1.0.4/media/css/ColReorder.css" media="screen" >
		
		<link rel="stylesheet" href="js/fancybox/jquery.fancybox-1.3.4.css" media="screen" >
		<script src="js/fancybox/jquery.fancybox-1.3.4.pack.js" type="text/javascript"></script>
		<script src="js/fancybox/jquery.easing-1.3.pack.js" type="text/javascript"></script>
		
		<link rel="stylesheet" href="css/style.css" type="text/css" media="all" >
		
		<script>
		   $(document).ready(function() {
               
				$("a.details").fancybox({
					type: 			'iframe',
					width:			'98%',
					height: 		'98%',
					centerOnScroll:	true,
					padding:		0,
					margin:			10
				});
               
           });
	   </script>

	</head>
	<body>

	 <div id="content">
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
				
					oTable = $('#Queries').dataTable({
						"sDom":             '"R<"H"rCp>t<"F"il>"',
						"bJQueryUI":        true,
						"bStateSave":       true,
						"bProcessing":      false,
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
						},
						"fnStateSaveParams": function(oSettings, oData) {
							 oData.aoSearchCols = [];
							 oData.oFilter = [];
							 oData.oSearch = [];
						},
						"fnStateLoadParams": function(oSettings, oData) {
							 oData.aoSearchCols = [];
							 oData.oFilter = [];
							 oData.oSearch = [];
							 oldVis = oData.abVisCols;
							 oData.abVisCols = [];
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
					
					 for (var i=0; i < oTable.fnSettings().aoColumns.length; i++)
						 oTable.fnSetColumnVis(i, oldVis[i]);
				});
		   </script>
		</div>
    </body>
</html>
