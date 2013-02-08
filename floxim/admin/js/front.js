fx_front = function () {
    this.mode = '';
    var more_menu = new fx_more_menu($fx.settings.more_menu); 
    more_menu.load();
           
    this.mode_menu = new fx_mode_menu();
    this.mode_menu.load();
    
    this.bind_actions();
    this.move_down_body();
    
}
    
fx_front.prototype.bind_actions = function () {
    $fx.panel.bind('fx.dialog.ok', function(event, data) {
        if ( $fx.mode == 'page' ) {
            window.locаation.reload(true);
        }
    });
}

fx_front.prototype.load = function ( mode ) {
    this.mode = mode;
    $fx.buttons.draw_buttons($fx.buttons_map.page[mode]);
    
    $fx.main_menu.set_active_item('site');
    
    var link;
    $('a').each( function(){
        link = $(this);
        if (  link.data('fx_href') ) {
            link.attr('href', link.data('fx_href') );
        }
    });
        
    if (mode == 'view') {
        this.set_mode_view();
    } else {
        this.reload();
        this.set_mode_edit();
    }
        
    $fx.update_history();
      
    this.mode_menu.set_active(this.mode);
            
    if ( $fx.settings.additional_text ) {
        $fx.draw_additional_text( $fx.settings.additional_text );
    }
    
    if ($fx.settings.additional_panel) {
    	$fx.draw_additional_panel($fx.settings.additional_panel);
    }
}

fx_front.prototype.set_mode_view = function () {
    $('.fx_admin_blockedit').removeClass('fx_admin_blockedit');
    $('.fx_admin_unchecked').hide();
    $('.fx_admin_selected').removeClass('fx_admin_selected');
}
    
fx_front.prototype.set_mode_edit = function () {
    var save_var = function() {
        var meta = $(this).data('fx_var');
        var ib = $(this).closest('.fx_infoblock').data('fx_infoblock');
        $(this).data('fx_saved_value', $(this).html());
        console.log('saving ', meta, ib);
        $fx.post({
            essence:'infoblock',
            action:'save_var',
            infoblock:ib,
            'var':meta,
            value:$(this).html(),
            fx_admin:true
        }, function(res) {
            //alert('res:'+res);
        });
        
        edit_off.apply(this);
    }
    
    var edit_off = function() {
        $(this).unbind('blur');
        $(this).blur();
        $(this).removeClass('fx_var_editable');
        $(this).attr('contenteditable', 'false');
    }
    
    var restore_var = function() {
        $(this).html($(this).data('fx_saved_value'));
    }
    
    $('body').on('click', '.fx_template_var', function() {
        $(this).addClass('fx_var_editable');
        $(this).attr('contenteditable', 'true');
        $(this).data('fx_saved_value', $(this).html());
        //var meta = $(this).data('fx_var');
        $(this).blur(save_var);
        $(this).keydown(function(e) {
            if (e.which == 27) {
                restore_var.apply(this);
                edit_off.apply(this);
                return false;
            }
            if (e.which == 13 && e.ctrlKey) {
                save_var.apply(this);
                return false;
            }
        });
        return false;
    });
    
    $('.fx_admin_blockedit').removeClass('fx_admin_blockedit');
    $('.fx_admin_selected').removeClass('fx_admin_selected');
      
    $.each($fx.g_data.infoblock , function(key,infoblock){
        $('#fx_design_' + key).data('key', key);
    });

    this.blocks = [];
    this.load_items('block');
    this.bind_blocks();
    
    
    this.fields = [];
    this.load_items('field');   
    this.bind_fields();
    
    $('.fx_admin_unchecked').hide();
    $('.fx_admin_unchecked_'+this.mode).show();
    
    $fx.front.update_view();       
    var link;
    $('a', '.fx_admin_blockedit').each( function(){
        link = $(this);
        if ( link.attr('href') ) {
            link.data('fx_href', link.attr('href')).removeAttr('href');
        }
    });
            
    $('.fx_admin_blockedit').closest('a').each( function(){
        link = $(this);
        if ( link.attr('href') ) {
            link.data('fx_href', link.attr('href')).removeAttr('href');
        }
    });
},

fx_front.prototype.reload =  function( forcibly ) {
    var self = this;
    if (this.reloaded && !forcibly) {
        return false;
    }
            
    var action = $("body").data('action') != undefined ? $("body").data('action') : 'index';
    var message_id = $("body").data('message_id') != undefined ? $("body").data('message_id') : 0;
    $.ajax({
        url: $fx.settings.action_link,
        data: {
            'action':'index', 
            'essence':'infoblock', 
            'sub' : $("body").data('sub'), 
            'url': $("body").data('url'),
            'infoblocks': $fx.g_data.infoblock, 
            'show_method':action, 
            'message_id':message_id, 
            'url': $fx.g_data.main.url,
            'block_number' : $("body").data("block_number"),
            'field_number' : $("body").data("field_number")
        },
        dataType: 'json',
        async: false,
        type: 'POST',
        success: function(data) {
            $.each(data, function(key, val){
                if ( key == 'nc_scripts') {
                    $("body").append("<script>" + val + "</script>");
                }
                else {
                    $('#fx_design_'+key).html(val);
                }
            });
            self.reloaded = true;
        }
    });
},
    
fx_front.prototype.load_items = function (type) {
    var key, item, mode;
    var re = type == 'block' ? /fx_page_block_(\d*)/ : /fx_page_field_(\d*)/;
    var self = this;
    
    var items = $('.fx_page_'+type);
    items.each ( function() {
        item = $(this);
        item.unbind('click');
        
        key = $fx.regexp(re, item.attr('class'))[1];
        item.data('key', key).data('type', type);
                    
        mode = $fx.g_data[type+'s'][key].mode || 'edit';
        if ( $fx.hash[1] == mode ) {
            self[type + 's'].push(item);
        }
        
        if ( type == 'block' && !$fx.g_data.blocks[key].checked ) {
            item.addClass('fx_admin_unchecked fx_admin_unchecked_'+mode);
        }
    });   
    
    self[type + 's'] = $(self[type + 's']);
}

/**
 * По существу, функция проставляет fx_admin_blockedit
 * Такие блоки будут выделены и на них можно нажать
 */
fx_front.prototype.update_view = function () { 
    var selected_blocks = $('.fx_admin_selected');
    var count = selected_blocks.length;
    $('.fx_admin_blockedit').removeClass('fx_admin_blockedit');
    
    var parent_block = false;
    if ( count == 1 ) {
        parent_block = 'fx_page_block_' + selected_blocks.data('key');
    }
            
    this.blocks.each ( function() {
    	var block_key = this.data('key');
        var block = $fx.g_data.blocks[ block_key ];
        if ( block === undefined ) {;
        	return true;
        }
        var show_blockedit = true;
        if (  block.hidden ) {
            show_blockedit = false;
        } 
        if (  block.parent ) {
            var cur_parent_block = $('.'+ block.parent ).data('key');
            var cur_parent_block_hidden = $fx.g_data.blocks[cur_parent_block].hidden;
            var selected_parent = (parent_block && block.parent == parent_block); 
            if ( cur_parent_block_hidden === undefined ) {
                show_blockedit = false;
            }  
            if ( selected_parent ) {
                show_blockedit = true;
            }
        }
                
        if ( show_blockedit ) {
            this.addClass('fx_admin_blockedit');
        }
        else {
            this.removeClass('fx_admin_blockedit');
        }    
    });
    
    this.fields.each( function() {
        if ( $fx.g_data.fields[ this.data('key') ] === undefined )  return true;
                    
                
        var parent = $fx.g_data.fields[ this.data('key') ].parent;
        if (  parent === undefined || parent_block == parent ) {
            this.addClass('fx_admin_blockedit');
        }
        else {
            this.removeClass('fx_admin_blockedit');
        }
    });
    
    this.update_available_buttons(selected_blocks);
    
    $fx.sort.update($fx.hash[1]);          
}

fx_front.prototype.update_available_buttons = function ( selected_blocks ) {
    var buttons_cfg = {};
    buttons_cfg.view = {
        'none': []
    };
    buttons_cfg.edit = {
        'none': [], 
        'one': ['edit',  'on', 'off', 'delete', 'select_block', 'settings'], 
        'many':['on', 'off', 'delete']
    };
    buttons_cfg.design = buttons_cfg.edit;
    var count = selected_blocks.length;
    var key = count ? ( count == 1  ? 'one' : 'many') : 'none';
    var buttons = buttons_cfg[ $fx.hash[1] ][key];
    $.each ( selected_blocks , function (k,v){
        var data = $fx.g_data.blocks[ $(v).data('key') ];
        if (data) {
			buttons = array_intersect(buttons, data.buttons);
        }
    });
    
    if ( count <= 1 && this.show_add_button(selected_blocks) ) {
        buttons.push('add');
    }
    
    $fx.buttons.set_active_buttons(buttons);
}

fx_front.prototype.bind_blocks = function () {
    var self = this;
    
    $fx.panel.bind('fx.click', function(event, owner){
        if ( owner != 'block' ) {
            var selected = $('.fx_admin_selected');
            if ( selected.length ) {
				selected.each(function() {
					if ($(this).parents('.ui-dialog').length == 0) {
						$(this).removeClass('fx_admin_selected');
					}
				});
                self.update_view();
            }
        }
    });
    
    this.blocks.each ( function() { 
        this.click (function (e) { 
            var target = $(this);
            if ( !target.hasClass('fx_admin_blockedit')) return true;
                    
            if ( !e.ctrlKey && !e.metaKey) {
                $('.fx_admin_selected').removeClass('fx_admin_selected');
            }
			target.toggleClass('fx_admin_selected');
			
            self.update_view();
            
            $fx.panel.trigger('fx.click', 'block');
            return false;
        });
        
    });    
}

fx_front.prototype.bind_fields = function () {
    this.fields.each ( function () {
        this.click ( function (e){
            var target = $(this);
            if ( !target.hasClass('fx_admin_blockedit')) return true;
            if ( target.hasClass('nc_editor')) return false;
            var parent_data = false;
            var selected = $fx.g_data.fields[target.data('key')];
            if ( selected.parent !== undefined ) {
                var parent_key = $('.'+selected.parent).data('key');
                parent_data = $fx.g_data.blocks[parent_key].post;
            }
          
            $fx.panel.trigger('fx.click', 'field');
            
            target.addClass('nc_editor');
            $fx.buttons.hide_panel();
            target.edit_in_place(selected, parent_data, function(){
                $('.nc_editor').removeClass('nc_editor');
                $('.fx_admin_selected').removeClass('fx_admin_selected');
                $fx.buttons.show_panel();
                $fx.front.update_view();
            }); 

            return false;
        }); 
    });
}

fx_front.prototype.move_down_body =function () {
    $("body").css('margin-top','74px'); //74 - высота панели
}

fx_front.prototype.show_add_button = function (selected) {
    var mode = $fx.hash[1];
    var menu = [];
    var selected_block = false;
    
    if ( mode == 'design' ) {
        return true;
    }
            
    if ( selected.length == 1 ) {
        selected_block = $fx.g_data.blocks[selected.data('key')];
    }
    if ( $fx.g_data.addition_block ) {
        $.each ( $fx.g_data.addition_block, function (k, block) {
            var push = false;
            block.post.subdivision_id = $('body').data('sub'); 

            if ( !block.mode || block.mode != mode ) {
                return true;
            } 

            if ( selected.length ) {
                if ( block.decent_parent && selected.hasClass(block.decent_parent) ) {
                    push = true;
                }
                    
                if ( block.parent_key && selected.hasClass(block.parent_key) ) {
                    push = true;
                }
                        
                if ( block.key && selected_block && selected_block.parent == block.key ) {
                    push = true;
                }
            }
            else {
                if ( !block.parent_key ) {
                    push = true;
                }
            }
                
            if ( push ) {
                menu.push({
                    name:block.name, 
                    options: block.post
                });
            }

        });
            

            
    }
    
    
    if ( menu.length  ) {
        $fx.buttons.pulldown.add = menu;
        return true;
    }
    
    return false;
}