fx_front = function () {
    this.mode = '';
    var more_menu = new fx_more_menu($fx.settings.more_menu); 
    more_menu.load();
           
    this.mode_menu = new fx_mode_menu();
    this.mode_menu.load();
    
    this.move_down_body();
    
    this.mode_selectable_selector = null;
    $('html').on('keyup', '*', function(e) {
        if (e.which == 113) {
            if ($fx.front.get_selected_item()) {
                if ($fx.buttons.is_active('edit')) {
                    $fx.buttons.trigger('edit');
                    return false;
                }
            }
            var mode_map = {
                view:'#page.edit',
                edit:'#page.design',
                design:'#page.view'
            }
            document.location.hash = mode_map[$fx.front.mode];
        }
    });
    
    var c_hover = null;
    $('html').on('mouseover', '.fx_hilight', function() {
        c_hover = this;
        var node = $(this);
        setTimeout(
            function() {
                if (c_hover != node.get(0)) {
                    return;
                }
                $('.fx_hilight_hover').removeClass('fx_hilight_hover');
                node.addClass('fx_hilight_hover');
            }, 
            c_hover ? 100 : 10
        );
        node.one('mouseout', function() {
            c_hover = null;
            setTimeout(
                function() {
                    if (c_hover != node.get(0)) {
                        node.removeClass('fx_hilight_hover');
                    }
                },
                100
            );
        });
        return false;
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
            var tvs = $('.fx_template_var, .fx_template_var_in_att', n);
            if (tvs.length == 1) {
                tvs.edit_in_place();
            }
		}
		if (n.is('.fx_template_var, .fx_template_var_in_att')) {
			n.edit_in_place();
            var container_essence = n.closest('.fx_content_essence');
            if (container_essence.get(0) !== n.get(0)) {
                container_essence.trigger('fx_select');
            }
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
    var adders = [];
	var cm = ib.data('fx_controller_meta');
	if (cm && cm.accept_content) {
		for (var i = 0; i < cm.accept_content.length; i++) {
			var c_cnt = cm.accept_content[i];
			var cb_closure = (function(c_cnt) {
				return function() {
                    $fx.front.disable_infoblock(ib);
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
            adders.push(cb_closure);
			buttons.push({
				name:c_cnt.title,
				callback:cb_closure
			});
        }
	}
    ib.data('content_adders', adders);
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
                    admin_mode:$fx.front.mode,
					fx_admin:true
				}, function(json) {
					$fx_dialog.open_dialog(
                        json,
                        {
                            onfinish:function(res) {
                                $fx.front.reload_layout(
                                    function() {
                                        if (!res.props || !res.props.infoblock_id) {
                                            return;
                                        }
                                        var new_ib_node = $('.fx_infoblock_'+res.props.infoblock_id);
                                        if (new_ib_node.length == 0) {
                                            return;
                                        }
                                        $fx.front.select_item(new_ib_node.get(0));
                                        var adders = new_ib_node.data('content_adders');
                                        if (!adders || adders.length == 0 ){
                                            return;
                                        }
                                        adders[0]();
                                    }
                                );
                            }
                        }
                    );
				});
			}
		});
	}
	if (buttons.length > 0) {
		$fx.buttons.bind('add', function() {
			$fx.buttons.show_pulldown('add', buttons);
			return false;
		});
		$('html').one('fx_deselect', function() {
			$fx.front.redraw_add_button();
		});
	}
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
        n.addClass('fx_wrong_mode');
		return false;
	}
    if (n.is('.fx_template_var, .fx_template_var_in_att') && !n.is('.fx_content_essence') ) {
        var ne = n.closest('.fx_content_essence');
        if (ne && $('.fx_template_var, .fx_template_var_in_att', ne).length == 1) {
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
    $('html').on('keydown.fx_selected', function(e) {
       if (e.which == 27) {
           if (e.isDefaultPrevented && e.isDefaultPrevented()) {
                return;
           }
           $fx.front.deselect_item();
       }
       if (selectable_up && e.which == 38 && e.ctrlKey) {
           $fx.front.select_level_up();
           return;
       }
    });
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
    $('html').off('.fx_selected');
}

fx_front.prototype.select_level_up = function() {
    var item_up = $fx.front.get_selectable_up();
    if (item_up) {
        $fx.front.select_item(item_up);
    }
}


fx_front.prototype.hilight = function() {
	var items = $('.fx_template_var, .fx_area, .fx_template_var_in_att, .fx_content_essence, .fx_infoblock').not('.fx_unselectable');
	items.
        removeClass('fx_hilight').
        removeClass('fx_hilight_empty').
        removeClass('fx_hilight_empty_inline').
        removeClass('fx_no_hilight').
        removeClass('fx_wrong_mode');
    $('.fx_hilight_hover').removeClass('fx_hilight_hover');
    items.filter('.fx_hidden_placeholded').removeClass('fx_hidden_placeholded').html('');
	if ($fx.front.mode == 'view') {
		return;
	}
	items.each(function(index, item) {
		if ($fx.front.is_selectable(item)) {
            var i = $(item);
			i.addClass('fx_hilight');
            if (i.width() == 0 || i.height() == 0) {
                i.addClass('fx_hilight_empty');
                if (i.css('display') == 'inline') {
                	i.addClass('fx_hilight_empty_inline');
                }
            }
            var controller_meta = i.data('fx_controller_meta');
            if (controller_meta && controller_meta.hidden_placeholder) {
                i.html(controller_meta.hidden_placeholder);
                i.addClass('fx_hidden_placeholded');
            }
		}
	});
    var wrong_mode = items.filter('.fx_wrong_mode');
    wrong_mode.each(function(index, item) {
        if ($('.fx_hilight', item).length == 0) {
            $(item).addClass('fx_no_hilight');
        }
    });
    wrong_mode.filter('.fx_no_hilight .fx_no_hilight').removeClass('fx_no_hilight');
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
	   if (confirm(fx_lang("Вы уверены?"))) {
           $fx.front.disable_infoblock(ib_node);
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
    //$fx.front.start_essences_sortable(n.closest('.fx_infoblock'));
    //console.log('make sortbl', n.parent());
    $fx.front.start_essences_sortable(n.parent());
	$('html').one('fx_deselect', function() {
		$fx.buttons.unbind('edit');
		$fx.buttons.unbind('delete');
        $fx.front.stop_essences_sortable();
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
    
    $fx.buttons.bind('delete', function() {
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
            $fx_dialog.open_dialog(
                json, {
            		onfinish:function() {
                        $fx.front.reload_layout();
					}
                }
            );
        });
    });
    
    $fx.front.start_areas_sortable();
    
    $('html').one('fx_deselect', function() {
    	$fx.buttons.unbind('settings');	
        $fx.buttons.unbind('delete');	
        $fx.front.stop_areas_sortable();
    });
}

fx_front.prototype.set_mode_view = function () {
    
}

fx_front.prototype.start_essences_sortable = function(container) {
    var essences = $('.fx_content_essence.fx_hilight', container).filter(':not(.fx_not_sortable)');
    
    essences.each( function() {
        var cp = $(this).parent();
        if (
            cp.hasClass('fx_essence_container_sortable') || 
            cp.hasClass('fx_not_sortable')
        ) {
            return;
        }

        var essences = $('.fx_content_essence.fx_hilight', cp);
        if (essences.length < 2) {
            return;
        }
        var placeholder_class = "fx_essence_placeholder";
        if (essences.first().css('display') == 'inline') {
            placeholder_class += ' fx_essence_placeholder_inline';
        }

        cp.addClass('fx_essence_container_sortable');
        cp.sortable({
            items:'>:not(.fx_not_sortable).fx_content_essence.fx_hilight',
            placeholder: placeholder_class,
            forcePlaceholderSize : true,
            stop:function(e, ui) {
                var ce = ui.item.closest('.fx_content_essence');
                var ce_data = ce.data('fx_content_essence');
                var ce_id = ce_data.linker_id || ce_data.id;
                var ce_type = ce_data.linker_type || ce_data.type;
                
                var next_e = ce.nextAll('.fx_content_essence').first();
                var next_id = null;
                if (next_e.length > 0) {
                    var next_data = next_e.data('fx_content_essence');
                    next_id = next_data.linker_id || next_data.id;
                } else {
                    console.log('no next');
                }
                $fx.front.disable_infoblock(cp.closest('.fx_infoblock'));
                $fx.post({
                    essence:'content',
                    action:'move',
                    content_id:ce_id,
                    content_type:ce_type,
                    next_id:next_id
                }, function(res) {
                    $fx.front.reload_infoblock(cp.closest('.fx_infoblock'));
                });
                $fx.front.fix();
            }
        });
    });
}

fx_front.prototype.stop_essences_sortable = function(container) {
    if (!container) {
        container = $('.fx_essence_container_sortable');
    }
    if (!container.hasClass('fx_essence_container_sortable')) {
        return;
    }
    container.removeClass('fx_essence_container_sortable');
    container.sortable('destroy');
};

fx_front.prototype.set_mode_edit = function () {
    $fx.panel.one('fx.startsetmode', function() {
        $('html').off('.fx_edit_mode');
    });
};

fx_front.prototype.start_areas_sortable = function() {
    $('.fx_area').each(function(){
        var cp = $(this);
        if (cp.hasClass('fx_area_sortable')) {
            return;
        }
        cp.addClass('fx_area_sortable');
        cp.sortable({
            items:'>.fx_infoblock',
            //cancel:':not(.fx_selected)',
            connectWith:'.fx_area',
            placeholder: "fx_infoblock_placeholder",
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

fx_front.prototype.stop_areas_sortable = function() {
    $('.fx_area_sortable').
            sortable('destroy').
            removeClass('fx_area_sortable');
}

fx_front.prototype.set_mode_design = function() {
	$fx.panel.one('fx.startsetmode', function() {
        $('html').off('.fx_design_mode');/*
        $('.fx_area_sortable').
            sortable('destroy').
            removeClass('fx_area_sortable');
        */
    });
    
    
    //start_areas_sortable();
}

fx_front.prototype.disable_infoblock = function(infoblock_node) {
    $(infoblock_node).css({opacity:'0.3'}).on('click.fx_fake_click', function() {
        return false;
    });
}

fx_front.prototype.reload_infoblock = function(infoblock_node, callback) {
    $fx.front.disable_infoblock(infoblock_node);
    var ib_parent = $(infoblock_node).parent();
    var meta = $(infoblock_node).data('fx_infoblock');
    var page_id = $('body').data('fx_page_id');
    $.ajax({
       url:'/~ib/'+meta.id+'@'+page_id,
       success:function(res) {
           $(infoblock_node).off('click.fx_fake_click').css({opacity:''});
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
               var sel_target = ib_parent.find(selected_selector);
               if (sel_target.length > 0) {
                   sel_target = sel_target.get(0);
                   if (!$fx.front.is_selectable(sel_target)) {
                       sel_target = $fx.front.get_selectable_up(sel_target);
                   }
                   $fx.front.select_item(sel_target);
               }
           }
           $fx.front.hilight();
           if (typeof callback == 'function') {
               callback();
           }
       }
    });
}

fx_front.prototype.reload_layout = function(callback) {
   $fx.front.reload_infoblock($('body').get(0), callback);
}

fx_front.prototype.move_down_body =function () {
    $("body").css('margin-top','74px'); //74 - высота панели
}

fx_front.prototype.add_panel_field = function(field) {
	var field_node = $fx_form.draw_field(field);
	$('#fx_admin_fields').append(field_node);
	return field_node;
}