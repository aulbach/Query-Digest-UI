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
		
		$( ".tabs" ).tabs({
        });
		
		$.datepicker.setDefaults({
			numberOfMonths: 	2,
			showOtherMonths:   	true ,
			selectOtherMonths:  true,
			maxDate:       		'+0d',
			duration:       	'fast'
		});
    });
</script>

</html>
