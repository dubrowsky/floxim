fx_front = function () {
    this.mode = '';
    var more_menu = new fx_more_menu($fx.settings.more_menu); 
    more_menu.load();
           
    this.mode_menu = new fx_mode_menu();
    this.mode_menu.load();
    
    this.bind_actions();
    this.move_down_body();
    
    this.mode_selectable_selector = null;
    $('html').on('keyup', '*', function(e) {
        if (e.which == 113) {
            var mode_map = {
                view:'#page.edit',
                edit:'#page.design',
                design:'#page.view'
            }
            document.location.hash = mode_map[$fx.front.mode];
        }
    });
    
    $('html').on('click', '*', function(e) {
        if ($fx.front.mode_selectable_selector == null) {
            return;
        }
        var target = $(e.target);
        if (target.closest('.fx_overlay').length > 0) {
            return;
        }
        var closest_selectable = target.closest($fx.front.mode_selectable_selector);
        if (closest_selectable.length > 0) {
            $fx.front.select_item(closest_selectable.get(0));
            return false;
        }
        var tn = target.get(0), parents = [];
        while ( tn = tn.parentNode ) {
            parents.push(tn);
        }
        $fx.front.deselect_item();
    });
}

fx_front.prototype.is_selectable = function(node) {
    return $(node).is(this.mode_selectable_selector);
}

fx_front.prototype.get_selectable_up = function() {
    var selected = this.get_selected_item();
    if (!selected) {
        return null;
    }
    var selectable_up = null
    var parents = $(selected).parents();
    for (var i = 0; i < parents.length; i++) {
        var c_parent = parents.get(i);
        if (this.is_selectable(c_parent)) {
            selectable_up = c_parent;
            break;
        }
    }
    return selectable_up;
}

fx_front.prototype.select_item = function(node) {
    var c_selected = this.get_selected_item();
    if (c_selected == node) {
        return;
    }
    this.deselect_item();
    $(node).addClass('fx_selected').trigger('fx_select');
    // при удалении выбранного узла из дерева дергаем deselect_item()
    $(node).bind('remove.deselect_removed', function(e) {
        $fx.front.deselect_item();
    });
    var selectable_up = this.get_selectable_up();
    if (selectable_up) {
        $fx.buttons.bind('select_block', $fx.front.select_level_up);
    } else {
        $fx.buttons.unbind('select_block', $fx.front.select_level_up);
    }
    $('body').on('click', this.click_out);
}

fx_front.prototype.get_selected_item = function() {
    return $('.fx_selected').get(0);
}

fx_front.prototype.deselect_item = function() {
    var selected_item = this.get_selected_item();
    if (selected_item) {
        $(selected_item).
                removeClass('fx_selected').
                trigger('fx_deselect').
                unbind('remove.deselect_removed');
    }
    $fx.buttons.unbind('select_block');
}

fx_front.prototype.select_level_up = function() {
    var item_up = $fx.front.get_selectable_up();
    if (item_up) {
        $fx.front.select_item(item_up);
    }
}
    
fx_front.prototype.bind_actions = function () {
    $fx.panel.bind('fx.dialog.ok', function(event, data) {
        if ( $fx.mode == 'page' ) {
            //window.location.reload(true);
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
    } else if (mode == 'edit') {
        this.set_mode_edit();
    } else {
        this.set_mode_design();
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
    // селектор для блоков, которые можно выбирать в режиме редактирования
    this.mode_selectable_selector = '.fx_template_var, .fx_template_var_in_att, .fx_content_essence';
    $fx.panel.one('fx.startsetmode', function() {
        $fx.front.mode_selectable_selector = null;
        $fx.front.deselect_item();
        $('html').off('.fx_edit_mode');
        $('.fx_essence_container_sortable').
            sortable('destroy').
            removeClass('fx_essence_container_sortable');
    });
    var save_var = function() {
        var var_node = $(this);
        var val = var_node.html().replace(/<br[\s\/]*?>$/, '');
        if (val == var_node.data('fx_saved_value')) {
            return;
        }
        $fx.post({
            essence:'infoblock',
            action:'save_var',
            infoblock:var_node.closest('.fx_infoblock').data('fx_infoblock'),
            'vars': [
                {'var':var_node.data('fx_var'),value:val}
            ],
            fx_admin:true
        }, function(res) {
            var_node.html(val);
            var_node.data('fx_saved_value', val);
        });
    }
    
    var edit_off = function() {
        $(this).unbind('fx_deselect');
        $(this).blur();
        $(this).removeClass('fx_var_editable');
        $(this).attr('contenteditable', 'false');
    }
    
    var restore_var = function() {
        $(this).html($(this).data('fx_saved_value'));
    }
    
    var var_select = function() {
        $(this).addClass('fx_var_editable')
            .attr('contenteditable', 'true')
            .data('fx_saved_value', $(this).html())
            .one('fx_deselect', save_var)
            .one('fx_deselect', edit_off)
            .focus();
        var var_node = $($fx.front.get_selected_item());
        var var_meta = var_node.data('fx_var');
        var ib_node = var_node.closest('.fx_infoblock');
        var infoblock_meta = ib_node.data('fx_infoblock');
        if (var_meta.var_type == 'visual') {
            $fx.buttons.bind('delete', function() {
                $fx.post({
                    essence:'infoblock',
                    action:'save_var',
                    infoblock:infoblock_meta,
                    'vars': [
                        {'var':var_meta,value:null}
                    ],
                    fx_admin:true
                }, function(res) {
                    $fx.front.reload_infoblock(ib_node.get(0));
                });
            });
        }
        return false;
    };
    
    var edit_att_vars = function() {
        var var_node = $($fx.front.get_selected_item());
        var infoblock_meta = var_node.closest('.fx_infoblock').data('fx_infoblock');
        var fields = [];
        var vars = {};
        for( var i in var_node.data()) {
            if (/^fx_template_var/.test(i)) {
                var data = var_node.data(i);
                fields.push({
                    value:data.value,
                    name:data.id,
                    label: data.title || data.name || data.id
                });
                vars[data.id] = data;
            }
        }
        $fx_dialog.open_dialog({fields:fields}, {
            onsubmit:function(e) {
                var fields_to_send = [];
                for(var var_id in vars) {
                    fields_to_send.push({
                        'var':vars[var_id],
                        'value':$('#'+var_id, this).val()
                    });
                }
                $fx.post({
                    essence:'infoblock',
                    action:'save_var',
                    infoblock:infoblock_meta,
                    'vars': fields_to_send,
                    fx_admin:true
                }, function(res) {
                    $fx.front.reload_infoblock(var_node.closest('.fx_infoblock').get(0));
                });
                return false;
            }
        });
    }
    
    $('html').on('fx_select.fx_edit_mode', '.fx_template_var', var_select);
    $('html').on('fx_deselect.fx_edit_mode', '.fx_template_var', function() {
        $fx.buttons.unbind('delete');
    });
    
    $('html').on('fx_select.fx_edit_mode', '.fx_template_var_in_att', function() {
        $fx.buttons.bind('settings', edit_att_vars);
        return false;
    });
    $('html').on('fx_deselect.fx_edit_mode', '.fx_template_var_in_att', function() {
        $fx.buttons.unbind('settings', edit_att_vars);
        return false;
    })
    
    $('html').on('fx_select.fx_edit_mode', '.fx_content_essence', function() {
        var essence_node = $(this);
        var essence_meta = $(this).data('fx_content_essence');
        var ib_node = essence_node.closest('.fx_infoblock').get(0);
        $fx.buttons.bind('edit', function() {
            $fx.post({
                essence:'content',
                action:'add_edit',
                content_type:essence_meta.type,
                content_id:essence_meta.id
             }, function(res) {
                 $fx_dialog.open_dialog(res, {
                     onfinish:function() {
                         $fx.front.reload_infoblock(ib_node);
                     }
                 });
             });
        });
        $fx.buttons.bind('delete', function() {
           if (confirm("ORLY???")) {
               $fx.post({
                   essence:'content',
                   action:'delete_save',
                   content_type:essence_meta.type,
                   content_id:essence_meta.id
               }, function () {
                   $fx.front.reload_infoblock(ib_node);
               });
           }
        });
    });
    
    $('html').on('fx_deselect.fx_edit_mode', '.fx_content_essence', function() {
        $fx.buttons.unbind('edit');
        $fx.buttons.unbind('delete');
    });
    
    
    
    $fx.buttons.bind('add', function() {
        var buttons = [];
        $('.fx_infoblock').each(function() {
            var ib_node = this;
            var cm = $(this).data('fx_controller_meta');
            
            if ( cm && cm.accept_content) {
                for (var i = 0; i < cm.accept_content.length; i++) {
                    var c_cnt = cm.accept_content[i];
                    buttons.push({
                        name:c_cnt.title,
                        callback:function() {
                            $fx.post({
                               essence:'content',
                               action:'add_edit',
                               content_type:c_cnt.type,
                               infoblock_id:c_cnt.infoblock_id,
                               parent_id:c_cnt.parent_id
                            }, function(res) {
                                $fx_dialog.open_dialog(res, {
                                    onfinish:function() {
                                        $fx.front.reload_infoblock(ib_node);
                                    }
                                });
                            });
                        }
                    });
                }
            }
        });
        $fx.buttons.show_pulldown('add', buttons);
    });
    
    var var_keydown = function(e) {
        if (e.which == 27) {
            $(this).unbind('fx_deselect', save_var);
            $fx.front.deselect_item();
            restore_var.apply(this);
            return false;
        }
        if (e.which == 13 && e.ctrlKey) {
            $fx.front.deselect_item();
            return false;
        }
    };
    $('html').on('keydown.fx_edit_mode', '.fx_var_editable', var_keydown);
    $('html').on('mouseover.fx_edit_mode', '.fx_content_essence', function() {
        var cp = $(this).parent();
        if (cp.hasClass('fx_essence_container_sortable')) {
            return;
        }
        if (cp.find('.fx_content_essence').length < 2) {
            return;
        }
        cp.addClass('fx_essence_container_sortable');
        cp.sortable({
            items:'>.fx_content_essence',
            stop:function(e) {
                var ce = $(e.srcElement);
                var ce_data = ce.data('fx_content_essence');
                
                var next_e = ce.next('.fx_content_essence');
                var next_id = null;
                if (next_e.length > 0) {
                    next_id = next_e.data('fx_content_essence').id;
                }
                
                $fx.post({
                    essence:'content',
                    action:'move',
                    content_id:ce_data.id,
                    content_type:ce_data.type,
                    next_id:next_id
                }, function(res) {
                    
                });
            }
        });
    });
},

fx_front.prototype.set_mode_design = function() {
    this.mode_selectable_selector = '.fx_infoblock, .fx_area';
    $fx.panel.one('fx.startsetmode', function() {
        $fx.front.mode_selectable_selector = null;
        $fx.front.deselect_item();
        $('html').off('.fx_design_mode');
    });
    
    $('html').on('mouseover.fx_design_mode', '.fx_infoblock', function() {
        var cp = $(this).closest('.fx_area');
        if (cp.hasClass('fx_area_sortable')) {
            return;
        }
        if (cp.find('.fx_infoblock').length < 2) {
            return;
        }
        cp.addClass('fx_area_sortable');
        cp.sortable({
            items:'>.fx_infoblock',
            connectWith:'.fx_area',
            stop:function(e, ui) {
                var ce = ui.item;
                var ce_data = ce.data('fx_infoblock');
                
                var params = {
                    essence:'infoblock',
                    action:'move',
                    area:ce.closest('.fx_area').data('fx_area').id
                }
                
                params.infoblock_id = ce_data.id;
                params.visual_id = ce_data.visual_id;
                
                var next_e = ce.next('.fx_infoblock');
                if (next_e.length > 0) {
                    var next_data = next_e.data('fx_infoblock');
                    params.next_infoblock_id = next_data.id;
                    params.next_visual_id = next_data.visual_id;
                }
                
                $fx.post(params, function(res) {
                    console.log('posted');
                });
            }
        });
    });
    
    var configure_infoblock = function() {
        var ib_node = $fx.front.get_selected_item();
        if (!ib_node) {
            return;
        }
        var ib = $(ib_node).data('fx_infoblock');
        if (!ib) {
            return;
        }
        $fx.post({
           essence:'infoblock',
           action:'select_settings',
           id:ib.id,
           visual_id:ib.visual_id,
           page_id:$('body').data('fx_page_id'),
           fx_admin:true
        }, function(json) {
            $fx_dialog.open_dialog(json);
        });
    }
    
    var add_infoblock = function() {
        var area = $(this).data('fx_area');
        $fx.post({
            essence:'infoblock',
            action:'select_controller',
            page_id:$('body').data('fx_page_id'),
            area:area,
            fx_admin:true
        }, function(json) {
            $fx_dialog.open_dialog(json);
        });
    }
    
    var delete_infoblock = function() {
        var ib_node = $fx.front.get_selected_item();
        if (!ib_node) {
            return;
        }
        var ib = $(ib_node).data('fx_infoblock');
        if (!ib) {
            return;
        }
        $fx.post({
           essence:'infoblock',
           action:'delete_infoblock',
           id:ib.id,
           fx_admin:true
        }, function(json) {
            $fx_dialog.open_dialog(json);
        });
    }
    
    $('html').on('fx_select.fx_design_mode', '.fx_infoblock', function() {
        $fx.buttons.bind('settings', configure_infoblock);
        $fx.buttons.bind('delete', delete_infoblock);
    });
    $('html').on('fx_deselect.fx_design_mode', '.fx_infoblock', function() {
        $fx.buttons.unbind('settings');
        $fx.buttons.unbind('delete');
    });
    $('html').on('click.fx_design_mode', '.fx_infoblock_adder', add_infoblock);
}

fx_front.prototype.reload_infoblock = function(infoblock_node) {
    var ib_parent = $(infoblock_node).parent();
    var meta = $(infoblock_node).data('fx_infoblock');
    var page_id = $('body').data('fx_page_id');
    var selected = $(infoblock_node).descendant_or_self('.fx_selected');
    var selected_selector = null;
    if(selected.length > 0) {
        selected_selector = selected.first().generate_selector(ib_parent);
    }
    $.ajax({
       url:'/~ib/'+meta.id+'@'+page_id,
       success:function(res) {
           $(infoblock_node).hide().before(res);
           $(infoblock_node).remove();
           if (selected_selector) {
               $fx.front.select_item(ib_parent.find(selected_selector).get(0));
           }
       }
    });
}

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