$(function() {
   $('#doExplain').click(function() {
        explainDb = $('#explainDb option:selected').val();
        checksum = $('#checksum').val();
        $.ajax({
            url: 'explain.php',
            data : {
                checksum: checksum,
                explainDb: explainDb
            },
            
            success: function(data) {
                $('#normalizedQuery').text(data.Query);
                $('#explainPlan').dataTable().fnClearTable();
                for (var i=0; i < data.Explain.length; i++) {
                    ret = $('#explainPlan').dataTable().fnAddData([
                        data.Explain[i].id,
                        data.Explain[i].select_type,
                        data.Explain[i].table,
                        data.Explain[i].type,
                        data.Explain[i].possible_keys,
                        data.Explain[i].key,
                        data.Explain[i].key_len,
                        data.Explain[i].ref,
                        data.Explain[i].rows,
                        data.Explain[i].Extra
                    ], true);
                }
                $('#explainAccordion').accordion( "resize" );
            }
        });
   });
});
