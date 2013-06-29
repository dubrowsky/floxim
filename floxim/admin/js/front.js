fx_front = function () {
    this.mode = '';
    var more_menu = new fx_more_menu($fx.settings.more_menu); 
    more_menu.load();
           
    this.mode_menu = new fx_mode_menu();
    this.mode_menu.load();
    
    //this.bind_actions();
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
    
    $('html').on('click', function(e) {
        if ($fx.front.mode == 'view') {
            return;
        }
        var target = $(e.target);
        if (target.closest('.fx_overlay, #redactor_modal').length > 0) {
            return;
        }
        var closest_selectable = null;
        if ($fx.front.is_selectable(target)) {
            closest_selectable = target;
        } else {
        	closest_selectable = $fx.front.get_selectable_up(target);
        }
        
        // нечего выбирать
        if (!closest_selectable) {
        	// случаи, когда target оказался вне основого дерева
        	// как с jqueryui-datepicker при перерисовке
        	if (target.closest('html').length == 0) {
        		return;
			}
			// снимаем выделение и заканчиваем обработку
			$fx.front.deselect_item();
        	return;
        }
        
		// перемещение между страницами по ссылкам с зажатым контролом,
		// и даже сохраняет текущий режим
		var clicked_link = target.closest('a');
		if (clicked_link.length > 0 && e.ctrlKey && clicked_link.attr('href')) {
			document.location.href = clicked_link.attr('href')+document.location.hash;
			return false;
		}
        
		// отлавливаем только contenteditable
		if ($(closest_selectable).hasClass('fx_selected')) {
            if ($(closest_selectable).attr('contenteditable') == 'true') {
                return;
            }
            return false;
		}
		$fx.front.select_item(closest_selectable);
		return false;
        
    });
    
    $('html').on('fx_select', function(e) {
		var n = $(e.target);
		if (n.is('.fx_content_essence')) {
			$fx.front.select_content_essence(n);
            var tvs = $('.fx_template_var', n);
            if (tvs.length == 1) {
                tvs.edit_in_place();
            }
		}
		if (n.is('.fx_template_var, .fx_template_var_in_att')) {
			n.edit_in_place();
		}
		if (n.is('.fx_infoblock')) {
			$fx.front.select_infoblock(n);
		}
		$fx.front.redraw_add_button(n, $fx.front.mode);
    	return false;
    });
}

fx_front.prototype.redraw_add_button = function(node, mode) {
	$fx.buttons.unbind('add');
	var buttons = [];
	if (!node) {
		return;
	}
	
	var ib = node.closest('.fx_infoblock_'+mode);
	var cm = ib.data('fx_controller_meta');
	if (cm && cm.accept_content) {
		for (var i = 0; i < cm.accept_content.length; i++) {
			var c_cnt = cm.accept_content[i];
			var cb_closure = (function(c_cnt) {
				return function() {
					$fx.post({
					   essence:'content',
					   action:'add_edit',
					   content_type:c_cnt.type,
					   infoblock_id:c_cnt.infoblock_id,
					   parent_id:c_cnt.parent_id
					}, function(res) {
						$fx_dialog.open_dialog(res, {
							onfinish:function() {
								$fx.front.reload_infoblock(ib);
							}
						});
					});
				}
			})(c_cnt);
			buttons.push({
				name:c_cnt.title,
				callback:cb_closure
			});
        }
	}
	var area_meta = node.closest('.fx_area').data('fx_area');
	if (area_meta) {
		buttons.push({
			name:'Add new infoblock to '+area_meta.id,
			callback: function() {
				$fx.post({
					essence:'infoblock',
					action:'select_controller',
					page_id:$('body').data('fx_page_id'),
					area:area_meta.id,
					fx_admin:true
				}, function(json) {
					$fx_dialog.open_dialog(json);
				});
			}
		});
	}
	if (buttons.length > 0) {
		$fx.buttons.bind('add', function() {
			console.log('btnz', buttons);
			$fx.buttons.show_pulldown('add', buttons);
			return false;
		});
		$('html').one('fx_deselect', function() {
			$fx.front.redraw_add_button();
		});
	}
	
	/*
	$fx.buttons.bind('add', function() {
        var buttons = [];
        $('.fx_infoblock').each(function() {
            var ib_node = this;
            var cm = $(this).data('fx_controller_meta');
            
            if ( cm && cm.accept_content) {
                for (var i = 0; i < cm.accept_content.length; i++) {
                    var c_cnt = cm.accept_content[i];
                    var cb_closure = (function(c_cnt) {
                        return function() {
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
                    })(c_cnt);
                    buttons.push({
                        name:c_cnt.title,
                        callback:cb_closure
                    });
                }
            }
        });
        $fx.buttons.show_pulldown('add', buttons);
    });
    */
}

fx_front.prototype.is_selectable = function(node) {
	var n = $(node);
	if (n.hasClass('fx_unselectable')) {
		return false;
	}
	if (n.hasClass('fx_area')) {
		return true;
	}
	if (!n.is('.fx_template_var, .fx_template_var_in_att, .fx_content_essence, .fx_infoblock')) {
		return false;
	}
	var ib_node = n.closest('.fx_infoblock');
	if (!ib_node.hasClass('fx_infoblock_'+$fx.front.mode)) {
		return false;
	}
    if (n.is('.fx_template_var') && !n.is('.fx_content_essence') ) {
        var ne = n.closest('.fx_content_essence');
        if (ne && $('.fx_template_var', ne).length == 1) {
            return false;
        }
    }
	return true;
}

fx_front.prototype.get_selectable_up = function(rel_node) {
    if (!rel_node) {
        rel_node = this.get_selected_item();
    }
    if (!rel_node) {
        return null;
    }
    var selectable_up = null
    var parents = $(rel_node).parents();
    for (var i = 0; i < parents.length; i++) {
        var c_parent = parents.get(i);
        if (this.is_selectable(c_parent)) {
            selectable_up = c_parent;
            break;
        }
    }
    return selectable_up;
}

fx_front.prototype.fix = function() {
    $('body').css('opacity', '0.99');
    setTimeout(function(){$('body').css('opacity', 1);}, 5);
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
    $fx.front.fix();
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
        // chrome outline bug
        $fx.front.fix();
    }
    $fx.buttons.unbind('select_block');
}

fx_front.prototype.select_level_up = function() {
    var item_up = $fx.front.get_selectable_up();
    if (item_up) {
        $fx.front.select_item(item_up);
    }
}


fx_front.prototype.hilight = function() {
	var items = $('.fx_template_var, .fx_area, .fx_template_var_in_att, .fx_content_essence, .fx_infoblock').not('.fx_unselectable');
	items.removeClass('fx_hilight');
	if ($fx.front.mode == 'view') {
		return;
	}
	items.each(function(index, item) {
		if ($fx.front.is_selectable(item)) {
			$(item).addClass('fx_hilight');
		}
	});
}

fx_front.prototype.load = function ( mode ) {
    this.mode = mode;
    
    $fx.front.deselect_item();
    
    $fx.front.hilight();
    
    $fx.buttons.draw_buttons($fx.buttons_map.page);
    
    $fx.main_menu.set_active_item('site');
        
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

fx_front.prototype.select_content_essence = function(n) {
	var essence_meta = n.data('fx_content_essence');
	var ib_node = n.closest('.fx_infoblock').get(0);
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
	$('html').one('fx_deselect', function() {
		$fx.buttons.unbind('edit');
		$fx.buttons.unbind('delete');
	});
}

fx_front.prototype.select_infoblock = function(n) {
	$fx.buttons.bind('settings', function() {
        var ib_node = n;
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
            $fx_dialog.open_dialog(
            	json, 
            	{
            		onfinish:function() {
					 $fx.front.reload_infoblock(ib_node);
					}
				}
			);
        });
    });
    $('html').one('fx_deselect', function() {
    	$fx.buttons.unbind('settings');	
    });
}

fx_front.prototype.set_mode_view = function () {
    
}

fx_front.prototype.set_mode_edit = function () {
    $fx.panel.one('fx.startsetmode', function() {
        $('html').off('.fx_edit_mode');
        //stop_essences_sortable();
    });
    
    /*
    var edit_off = function() {
        $(this).unbind('fx_deselect');
        $(this).blur();
        $(this).removeClass('fx_var_editable');
        $(this).attr('contenteditable', 'false');
        //start_essences_sortable();
    }
    
    var restore_var = function() {
        $(this).html($(this).data('fx_saved_value'));
    }
    
    var edit_att_vars = function() {
        var var_node = $($fx.front.get_selected_item());
        var infoblock_meta = var_node.closest('.fx_infoblock').data('fx_infoblock');
        var fields = [];
        var vars = {};
        for( var i in var_node.data()) {
            if (/^fx_template_var/.test(i)) {
                var data = var_node.data(i);
                var field_props = {
                    name:data.id,
                    label: data.title || data.name || data.id,
                    type: data.type || 'string'
                };
                if (data.type == 'image') {
                    field_props.value = data.filetable_id;
                    field_props.path = data.value;
                } else {
                    field_props.value = data.value;
                }
                fields.push(field_props);
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
                    $fx_dialog.close();
                });
                return false;
            }
        });
    }
    */
    
    /*$('html').on('fx_select.fx_edit_mode', '.fx_template_var', var_select);
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
        return false;
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
                    var cb_closure = (function(c_cnt) {
                        return function() {
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
                    })(c_cnt);
                    buttons.push({
                        name:c_cnt.title,
                        callback:cb_closure
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
     
    function start_essences_sortable() {
        $('.fx_content_essence').each(function() {
            var cp = $(this).parent();
            if (cp.hasClass('fx_essence_container_sortable') || cp.hasClass('fx_not_sortable')) {
                return;
            }
            if (cp.find('.fx_content_essence').length < 2) {
                return;
            }
            cp.addClass('fx_essence_container_sortable');
            cp.sortable({
                items:'>.fx_content_essence',
                stop:function(e) {
                    var ce = $(e.srcElement).closest('.fx_content_essence');
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
                    $fx.front.fix();
                }
            });
        });
    }
    start_essences_sortable();
    function stop_essences_sortable() {
        $('.fx_content_essence').each(function() {
            var cp = $(this).parent();
            if (!cp.hasClass('fx_essence_container_sortable')) {
                return;
            }
            cp.removeClass('fx_essence_container_sortable');
            cp.sortable('destroy');
        });
    }
    */
},

fx_front.prototype.set_mode_design = function() {
	$fx.panel.one('fx.startsetmode', function() {
        $('html').off('.fx_design_mode');
        $('.fx_area_sortable').
            sortable('destroy').
            removeClass('fx_area_sortable');
    });
    
    function start_areas_sortable() {
        $('.fx_area').each(function(){ 
            var cp = $(this);
            if (cp.hasClass('fx_area_sortable')) {
                return;
            }
            cp.addClass('fx_area_sortable');
            cp.sortable({
                items:'>.fx_infoblock',
                connectWith:'.fx_area',
                start:function(e, ui) {
                    $('.fx_area').addClass('fx_area_target');
                    cp.sortable('refreshPositions');
                },
                stop:function(e, ui) {
                    $('.fx_area').removeClass('fx_area_target');
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
                        
                    });
                    $fx.front.fix();
                }
            });
        });
    }
    start_areas_sortable();
    
    var add_infoblock = function() {
        var area = $(this).closest('.fx_area').data('fx_area');
        $fx.post({
            essence:'infoblock',
            action:'select_controller',
            page_id:$('body').data('fx_page_id'),
            area:area.id,
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
    
    /*
    $('html').on('fx_select.fx_design_mode', '.fx_infoblock', function(e) {
        $fx.buttons.bind('settings', configure_infoblock);
        $fx.buttons.bind('delete', delete_infoblock);
        return false;
    });
    
    $('html').on('fx_select.fx_design_mode', '.fx_area', function() {
        return false;
    });
    
    $('html').on('fx_deselect.fx_design_mode', '.fx_infoblock', function() {
        $fx.buttons.unbind('settings');
        $fx.buttons.unbind('delete');
        return false;
    });
    $('html').on('click.fx_design_mode', '.fx_infoblock_adder', add_infoblock);
    */
}

fx_front.prototype.reload_infoblock = function(infoblock_node) {
    var ib_parent = $(infoblock_node).parent();
    var meta = $(infoblock_node).data('fx_infoblock');
    var page_id = $('body').data('fx_page_id');
    $.ajax({
       url:'/~ib/'+meta.id+'@'+page_id,
       success:function(res) {
           var selected = $(infoblock_node).descendant_or_self('.fx_selected');
           var selected_selector = null;
           if(selected.length > 0) {
                selected_selector = selected.first().generate_selector(ib_parent);
           }

           if (infoblock_node.nodeName == 'BODY') {
               var inserted = false;
               $(infoblock_node).children().each(function() {
                   if(!$(this).hasClass('fx_overlay')) {
                       if (!inserted) {
                            $(this).before(res);
                            inserted = true;
                       }
                       $(this).remove();
                   }
               });
           } else {
               $(infoblock_node).hide().before(res);
               $(infoblock_node).remove();
           }
           if (selected_selector) {
               $fx.front.select_item(ib_parent.find(selected_selector).get(0));
           }
           $fx.front.hilight();
       }
    });
}

fx_front.prototype.move_down_body =function () {
    $("body").css('margin-top','74px'); //74 - высота панели
}

fx_front.prototype.add_panel_field = function(field) {
	var field_node = $fx_form.draw_field(field);
	$('#fx_admin_fields').append(field_node);
	return field_node;
}