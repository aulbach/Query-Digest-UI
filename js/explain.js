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
                $('#normalizedQuery').html(data.Query);
                $('#explainPlan').dataTable().fnClearTable();
                if (typeof data.Warnings != 'undefined') {
                  $('#dialogWarning').text('');
                  for (var i=0; i < data.Warnings.length; i++) {
                     var line = data.Warnings[i].Level + ' ['+data.Warnings[i].Code+'] '+data.Warnings[i].Message+"\n";
                     $('#dialogWarning').text($('#dialogWarning').text() + line);
                  }
                  $('#dialogWarning').dialog('open');
                }
                if (typeof data.Explain != 'undefined') {
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
                }
                $('#explainAccordion').accordion( "resize" );
            }
        });
   });
});

function lookupDatabase(label, database) {
    return;
}

function lookupTable(label, database, table) {
    return;
}

function lookupCol(label, database, table, column) {
    return;
}
