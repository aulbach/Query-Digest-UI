		</div>
    </body>

<script type="text/javascript">
    $(function() {
        $( ".accordionOpen" ).accordion({
            collapsible: 		true
        });

        $( ".accordion" ).accordion({
            collapsible: 		true,
            active: 			false
        });
		
		$( ".datepicker" ).datepicker({
			showOtherMonths: 	true ,
			selectOtherMonths: 	true,
			maxDate: 			'+0d',
			duration: 			'fast'
		});
		
		$( ".tabs" ).tabs({
        });
    });
</script>

</html>
