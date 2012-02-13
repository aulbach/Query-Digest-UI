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
                          $(data.Explain[i].table),
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
   $.ajax({
            url: 'descTable.php',
            data : {
                checksum: checksum,
                label: label,
                db: database,
                table: table
            },

            success: function(data) {
               var dlog = $('<div class="dialog">');
               
               var str = '<div class="tabs">';
               str += '<ul><li><a href="#dialogDescCreate">Create</a></li>';
               str += '<li><a href="#dialogDescDesc">Desc</a></li>';
               str += '<li><a href="#dialogDescIndexes">Indexes</a></li></ul>';
               
               str += '<div id="dialogDescCreate">';
               str += data.create;
               str += '</div>';
               
               str += '<div id="dialogDescDesc" style="font-size: 80%;">';
               str += data.desc;
               str += '</div>';
               
               str += '<div id="dialogDescIndexes" style="font-size: 80%;">';
               str += data.indexes;
               str += '</div>';
               
               
               str += '</div>';
               $(dlog).html(str);
               
               $(dlog).dialog({
                  title:    data.title,
                  minWidth: 400
               });
               $("div.dialog div.tabs").tabs();
            }
   });
            
    return;
}

function lookupCol(label, database, table, column) {
    return;
}
