fx_buttons = function ( source ) {
    this.source = source;
    this.container = $('#fx_admin_buttons');
    
    this.pulldown = {};
    this.pulldown_is_hide = true;
    this.buttons_action = {};
        
    var self = this;
    $fx.panel.bind('fx.admin_load_page', function(event, data){
        self.pulldown = {};
        self.buttons_action = {};
        if ( data.buttons_pulldown ) {
            self.pulldown = data.buttons_pulldown;
        }
        if ( data.buttons_action ) {
            self.buttons_action = data.buttons_action;
        }
    });
    
    $fx.panel.bind('fx.click', function(event,owner){
		if ( owner != 'button_pulldown') {
            self.hide_pulldown();
        } 
    });
    
}

fx_buttons.prototype.bind = function(button_key, callback) {
    var b = $('.fx_admin_button_'+button_key);
    b.show();
    b.data('has_callback', true);
    b.click(function() {
    		callback();
    		return false;
    });
}

fx_buttons.prototype.unbind = function(button_key, callback) {
    var b = $('.fx_admin_button_'+button_key);
    b.data('has_callback', null);
    b.hide();
    b.unbind('click', callback);
}


fx_buttons.prototype.draw_buttons = function ( buttons ) {
    var element,self = this;
    this.container.html('');

    if ( buttons === undefined ) {
        return false;
    }
  
    $.each(buttons, function(key, button) {
        if ( button == 'divider' ) {
            var div = $('<div/>').addClass('nc_adminpanel_button_divider');
            div.data('key', button);
        }
        else {
            var button_source = self.source[button];
            element = $('<div/>').addClass('fx_admin_button_'+button);
            element.attr('title', button_source.title);
            if ( button_source.type && button_source.type == 'text' ) {
                element.addClass('fx_admin_button_text').html( $('<span>').text(button_source.title) );
            }
            else {
                element.addClass('fx_admin_button');
            }

            element.data(button_source).data('key', button);
            element.click( function () {
				if ($(this).data('has_callback')) {
					return;
				}
                self.handle(button);
                return false;
            });
			element.hide();
        }
                
        self.container.append(element);
    });
            
}

fx_buttons.prototype.set_active_buttons = function ( buttons ) {
    $('div', this.container).each(function() {
        if ( $(this).data('key') == 'more' || $(this).data('key') == 'divider'  ) return true;
        if ($.inArray($(this).data('key'), buttons) == -1 ) {
            $(this).hide();
        } else {
        	$(this).show();
        }
    });
}

fx_buttons.prototype.update_button = function ( btn, settings) {
    var button = $(".fx_admin_button_" + btn);
    if ( settings.title !== undefined ) {
        button.attr('title',settings.title );
    }
            
    if ( settings.available !== undefined ) {
        if ( settings.available ) {
            button.show();
        }
        else {
        	button.hide();
        }
    }
}

fx_buttons.prototype.show_panel = function () {
    this.container.show();
}

fx_buttons.prototype.hide_panel = function () {
    this.container.hide();
}

/**
 * Первичная обработка нажатия, например, показ всплывающего меню
 * сама обработка нажатия - в другом месте
 */
fx_buttons.prototype.handle = function ( button ) {
    if ( this.pulldown[button] ) {
        if ( this.pulldown_is_hide ) {
            this.show_pulldown(button, this.pulldown[button]);
        }
        else {
        	this.hide_pulldown();
        }

        $fx.panel.trigger('fx.click', 'button_pulldown');
        return false;    
    }
    
    var button_action = this.buttons_action[button];
    if ( button_action ) {
        if ( button_action.url ) {
            window.location = button_action.url;
            return false;
        }
        if ( button_action.location ) {
            var location = '#'+$fx.hash.slice(0,2).join('.')+'.'+button_action.location;
            window.location.hash = location;
            return false;
        }
        if (button_action.options) {
            $fx.post(
                button_action.options, 
                function(json) {
                    $fx_dialog.open_dialog(json, {onfinish:function() {
                        $(window).hashchange();
                    }});
                }
            );
            return false;
        }
    }
    if (button == 'delete'){
        var opts = {
            essence:$fx.admin.essence,
            action:'delete',
            id:$('.fx_admin_selected', '#fx_admin_content').data('id'),
            posting:true
        };
        $fx.post(
            opts, 
            function(json) {
                $(window).hashchange();
            }
        );
        return false;
    }
}

fx_buttons.prototype.show_pulldown = function ( button, data ) {
    var container = $('<div class="fx_admin_pull_down_menu"/>');
    var item;
    $.each( data, function (i,v) {
        if ( v === 'divider') {
            item = $('<span>').addClass('fx_admin_pull_down_divider');
        }
        else {
            if (!v.callback) {
                v.callback = function() {
                    $fx.post(
                        $.extend({
                            essence:$fx.admin.essence,
                            action:button
                        }, v.options), 
                        function(json) {
                            $fx_dialog.open_dialog(json, {onfinish:function() {
                                $(window).hashchange();
                            }});
                        }
                    );
                }
            }
        	var link_name = v.name || '[NoName]';
            item = $('<span/>').html(link_name).click(v.callback);
        }
        container.append(item);
    });
    
    var pos = $('.fx_admin_button_'+button).offset();
    pos.top -= $(document).scrollTop();
    container.css('left', pos.left).css('top', pos.top+25).appendTo($('#fx_admin_control'));

}

fx_buttons.prototype.hide_pulldown = function () {
	$('.fx_admin_pull_down_menu').remove();
}
