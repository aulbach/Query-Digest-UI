function generateMysqlDocLinks() {
    $( ' span.syntax_alpha,'
      +' span.syntax_punct'
      ).each(function() {
        var txt = $(this).text().toLowerCase();
        if (help_topic[txt])
            $(this).html('<a target="_blank" href="'+help_topic[txt]+'">'+$(this).html()+'</a>');
        });
}

$(generateMysqlDocLinks);
