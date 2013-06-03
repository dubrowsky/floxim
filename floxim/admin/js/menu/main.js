fx_main_menu = function ( data ) {
    this.container = $('#fx_admin_main_menu');
    this.data = data;
}

fx_main_menu.prototype.load = function () {
    this.load_items();
}

fx_main_menu.prototype.load_items = function () {
    var i=0, count = fx_object_length(this.data), self = this;
    $.each( this.data, function (key, item){
        $('<a/>').data('key', key).text(item.name).attr('href', item.href).appendTo(self.container);
        if ( i++ < count-1 ) {
            self.container.append('<span />');
        }
    });
}

fx_main_menu.prototype.set_active_item = function (item ) {
    var elements = $('a', this.container);
    elements.removeClass('fx_admin_main_menu_active'); 
    $.each( elements, function (k,v){
        v = $(v);
        if ( v.data('key') == item ) {
            v.addClass('fx_admin_main_menu_active');
            return false;
        }
    });
}