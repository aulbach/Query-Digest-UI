var docFuncs = {
    'date'   : 'http://dev.mysql.com/doc/refman/5.1/en/date-and-time-functions.html#function_date',
    'sum'    : 'http://dev.mysql.com/doc/refman/5.1/en/group-by-functions.html#function_sum',
    'round'  : 'http://dev.mysql.com/doc/refman/5.0/en/mathematical-functions.html#function_round'
}

var docReserved = {
    'select' : 'http://dev.mysql.com/doc/refman/5.1/en/select.html',
    'group'  : 'http://dev.mysql.com/doc/refman/5.1/en/group-by-functions.html',
    'order'  : 'http://dev.mysql.com/doc/refman/5.1/en/sorting-rows.html'
}

function generateMysqlLinks() {

    $('span.syntax_alpha_functionName').each(function() {
        var txt = $(this).text().toLowerCase();
        if (docFuncs[txt])
            $(this).html('<a target="_blank" href="'+docFuncs[txt]+'">'+$(this).html()+'</a>');
        });
    $('span.syntax_alpha_reservedWord').each(function() {
        var txt = $(this).text().toLowerCase();
        if (docReserved[txt])
            $(this).html('<a target="_blank" href="'+docReserved[txt]+'">'+$(this).html()+'</a>');
        });

}

$(generateMysqlLinks);
