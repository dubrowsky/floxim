fx_mode_menu = function ( ) {
    this.container = $('#fx_admin_page_modes');
    this.data =  {
        "view" : {
            "name" : "Просмотр", 
            "href" : "view"
        },
        "edit" : {
            "name" : "Редактирование", 
            "href" : "edit"
        }, 
        "design" : {
            "name" : "Дизайн",
            "href" : "design"
        }
    };
}

fx_mode_menu.prototype.load = function ( active ) {
    var cont = this.container.html('');
    $.each(this.data, function(key, value) {
        item = $('<a/>').data('key', value.href).text(value.name).attr('href', '#page.'+value.href ).appendTo(cont);
    });
}
fx_mode_menu.prototype.set_active = function ( active ) {
    $('.fx_admin_page_modes_active').removeClass('fx_admin_page_modes_active');
    $('.fx_admin_page_modes_arrow').remove();
    $('.fx_admin_page_modes_line').remove();
    
    var arrow = $("<span>").addClass('fx_admin_page_modes_arrow');
    var line = $("<span>").addClass('fx_admin_page_modes_line');
    
    $.each( $('a', this.container), function(key, value) {
        var item = $(this);
        if ( active ==  item.data('key') ) {
            item.addClass('fx_admin_page_modes_active').append(arrow).append(line);
            arrow.css('left', 0.5*item.width()-3); // 3 - пол-ширины стрелки
            line.width(item.width() + parseInt(item.css('margin-left')) + parseInt(item.css('margin-right')) );
        }
    });
}


