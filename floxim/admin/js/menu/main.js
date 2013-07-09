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
        var node = $('<div class="fx_admin_main_menu_item"></div>').appendTo(self.container);
        $('<a/>').data('key', key).text(item.name).attr('href', item.href).appendTo(node);
        if ( i++ < count-1 ) {
            self.container.append('<span class="fx_main_menu_separator" />');
        }
        if (item.children) {
            node.append(
                '<span class="fx_admin_more_menu_after_container">'+
                    '<span class="fx_admin_more_menu_after"></span>'+
                '</span>'
            );
            var sub = '<div class="fx_main_submenu">';
            $.each(item.children, function(sub_key, sub_item) {
                sub += '<div class="fx_main_subitem">'+
                            '<a href="'+sub_item.href+'">'+sub_item.name+'</a>'+
                       '</div>';
            });
            sub += '</div>';
            node.append(sub);
        }
    });
    this.container.on('click', '.fx_admin_more_menu_after_container', function() {
        var arrow = $('.fx_admin_more_menu_after', this);
        var item = arrow.closest('.fx_admin_main_menu_item');
        var sub = $('.fx_main_submenu', item);
        var item_offset = item.offset();
        sub.css({
            position:'absolute',
            top: (item_offset.top+item.height()) + 'px',
            left: item_offset.left + 'px'
        });
        sub.show();
        if (sub.width() < item.width()) {
            sub.css({width: item.width()+'px'});
        }
        arrow.addClass('fx_admin_more_menu_after_active');
        $fx.panel.one('fx.click', function() {
            sub.hide();
            arrow.removeClass('fx_admin_more_menu_after_active');
            return false;
        });
        return false;
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