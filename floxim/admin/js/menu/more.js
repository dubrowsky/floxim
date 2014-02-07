/* Меню "Еще" */
fx_more_menu = function ( data ) {
    this.container = $('#fx_admin_more_menu');
    this.data = data;
    this.name = fx_lang('Еще');
    
    this.is_hide = true;
}

fx_more_menu.prototype.set_name = function (name) {
    this.name = name;
}

fx_more_menu.prototype.load = function () {
    this.load_items();
    this.load_handlers();
}

fx_more_menu.prototype.load_items = function () {
    this.prepend = $('<span>').addClass('fx_admin_more_menu_prepend').appendTo(this.container);
    this.button = $('<span>').addClass('fx_admin_more_menu_button').text( this.name).appendTo(this.container);
    this.after = $('<span>').addClass('fx_admin_more_menu_after').appendTo(this.container);
    this.menu = $('<div/>').appendTo(this.container);
    var self = this;
    
    $.each(this.data, function(k, item){
        var element = $('<span>').text(item.name).appendTo(self.menu);
        element.click( function(){
            self.hide();
            if (item.button && typeof item.button == 'object') {
                $fx.post(item.button,
                function(json) {
                    $fx.front_panel.show_form(json, {});
                });
            }
            return false;
        });
    });
}

fx_more_menu.prototype.load_handlers = function () {
    var self = this;
    
    this.button.click( function() {
        if ( self.is_hide ) {
            self.show();
        }
        else {
            self.hide();
        }
        
        $fx.panel.trigger('fx.click', 'more_menu');
        return false;
    });
    
    $fx.panel.bind('fx.click', function(event,owner){
        if ( owner != 'more_menu') {
            self.hide();
        } 
    });
}

fx_more_menu.prototype.show = function () {
    this.is_hide = false;
    this.menu.show();
    this.prepend.css("visibility", "hidden");
    this.button.addClass('fx_admin_more_menu_button_active');
    this.after.addClass('fx_admin_more_menu_after_active');
}

fx_more_menu.prototype.hide = function () {
    this.is_hide = true;
    this.menu.hide();
    this.prepend.css("visibility", "visible");
    this.button.removeClass('fx_admin_more_menu_button_active');
    this.after.removeClass('fx_admin_more_menu_after_active');
}

