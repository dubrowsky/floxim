fx_additional_menu = function ( ) {
    this.container = $('#fx_admin_additional_menu');
}

fx_additional_menu.prototype.load = function () {
    var menu = this.container;
        
    $.each( ['undo', 'redo'], function(k, v) {
        var item = $('<div>').addClass('fx_admin_button').addClass('fx_admin_button_'+v).appendTo(menu);
        item.click( function(){
            $fx.handle_button(v);
        });
    });

    $('<span/>').addClass('fx_admin_additional_menu_divider').appendTo(menu);
    $('<a/>').text('выход').attr('href', '/floxim/index.php?essence=module_auth&action=logout').appendTo(menu);
}
