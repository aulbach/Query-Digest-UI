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
			duration:       	'fast',
			dateFormat:         'yy-mm-dd'
		});
		$.datepicker.regional[""].dateFormat = 'yy-mm-dd';
		
		$('.inlinesparkline').sparkline(); 
    });
</script>

</html>
