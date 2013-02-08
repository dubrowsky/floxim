fx_submenu = function ( ) {
    this.container = $('#fx_admin_submenu');
    this.type = '';
}

fx_submenu.prototype.load = function ( data ) {
    if ( !data ) {
        return;
    }
 
    if ( !data.not_update ) {
        this.show_items(data);
    }
    this.set_active(data.active, data.subactive);
    
}

fx_submenu.prototype.show_items = function ( data ) {
    var self = this;
    var href,cont = this.container;
    
    this.container.html('');
    
    if ( data.error ) {
        this.container.append( $('<b/>').text(data.error) ); 
    }
    
    this.type = data.type;
    if ( data.type == 'full' ) {
        if ( data.backlink ) {
            this.container.append( $('<a/>').addClass('fx_admin_submenu_backlink').attr('href', '#admin.'+data.backlink)); 
        }
        if ( data.title ) {
            this.container.append( $('<h2/>').text(data.title) ); 
        }
        
        cont = $('<div />').addClass('fx_admin_submenu_items_full').appendTo(this.container);
        var free_children = {};
        if ( data.items ) {
            $.each ( data.items, function(k, item){
                href = '#admin.'+item.href;
                var item_link = $("<a/>").attr('href', href).attr('id', 'fx_admin_submenu_'+item.id).data('id', item.id).text(item.name);
                if (item.parent) {
                	item_link.addClass('fx_admin_submenu_child');
                	var parent_node = $('#fx_admin_submenu_'+item.parent);
                	if (parent_node.length > 0) {
                		parent_node.addClass('fx_admin_submenu_parent').after(item_link);
                	} else {
                		if (!free_children[item.parent]) {
                			free_children[item.parent] = [];
                		}
                		free_children[item.parent].push(item_link);
                	}
                } else {
                	item_link.appendTo(cont);
                	if (free_children[item.id]) {
                		$.each(free_children[item.id], function(chk, child) {
                			item_link.after(child);	
                		});
                	}
                }
            });
        }
        
    }
    else {
        if ( data.items ) {
            $.each ( data.items, function(k, item){
                cont = self.get_container(item.parent);
                href = '#admin.'+item.href;
                $("<a/>").attr('href', href).data('id', item.id).text(item.name).appendTo(cont);
            });
        }

    }
}

fx_submenu.prototype.get_container = function( parent_id ) {
    var cont, id = '__fx__'+parent_id;
    if (!parent_id ) {
        if ( $('.fx_admin_submenu_level0').length > 0 ) {
            cont = $('.fx_admin_submenu_level0');
        }
        else {
            cont = $('<div />').addClass('fx_admin_submenu_level0').appendTo(this.container);
        }
    }
    else {
        if ( $("#"+id).length > 0 ) {
            cont = $("#"+id);
        }
        else {
            cont = $('<div id="'+id+'" />').addClass('fx_admin_submenu_level1').appendTo(this.container);
        }
    }
    
    return cont;
}

fx_submenu.prototype.set_active = function( active, subactive ) {
    $('.fx_admin_submenu_active').removeClass('fx_admin_submenu_active');
    $('a', $('.fx_admin_submenu_level0')).each(function(){
        if ( $(this).data('id') == active ) {
            $(this).addClass('fx_admin_submenu_active');
        } 
    });
    
    $('a', $('.fx_admin_submenu_level1')).each(function(){
        if ( $(this).data('id') == subactive ) {
            $(this).addClass('fx_admin_submenu_active');
        } 
    });
    if ( this.type == 'full' ) {
        $('a', this.container).each(function(){
            if ( $(this).data('id') == subactive ) {
                $(this).addClass('fx_admin_submenu_active');
            } 
        });
    }
    
    $('.fx_admin_submenu_level1', this.container).hide();
    if ( active ) {
        $('#__fx__'+active).show();
    } 
    
}
