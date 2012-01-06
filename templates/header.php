<!DOCTYPE html>
<html>
    <head>
        <title>Query Digest UI</title>
        <link rel="stylesheet" href="css/cupertino/jquery-ui-1.8.16.custom.css" type="text/css" media="all" >
        <link rel="stylesheet" href="css/style.css" type="text/css" media="all" >
		<link rel="stylesheet" href="css/superfish.css" media="screen" >
		<link rel="stylesheet" href="js/fancybox/jquery.fancybox-1.3.4.css" media="screen" >
		<link rel="stylesheet" href="css/ColVis.css" media="screen" >

        <script src="js/jquery-1.6.2.min.js" type="text/javascript"></script>
        <script src="js/jquery-ui-1.8.16.custom.min.js" type="text/javascript"></script>
        <script src="js/jquery.dataTables.js" type="text/javascript"></script>
		<script src="js/hoverIntent.js" type="text/javascript"></script>
		<script src="js/superfish.js" type="text/javascript"></script>
		<script src="js/supersubs.js" type="text/javascript"></script>
		
		<script src="js/fancybox/jquery.fancybox-1.3.4.pack.js" type="text/javascript"></script>
		<script src="js/fancybox/jquery.easing-1.3.pack.js" type="text/javascript"></script>
		
		<script src="js/ColVis.min.js" type="text/javascript"></script>
		
		<script>
		   $(document).ready(function() {
            
				if (top != self) {
					$('#navMenu').replaceWith('');
					$('#content').css('padding-top', '4px');
					$('#content').css('padding', '4px');
				}
				else {
					$('ul.sf-menu').superfish({
						delay:       1000,                            // one second delay on mouseout
						animation:   {opacity:'show',height:'show'},  // fade-in and slide-down animation
						speed:       'fast',                          // faster animation speed
						autoArrows:  false,                           // disable generation of arrow mark-up
						dropShadows: false                            // disable drop shadows
					});
				}
               
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

	<div id="navMenu">
		<ul id="menu" class="sf-menu">
			<li><a href="list-unreviewed.php" title="Never reviewed queries">Unreviewed Queries</a>
                <ul>
                    <li><a href="list-unreviewed.php">All</a></li>
                    <li><a href="list-unreviewed-past-month.php">Past Month</a></li>
                    <li><a href="list-unreviewed-past-two-week.php">Past Two Weeks</a></li>
                    <li><a href="list-unreviewed-past-week.php">Past Week</a></li>
                </ul>
            </li>
			<li><a href="list-reviewed-seen.php" title="Queries seen after a review">Seen after Reviewed Queries</a></li>
			<li><a href="list-reviewed.php" title="Reviewed Queries">Reviewed Queries</a>
                <ul>
                    <li><a href="list-reviewed.php">All</a></li>
                    <li><a href="list-reviewed-past-month.php">Past Month</a></li>
                    <li><a href="list-reviewed-past-two-week.php">Past Two Weeks</a></li>
                    <li><a href="list-reviewed-past-week.php">Past Week</a></li>
                </ul>
            </li>
		</ul>
	</div>

	<div id="content">
