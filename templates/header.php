<!DOCTYPE html>
<html>
    <head>
        <title><?php if(!is_null($settings['title'])) echo $settings['title'].' - '; ?>Query Digest UI</title>

		<script src="js/jquery-1.7.1.min.js" type="text/javascript"></script>

        <script src="js/jquery-ui-1.8.17.custom.min.js" type="text/javascript"></script>
		<link rel="stylesheet" href="css/cupertino/jquery-ui-1.8.16.custom.css" type="text/css" media="all" >

		<script src="js/DataTables/media/js/jquery.dataTables.js" type="text/javascript"></script>
		<script src="js/DataTables/extras/jquery-datatables-column-filter/media/js/jquery.dataTables.columnFilter.js" type="text/javascript"></script>

		<script src="js/DataTables/extras/ColVis/media/js/ColVis.min.js" type="text/javascript"></script>
		<link rel="stylesheet" href="js/DataTables/extras/ColVis/media/css/ColVis.css" media="screen" >

        <!-- Col Reordering breaks ajax datatables. Need to investigate.
		<script src="js/DataTables/extras/ColReorder/media/js/ColReorder.min.js" type="text/javascript"></script>
		<link rel="stylesheet" href="js/DataTables/extras/ColReorder/media/css/ColReorder.css" media="screen" >
        -->

		<link rel="stylesheet" href="js/fancybox/jquery.fancybox-1.3.4.css" media="screen" >
		<script src="js/fancybox/jquery.fancybox-1.3.4.pack.js" type="text/javascript"></script>
		<script src="js/fancybox/jquery.easing-1.3.pack.js" type="text/javascript"></script>

		<link rel="stylesheet" href="css/style.css" type="text/css" media="all" >
        <link rel="stylesheet" href="css/review.css" type="text/css" media="all" >
        <link rel="stylesheet" href="css/syntax.css" type="text/css" media="all" >

		<script src="js/explain.js" type="text/javascript"></script>
		<script src="js/docData.js" type="text/javascript"></script>
        <script src="js/doc.js" type="text/javascript"></script>
		
		<link href="data:image/x-icon;base64,AAABAAEAEBAAAAAAAABoBQAAFgAAACgAAAAQAAAAIAAAAAEACAAAAAAAAAEAAAAAAAAAAAAAAAEAAAAAAAC9qnMA9ubeAObKrAD///8A7s60AO7WvQDNupQA1bJ7AMWqcwD/8v8A//r2AM2qcwDevpwAtJ1aAN7StAC9oWIA//b2AN7CiwD27uYAtJlaAMWhYgCkhTEAzbqLAP/y7gD24s0A5trFAP/6/wD26t4AzaFiAN7KrADNtoMA/+7uAM2ycwD23s0A1cKcAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMiHQMDAwMDAwMDAwMDAwMNBQMDAwMDAwMDGgkDAwMaHRwCAwMDAwMPGCIDAwMDAw4RGgMDAwMEAhUDAwMDARMhAwMDAwMDEQoCAwMDAw8aAwMDAwMDAyIDAwMDAx8eAwMDAwMDAwMYGQMDAwMgEgMDAwMDAwMDBwMDAwMBAAMDAwMDAwMDEgIaAwMKDQMDAwMDAwMDAwwJBQMhExoDAwMDAwMDAxsiFxYUBgMDAwMDAwMDAwMECAsQAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA=" rel="icon" type="image/x-icon">

	</head>
	<body>

	<div id="content">
