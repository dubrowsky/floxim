var fx_front = function () {
    this.mode = '';
    $('html').on('mouseover', function(e) {
        $fx.front.mouseover_node = e.target;
    });
    var more_menu = new fx_more_menu($fx.settings.more_menu); 
    more_menu.load();
           
    this.mode_menu = new fx_mode_menu();
    this.mode_menu.load();
    
    this.move_down_body();
    
    this.mode_selectable_selector = null;
    
    $('#fx_admin_page_modes').on('click', 'a', function() {
        var mode = $(this).attr('href').match(/\.([^\.]+)$/)[1];
        $fx.front.load(mode);
        return false;
    });
    
    $('html').on('keyup', '*', function(e) {
        // F2
        if (e.which === 113) {
            if ($fx.front.get_selected_item() && !e.shiftKey) {
                var $p = $fx.front.get_node_panel();
                var $edit = $('.fx_admin_button_edit', $p);
                if ($edit.length) {
                    $edit.click();
                    return false;
                }
                var $settings = $('.fx_admin_button_settings', $p);
                if ($settings.length) {
                    $settings.click();
                    return false;
                }
            }
            var mode_map = {
                view: 'edit',
                edit: 'design',
                design: 'view'
            };
            var target_mode = 
                    !e.shiftKey 
                    ? mode_map[$fx.front.mode]
                    : mode_map[mode_map[$fx.front.mode]];
            $fx.front.load(target_mode);
        }
    });
    
    this.c_hover = null;
    $('html').on('mouseover', '.fx_hilight', function(e) {
        if ($fx.front.mode === 'view') {
            return;
        }
        if ($fx.front.hilight_disabled) {
            return;
        }
        if (e.fx_hilight_done) {
            return;
        }
        var node = $(this);
        if (node.hasClass('fx_selected')) {
            e.fx_hilight_done = true;
            return;
        }
        $fx.front.outline_block_off($($fx.front.c_hover));
        $fx.front.c_hover = this;
        $target = $(e.target);
        var fix_link_ce = $target.hasClass('fx_template_var') && $fx.front.mode === 'edit';
        setTimeout(
            function() {
                if ($fx.front.c_hover !== node.get(0)) {
                    return;
                }
                if (node.hasClass('fx_selected')) {
                    return;
                }
                $('.fx_hilight_hover').removeClass('fx_hilight_hover');
                node.addClass('fx_hilight_hover');
                $fx.front.outline_block(node);
                
                if (fix_link_ce) {
                    e.target.setAttribute('contenteditable', 'true');
                    /*
                    $(e.target).keydown(function(e) {
                        var sel  = window.getSelection();
                        var link = this;
                        var KEY_PGUP = 33;
                        var KEY_PGDN = 34;
                        var KEY_END = 35;
                        var KEY_DOWN = 40;
                        var KEY_RIGHT = 39;
                        var KEY_HOME = 36;

                        if (e.which !== KEY_PGDN && e.which !== KEY_PGUP && e.which !== KEY_END && e.which !== KEY_DOWN && e.which !== KEY_RIGHT && e.which !== KEY_HOME) {
                                return;
                        }
                        var right_out = sel.anchorNode.length - sel.anchorOffset === 1
                                                                && !sel.anchorNode.nextSibling
                                                                && sel.anchorNode.parentNode === this;
                        console.log(right_out);
                        if (e.which !== KEY_RIGHT || right_out) {
                            console.log('end', sel.anchorNode.nextSibling);
                            var range = document.createRange();
                            if (e.which === KEY_HOME) {
                                range.setStart(link, 0);
                                range.collapse(true);
                            } else {
                                range.selectNodeContents(link);
                                range.collapse(false);
                            }
                            var sel = window.getSelection();
                            sel.removeAllRanges();
                            sel.addRange(range);
                            return false;
                        }

                    });
                    */
                }
            }, 
            $fx.front.c_hover ? 100 : 10
        );
        node.one('mouseout', function() {
            $fx.front.c_hover = null;
            if (node.hasClass('fx_selected')) {
                return false;
            }
            setTimeout(
                function() {
                    if ($fx.front.c_hover !== node.get(0)) {
                        node.removeClass('fx_hilight_hover');
                        $fx.front.outline_block_off(node);
                        if (fix_link_ce) {
                            e.target.setAttribute('contenteditable', 'false');
                            $(e.target).unbind('keydown');
                        }
                    }
                },
                100
            );
        });
        e.fx_hilight_done = true;
        return;
        return false;
    });
    
    $('html').on('click', function(e) {
        if ($fx.front.mode === 'view' || $fx.front.select_disabled) {
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
            if (target.closest('html').length === 0) {
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
            e.preventDefault();
            return;
            return false;
        }
        
        $fx.front.select_item(closest_selectable);
        return false;
        
    });
    
    $('html').on('fx_select', function(e) {
        var n = $(e.target);
        $fx.front.redraw_add_button(n);
        if ($fx.front.mode === 'edit') {
            if (n.is('.fx_essence')) {
                $fx.front.select_content_essence(n);
                var tvs = $('.fx_template_var, .fx_template_var_in_att', n);
                if (tvs.length === 1) {
                    tvs.edit_in_place();
                }
            }
            if (n.is('.fx_template_var, .fx_template_var_in_att')) {
                n.edit_in_place();
            }
        }
        if (n.is('.fx_infoblock')) {
            $fx.front.select_infoblock(n);
        }
        return false;
    });
};

fx_front.prototype.disable_hilight = function() {
    this.hilight_disabled = true;
};

fx_front.prototype.enable_hilight = function(){
    this.hilight_disabled = false;
};

fx_front.prototype.disable_select = function() {
    this.select_disabled = true;
};

fx_front.prototype.enable_select = function(){
    this.select_disabled = false;
};

fx_front.prototype.get_area_meta = function($area_node) {
    var meta = $area_node.data('fx_area') || {};
    if (typeof meta.size === 'undefined') {
        // Хорошо бы вычислять
        var full_size = 1000;
        if ($area_node.outerWidth() < full_size*0.5) {
            meta.size = 'narrow';
        } else {
            meta.size = '';
        }
        $area_node.data('fx_area', meta);
    }
    return meta;
};

fx_front.prototype.redraw_add_button = function(node) {
    $fx.buttons.unbind('add');
    var mode = $fx.front.mode;
    var buttons = [];
    if (!node) {
        return;
    }
    if (!node.is('.fx_infoblock, .fx_area')) {
        return;
    }
    var ib = node.closest('.fx_infoblock');
    var adders = [];
    var cm = ib.data('fx_controller_meta');
    if (cm && cm.accept_content) {
        for (var i = 0; i < cm.accept_content.length; i++) {
            var c_cnt = cm.accept_content[i];
            var cb_closure = (function(c_cnt) {
                return function() {
                    $fx.front.select_item(ib.get(0));
                    
                    $fx.front_panel.load_form({
                       essence:'content',
                       action:'add_edit',
                       content_type:c_cnt.type,
                       infoblock_id:c_cnt.infoblock_id,
                       parent_id:c_cnt.parent_id
                    }, 
                        {
                        view:'cols',
                        onfinish:function() {
                            $fx.front.reload_infoblock(ib);
                        },
                        oncancel:function() {
                            
                        }
                    });
                };
            })(c_cnt);
            adders.push(cb_closure);
            if (mode === 'edit') {
               buttons.push({
                    name:c_cnt.title,
                    callback:cb_closure
               });
            }
        }
    }
    ib.data('content_adders', adders);
    
    if (mode === 'design' && node.is('.fx_area')) {
        var area_meta = $fx.front.get_area_meta(node.closest('.fx_area'));
        if (area_meta) {
            buttons.push({
                name:'Add new infoblock to '+area_meta.id,
                callback: function() {
                    $fx.front.add_infoblock_select_controller(node);
                }
            });
        }
    }
    for (var i = 0; i < buttons.length; i++) {
        $fx.front.add_panel_button(buttons[i]);
    }
};

/**
 * Function to show controller selection dialog
 */

fx_front.prototype.add_infoblock_select_controller = function($node) {
    //var infoblock_back = arguments.callee;
    var $area_node = $node.closest('.fx_area');
    var area_meta = $fx.front.get_area_meta($area_node);
    
    $fx.front.select_item($area_node.get(0));

    $fx.front_panel.load_form({
        essence:'infoblock',
        action:'select_controller',
        page_id:$('body').data('fx_page_id'),
        area:area_meta,
        fx_admin:true
    }, {
        view:'vertical',
        onfinish: $fx.front.add_infoblock_select_settings
    });
};

fx_front.prototype.add_infoblock_select_settings = function(data) {
    var $area_node = $($fx.front.get_selected_item());
    $fx.front_panel.show_form(data, {
        view:'horizontal',
        onfinish:function(res) {
            $fx.front.reload_layout(
                function() {
                    if (!res.props || !res.props.infoblock_id) {
                        return;
                    }
                    var new_ib_node = $('.fx_infoblock_'+res.props.infoblock_id);
                    if (new_ib_node.length === 0) {
                        return;
                    }
                    $fx.front.select_item(new_ib_node.get(0));
                    $fx.front.scrollTo(new_ib_node);
                    var adders = new_ib_node.data('content_adders');
                    if (!adders || adders.length === 0 ){
                        return;
                    }
                    adders[0]();
                }
            );
        },
        onready:function($form) {
           var back = $('.form_header a.back', $form);
            back.on('click', function() {
                infoblock_back();
                $('.fx_infoblock_fake').remove();
            });

            // creating infoblock preview
            $fx.front.deselect_item();
            var ib_node = $('<div class="fx_infoblock fx_infoblock_fake" />');
            $area_node.append(ib_node);
            ib_node.data('fx_infoblock', {id:'fake'});
            $form.data('ib_node', ib_node);
            //$fx.front.scrollTo($ib_node);
            $form.on('change', function(e) {
                if ($form.data('is_waiting')) {
                    return;
                }
                $form.data('is_waiting', true);
                $fx.front.reload_infoblock(
                    $form.data('ib_node'), 
                    function($new_ib_node) {
                        $form.data('ib_node', $new_ib_node);
                        $form.data('is_waiting', false);
                        $fx.front.select_item($new_ib_node.get(0));
                        $fx.front.scrollTo($new_ib_node);
                    }, 
                    {override_infoblock:$form.serialize()}
                );
            });
            $form.change();
        },
        oncancel:function() {
            $('.fx_infoblock_fake').remove();
        }
    });
};

fx_front.prototype.is_selectable = function(node) {
    var n = $(node);
    
    if (n.hasClass('fx_unselectable')) {
        return false;
    }
    
    
    switch($fx.front.mode) {
        case 'view': default:
            return false;
        case 'design':
            return n.hasClass('fx_area') || n.hasClass('fx_infoblock');
        case 'edit':
            if (n.hasClass('fx_essence') || n.hasClass('fx_accept_content')) {
                return true;
            }
            if ( n.hasClass('fx_template_var') || n.hasClass('fx_template_var_in_att') ) {
                var ne = n.closest('.fx_essence');
                if (ne && $('.fx_template_var, .fx_template_var_in_att', ne).length === 1) {
                    return false;
                }
                return true;
            }
            return false;
    }
};

fx_front.prototype.get_selectable_up = function(rel_node) {
    if (!rel_node) {
        rel_node = this.get_selected_item();
    }
    if (!rel_node) {
        return null;
    }
    var selectable_up = null;
    var parents = $(rel_node).parents();
    for (var i = 0; i < parents.length; i++) {
        var c_parent = parents.get(i);
        if (this.is_selectable(c_parent)) {
            selectable_up = c_parent;
            break;
        }
    }
    return selectable_up;
};

fx_front.prototype.fix = function() {
    $('body').css('opacity', '0.99');
    setTimeout(function(){$('body').css('opacity', 1);}, 5);
};

fx_front.prototype.select_item = function(node) {
    var c_selected = this.get_selected_item();
    if (c_selected === node) {
        return;
    }
    this.deselect_item();
    this.selected_item = node;
    var $node = $(node);
    this.make_node_panel($node);
    
    $node.on('mouseout.fx_catch_mouseout', function (e) {
       e.stopImmediatePropagation();
    });
    
    var selectable_up = this.get_selectable_up();
    if (selectable_up) {
        $fx.buttons.bind('select_block', $fx.front.select_level_up);
        $fx.front.add_panel_button('select_block', $fx.front.select_level_up);
    } else {
        $fx.buttons.unbind('select_block', $fx.front.select_level_up);
    }
    
    $node.addClass('fx_selected').trigger('fx_select');
    $fx.front.outline_block_off($node);
    $fx.front.outline_block($node, 'selected');
    
    
    // при удалении выбранного узла из дерева дергаем deselect_item()
    $(node).bind('remove.deselect_removed', function(e) {
        $fx.front.deselect_item();
    });
    
    $fx.front.disable_hilight();
    $('html').on('keydown.fx_selected', function(e) {
       if (e.which === 27) {
           if (e.isDefaultPrevented && e.isDefaultPrevented()) {
                return;
           }
           $fx.front.deselect_item();
       }
       if (selectable_up && e.which === 38 && e.ctrlKey) {
           $fx.front.select_level_up();
           return;
       }
    });
};

fx_front.prototype.make_node_panel = function($node) {
    var $overlay = this.get_front_overlay();
    var $panel = $('<div class="fx_node_panel fx_overlay"></div>');
    $overlay.append($panel);
    $node.data('fx_node_panel', $panel);
    var o = $node.offset();
    $panel.css({
        position:'absolute',
        left:o.left - 4 + 'px',
        top: o.top - $panel.outerHeight() - 4 + 'px',
        'z-index':this.get_panel_z_index() + 1
    });
    
    setTimeout(function() {
        $fx.front.recount_node_panel();
    }, 10);
    $(window).on('scroll', function() {$fx.front.recount_node_panel();});
    
};

fx_front.prototype.recount_node_panel = function() {
    var $p = this.get_node_panel();
    if (!$p) {
        return;
    }
    var $p_items = $p.children();
    if ($p_items.length === 0) {
        return;
    }
    $p.css({
        width:'1000px',
        visibility:'hidden'
    });
    var po = $p.offset();
    var p_left = po.left;
    var $lpi = $p_items.last();
    $lpi.css('margin-right', '3px');
    var p_right = $lpi.offset().left + $lpi.outerWidth() + parseInt($lpi.css('margin-right'));
    var p_width = p_right - p_left;
    var p_height = $p.outerHeight();
    var css = {
        width:p_right - p_left + 'px',
        visibility:'visible',
        opacity:1
    };
    var top_fix = 0;
    var $top_fixed_nodes = $('#fx_admin_panel, .fx_top_fixed');
    var scroll_top = $('body').scrollTop();
    $top_fixed_nodes.each(function (index, item) {
        var $i = $(item);
        var i_top = $i.offset().top - scroll_top;
        var i_bottom = i_top + $i.outerHeight();
        if (i_bottom > top_fix) {
            top_fix = i_bottom;
        }
    });
    
    var $node = $($fx.front.get_selected_item());
    var no = $node.offset();
    var break_top = no.top - top_fix - p_height - 4;
    var break_bottom = break_top + $node.outerHeight() + p_height;
    var doc_scroll = $(document).scrollTop();
    
    if (doc_scroll >= break_bottom) {
        css.top = no.top + $node.outerHeight() + 4 + 'px';
        css.position = 'absolute';
        $p.removeClass('fx_node_panel_fixed');
    } else if (doc_scroll <= break_top) {
        // set panel underneath the node
        css.position = 'absolute';
        css.top = no.top - $p.outerHeight() - 4 + 'px';
        $p.removeClass('fx_node_panel_fixed');
    } else {
        //if (!$p.hasClass('fx_node_panel_fixed')) {
        // set panel fixed inside the node
        //if (doc_scroll > break_top && doc_scroll < break_bottom) {
            //if () {};
            var bottom_edge_visible = doc_scroll + $(window).height() > no.top + $node.outerHeight() + p_height;
            if (bottom_edge_visible) {
                css.top = no.top + $node.outerHeight() + 4 + 'px';
                css.position = 'absolute';
                $p.removeClass('fx_node_panel_fixed');
            } else {
                css.position = 'fixed';
                css.top = top_fix+'px';
                css.opacity = 0.7;
                $p.addClass('fx_node_panel_fixed');
            }
        //}
        //}
    }
    var p_gone = (p_left + p_width) - $(window).outerWidth() + 10;
    if (p_gone > 0) {
        css.left = p_left - p_gone;
    }
    $p.css(css);
    $p.css('opacity', parseFloat($p.css('opacity'))+0.05);
    clearTimeout($p.data('opacity_timeout'));
    $p.data('opacity_timeout', setTimeout(function() {
        $p.css('opacity', parseFloat($p.css('opacity'))-0.05);
    }, 100));
};

fx_front.prototype.get_selected_item = function() {
    return this.selected_item;
};

fx_front.prototype.deselect_item = function() {
    var selected_item = this.get_selected_item();
    if (selected_item) {
        $node = $(selected_item);
        $node.off('.fx_catch_mouseout');
        $fx.front.enable_hilight();
        $node.
                removeClass('fx_selected').
                trigger('fx_deselect').
                unbind('remove.deselect_removed');
        $fx.front.outline_block_off($node);
        var $panel = $node.data('fx_node_panel');
        if ($panel) {
            $panel.remove();
        }
    }
    this.selected_item = null;
    $fx.buttons.unbind('select_block');
    $('html').off('.fx_selected');
    if (this.mouseover_node) {
        $(this.mouseover_node).trigger('mouseover');
    }
};

fx_front.prototype.select_level_up = function() {
    var item_up = $fx.front.get_selectable_up();
    if (item_up) {
        $fx.front.select_item(item_up);
    }
};


fx_front.prototype.hilight = function() {
    var items = $('.fx_template_var, .fx_area, .fx_template_var_in_att, .fx_essence, .fx_infoblock').not('.fx_unselectable');
    items.
        removeClass('fx_hilight').
        removeClass('fx_hilight_empty').
        removeClass('fx_hilight_empty_inline').
        removeClass('fx_no_hilight').
        removeClass('fx_clearfix');
    $('.fx_hilight_hover').removeClass('fx_hilight_hover');
    items.filter('.fx_hidden_placeholded').removeClass('fx_hidden_placeholded').html('');
    if ($fx.front.mode === 'view') {
        return;
    }
    items.each(function(index, item) {
        var i = $(item);
        var meta = i.data('fx_controller_meta') || {};

        if (meta.accept_content) {
            i.addClass('fx_accept_content');
        }
        if ($fx.front.is_selectable(item)) {
            
            i.addClass('fx_hilight');
            if (!i.css('float').match(/left|right/) && !i.css('display').match(/^inline/)) {
                i.addClass('fx_clearfix');
            }
            // зеленые выделения для полей внутри скрытых блоков
            // пока лечим через i.is(':visible')
            if (i.is(':visible') && (i.width() === 0 || i.height() === 0) ) {
                i.addClass('fx_hilight_empty');
                if (i.css('display') === 'inline') {
                    i.addClass('fx_hilight_empty_inline');
                }
            }
            if (meta.hidden_placeholder) {
                i.html(meta.hidden_placeholder);
                i.addClass('fx_hidden_placeholded');
            }
        }
    });
};

fx_front.prototype.load = function ( mode ) {
    this.mode = mode;
    $.cookie('fx_front_mode', mode, {path:'/'});
    
    $fx.front.outline_all_off();
    
    $fx.front.deselect_item();
    
    $fx.front.hilight();
    
    $fx.buttons.draw_buttons($fx.buttons_map.page);
    
    $fx.main_menu.set_active_item('site');
        
    if (mode === 'view') {
        this.set_mode_view();
    } else if (mode === 'edit') {
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
    if (this.mouseover_node) {
        $(this.mouseover_node).trigger('mouseover');
    }
};

fx_front.prototype.select_content_essence = function(n) {
    var essence_meta = n.data('fx_essence');
    var ib_node = n.closest('.fx_infoblock').get(0);
    $fx.front.add_panel_button('edit', function() {
        $fx.front.select_item(n.get(0));
        $fx.front_panel.load_form(
            {
                essence:'content',
                action:'add_edit',
                content_id: essence_meta[0],
                content_type:essence_meta[1]
            }, 
            {
                view:'cols',
                onfinish: function() {
                    $fx.front.reload_infoblock(ib_node);
                },
                oncancel: function() {
                    
                }
            }
        );
    });
    
    $fx.front.add_panel_button('delete', function() {
       if (confirm(fx_lang("Вы уверены?"))) {
           $fx.front.disable_infoblock(ib_node);
           var ce_type = essence_meta[3] || essence_meta[1];
           var ce_id = essence_meta[2] || essence_meta[0];
           $fx.post({
               essence:'content',
               action:'delete_save',
               content_type:ce_type,
               content_id:ce_id,
               page_id:$('body').data('fx_page_id')
           }, function () {
               $fx.front.reload_infoblock(ib_node);
           });
       }
    });
    $fx.front.start_essences_sortable(n.parent());
    $('html').one('fx_deselect', function(e) {
        $fx.front.stop_essences_sortable();
    });
};

fx_front.prototype.select_infoblock = function(n) {
    if ($fx.front.mode !== 'design') {
        return;
    }
    $fx.front.add_panel_button('settings', function() {
        var ib_node = n;
        var ib = $(ib_node).data('fx_infoblock');
        if (!ib) {
            return;
        }
        var area_node = ib_node.closest('.fx_area');
        var area_meta = $fx.front.get_area_meta(area_node);
        
        $fx.front_panel.load_form({
            essence:'infoblock',
            action:'select_settings',
            id:ib.id,
            visual_id:ib.visual_id,
            page_id:$('body').data('fx_page_id'),
            fx_admin:true,
            //area_size:area_size
            area:area_meta
        }, {
            view:'horizontal',
            onfinish:function() {
                $fx.front.reload_infoblock(ib_node);
            },
            onready:function($form) {
                $form.data('ib_node', ib_node);
                $form.on('change', function(e) {
                    if (e.target.name === 'livesearch_input') {
                        return;
                    }
                    if ($form.data('is_waiting')) {
                        return;
                    }
                    $form.data('is_waiting', true);
                    $fx.front.reload_infoblock(
                        $form.data('ib_node'), 
                        function($new_ib_node) {
                            $form.data('ib_node', $new_ib_node);
                            $form.data('is_waiting', false);
                        }, 
                        {override_infoblock:$form.serialize()}
                    );
                });
            },
            oncancel:function($form) {
                $fx.front.reload_infoblock($form.data('ib_node'));
            }
        });
    });
    
    $fx.front.add_panel_button('delete', function() {
        var ib_node = $fx.front.get_selected_item();
        if (!ib_node) {
            return;
        }
        var ib = $(ib_node).data('fx_infoblock');
        if (!ib) {
            return;
        }
        $fx.front_panel.load_form({
            essence:'infoblock',
            action:'delete_infoblock',
            id:ib.id,
            fx_admin:true
        }, {
            onfinish: function() {
                $fx.front.reload_layout();
            }
        });
    });
    
    $fx.front.start_areas_sortable();
    
    $('html').one('fx_deselect', function() {
        $fx.buttons.unbind('settings');    
        $fx.buttons.unbind('delete');    
        $fx.front.stop_areas_sortable();
    });
};

fx_front.prototype.set_mode_view = function () {
    
};

fx_front.prototype.start_essences_sortable = function($cp) {
    var sortable_items_selector = ' > .fx_essence.fx_sortable.fx_hilight';
    var $essences = $(sortable_items_selector, $cp);
    if ($essences.length < 2 || $cp.hasClass('fx_not_sortable')) {
        return;
    }
    var placeholder_class = "fx_essence_placeholder";
    if ($essences.first().css('display') === 'inline') {
        placeholder_class += ' fx_essence_placeholder_inline';
    }
    $cp.addClass('fx_essence_container_sortable');
    
    var is_x = true;
    var is_y = true;
    var c_x = null;
    var c_y = null;
    $essences.each(function()  {
        var o  = $(this).offset();
        if (c_x === null){
            c_x = o.left;
        } else if (o.left !== c_x) {
            is_y = false;
        }
        if (c_y === null){
            c_y = o.top;
        } else if (o.top !== c_y) {
            is_x = false;
        }
    });
    var axis = is_x ? 'x' : is_y ? 'y' : null;
    
    var sort_params = {
        axis:axis,
        items:sortable_items_selector,
        placeholder: placeholder_class,
        forcePlaceholderSize : true,
        start:function(e, ui) {
            var ph = ui.placeholder;
            var item = ui.item;
            ph.css({
                width:item.width()+'px',
                height:item.height()+'px',
                'box-sizing':'border-box'
            });
            ph.attr('class', ph.attr('class')+ ' '+item.attr('class'));
            $c_selected = $($fx.front.get_selected_item());
            $fx.front.outline_block_off($c_selected);
            $fx.front.disable_hilight();
            $fx.front.get_node_panel().hide();
        },
        stop:function(e, ui) {
            var ce = ui.item.closest('.fx_essence');
            var ce_data = ce.data('fx_essence');
            var ce_id = ce_data[2] || ce_data[0];
            var ce_type = ce_data[3] || ce_data[1];

            var next_e = ce.nextAll('.fx_essence').first();
            var next_id = null;
            if (next_e.length > 0) {
                var next_data = next_e.data('fx_essence');
                next_id = next_data[2] || next_data[0];
            }
            $fx.front.disable_infoblock($cp.closest('.fx_infoblock'));
            $fx.post({
                essence:'content',
                action:'move',
                content_id:ce_id,
                content_type:ce_type,
                next_id:next_id
            }, function(res) {
                $fx.front.reload_infoblock($cp.closest('.fx_infoblock'));
            });
            $fx.front.get_node_panel().show();
        }
    };
    $cp.sortable(sort_params);
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
                var ph = ui.placeholder;
                var item = ui.item;
                ph.css({
                    'height':'100px',
                    'max-width':'300px'
                    //width:item.width()+'px',
                    //height:item.height()+'px',
                    //'box-sizing':'border-box'
                });
                //ph.attr('class', ph.attr('class')+ ' '+item.attr('class'));
                $c_selected = $($fx.front.get_selected_item());
                $fx.front.outline_block_off($c_selected);
                $fx.front.disable_hilight();
                $fx.front.get_node_panel().hide();
            },
            stop:function(e, ui) {
                $('.fx_area').removeClass('fx_area_target');
                var ce = ui.item;
                var ce_data = ce.data('fx_infoblock');
                $fx.front.outline_block_off(ce);
                $fx.front.outline_block(ce, 'selected');

                var params = {
                    essence:'infoblock',
                    action:'move',
                    area:ce.closest('.fx_area').data('fx_area').id
                };

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
                //$fx.front.fix();
            }
        });
    });
};

fx_front.prototype.stop_areas_sortable = function() {
    $('.fx_area_sortable').
            sortable('destroy').
            removeClass('fx_area_sortable');
};

fx_front.prototype.set_mode_design = function() {
    $fx.panel.one('fx.startsetmode', function() {
        $('html').off('.fx_design_mode');
    });
};

fx_front.prototype.disable_infoblock = function(infoblock_node) {
    $(infoblock_node).css({opacity:'0.3'}).on('click.fx_fake_click', function() {
        return false;
    });
};

fx_front.prototype.reload_infoblock = function(infoblock_node, callback, extra_data) {
    var $infoblock_node = $(infoblock_node);
    $fx.front.disable_infoblock(infoblock_node);
    var ib_parent = $infoblock_node.parent();
    var meta = $infoblock_node.data('fx_infoblock');
    var page_id = $('body').data('fx_page_id');
    var post_data = {c_url:document.location.href};
    if (typeof extra_data !== 'undefined') {
        $.extend(post_data, extra_data);
    }
    $.ajax({
        type:'post',
        data:post_data,
       url:'/~ib/'+meta.id+'@'+page_id,
       success:function(res) {
           $fx.front.c_hover = null;
           $infoblock_node.off('click.fx_fake_click').css({opacity:''});
           var selected = $infoblock_node.descendant_or_self('.fx_selected');
           var selected_selector = null;
           if(selected.length > 0) {
                selected_selector = selected.first().generate_selector(ib_parent);
           }
           $fx.front.outline_all_off();
           $fx.front.deselect_item();

           if (infoblock_node.nodeName === 'BODY') {
               var inserted = false;
               $infoblock_node.children().each(function() {
                   if(!$(this).hasClass('fx_overlay')) {
                       if (!inserted) {
                            $(this).before(res);
                            inserted = true;
                       }
                       $(this).remove();
                   }
               });
               $fx.front.front_overlay = null;
               $('body').trigger('fx_infoblock_loaded');
           } else {
               $infoblock_node.hide().before(res);
               var $new_infoblock_node = $infoblock_node.prev();
               $new_infoblock_node.trigger('fx_infoblock_loaded');
               $infoblock_node.remove();
           }
           
           $fx.front.hilight();
           $('body').removeClass('fx_stop_outline');
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
                                            
           if (typeof callback === 'function') {
               callback($new_infoblock_node);
           }
       }
    });
};

fx_front.prototype.scrollTo = function($node) {
    $node = $($node);
    var body_offset = parseInt($('body').css('margin-top'));
    var top_offset = $node.offset().top - body_offset - 15;
    $('body').scrollTo(
        top_offset,
        800
    );
};

fx_front.prototype.reload_layout = function(callback) {
   $fx.front.reload_infoblock($('body').get(0), callback);
};

fx_front.prototype.move_down_body =function () {
    $("body").css('margin-top','34px'); //34 - высота панели
};

fx_front.prototype.get_node_panel = function() {
    return $($fx.front.get_selected_item()).data('fx_node_panel');
};

fx_front.prototype.add_panel_field = function(field) {
    var $field_container = $fx.front.get_node_panel(); // $('#fx_admin_fields')
    $field_container.show();
    var field_node = $fx_form.draw_field(field, $field_container);
    field_node.css({'outline-style': 'solid','outline-color':'#FFF'});
    field_node.find(':input').css({'background':'transparent'});
    field_node.animate(
        {
            'background-color':'#FF0', 
            'outline-width':'6px',
            'outline-color':'#FF0'
        },
        300,
        null,
        function() {
            field_node.animate(
                {
                    'background-color':'#FFF', 
                    'outline-width':'0px',
                    'outline-color':'#FFF'
                },
                300
            );
        }
    );
    return field_node;
};

fx_front.prototype.add_panel_button = function(button, callback) {
    var $p = this.get_node_panel();
    if (typeof button !== 'string') {
        if (!callback) {
            callback = button.callback;
        }
        var $b = $('<div class="fx_admin_button_text fx_admin_button"><span>'+button.name+'</span></div>');
        //return;
    } else {
        var button_code = button;
        var $b = $('<div class="fx_admin_button_'+button_code+' fx_admin_button"></div>');
    }
    
    $b.click(callback);
    $p.append($b).show();
    return $b;
};

fx_front.prototype.outline_panes = [];


fx_front.prototype.get_front_overlay = function() {
    if (!this.front_overlay) {
        this.front_overlay = $(
            '<div class="panel_overlay" style="position:absolute; top:0px; left:0px;"></div>'
        );
        $('body').append(this.front_overlay);
    }
    return this.front_overlay;
};

fx_front.prototype.get_panel_z_index = function() {
    if (typeof this.panel_z_index === 'undefined') {
        this.panel_z_index = $('#fx_admin_control').css('z-index') - 10;
    }
    return this.panel_z_index;
};

fx_front.prototype.outline_block = function(n, style) {
    if (!style) {
        style = 'hover';
    }
    if (!n || n.length === 0) {
        return;
    }
    // already hilighted 
    if (n.data('fx_outline_panes') && n.data('fx_outline_style') === style) {
        return;
    }
    if (style === 'selected') {
        var recount_outlines = function() {
            $fx.front.outline_block_off(n);
            $fx.front.outline_block(n, 'selected');
            $fx.front.recount_node_panel();
        };
        n.on('resize.recount_outlines', recount_outlines);
        $(window).on('resize.recount_outlines', recount_outlines);
    }
    var o = n.offset();
    var overlay_offset = parseInt($('.panel_overlay').css('top')); 
    o.top -= overlay_offset > 0 ? overlay_offset : 0 ;
    var nw = n.outerWidth() + 1;
    var nh = n.outerHeight();
    var size = style === 'hover' ? 2 : 2;
    var pane_z_index = $fx.front.get_panel_z_index();
    var parents = n.parents();
    var pane_position = 'absolute';
    if (n.css('position') === 'fixed') {
        pane_position = 'fixed';
    }
    var fixed_found = false, overflow_found = false;
    for (var i = 0 ; i<parents.length; i++) {
        var $cp = parents.eq(i);
        if (pane_position !== 'fixed' && $cp.css('position') === 'fixed') {
            pane_position = 'fixed';
            if ($cp.css('z-index') !== undefined) {
                pane_z_index = $cp.css('z-index');
            }
            fixed_found = true;
        }
        if ($cp.css('overflow') === 'hidden') {
            var cph = $cp.outerHeight();
            if (cph < nh) {
                nh = cph;
            }
            overflow_found = true;
        }
        if (fixed_found && overflow_found) {
            break;
        }
    };
    var doc_width = $(document).width();
    var front_overlay = $fx.front.get_front_overlay();
    function make_pane(box, type) {
        var c_left = box.left;
        var c_width = box.width;
        if (c_left < 0) {
            c_left = 0;
            box.left = c_left;
        } else if (c_left >= doc_width) {
            c_left = doc_width - size - 1;
            box.left= c_left;
        }
        if (c_width + c_left >= doc_width) {
            box.width = (doc_width - c_left);
        }
        var css = {};
        // добавляем px для размеров
        $.each(box, function(i, v) {
            css[i] = Math.round(v)+'px';
        });
        var m = $(
            '<div class="fx_outline_pane '+
                'fx_outline_pane_'+type+' fx_outline_style_'+style+'" />'
        );
        css['z-index'] = pane_z_index;
        css['position'] = pane_position;
        m.css(css);
        m.data('pane_props', $.extend(box, {
            type:type,
            vertical: type === 'left' || type === 'right'
        }));
        front_overlay.append(m);
        return m;
    }
    var panes = {};
    var top_left_offset = 0;
    var top_top_offset = 0;
    var bottom_right_offset = 0;
    var bottom_bottom_offset = 0;
    if (n.css('display') === 'inline' && n.text() !== '') {
        var m_before = $('<span style="display:inline-block;width:1px; height:1px;background:#F00;"></span>');
        m_before.insertBefore(n.get(0).firstChild);
        var mbo = m_before.offset();
        // сравниваем высоту для случаев, когда тестовый пиксель оказывается на предыдущей строке один, 
        // т.к. он влезает, а реальный текст - нет
        if (mbo.top > o.top && (mbo.left - parseInt(n.css('padding-left')) - o.left) > 10) {
            top_left_offset = (mbo.left - o.left);
            top_top_offset = mbo.top - o.top + size*2 + 1;
            panes.top_left = make_pane({
                top:o.top - size,
                left:mbo.left - size,
                height: (mbo.top - o.top) + size*2,
                width:size
            }, 'left');
            panes.top_top = make_pane({
                top:mbo.top +size*2,
                left:o.left,
                width:mbo.left - o.left,
                height:size
            }, 'top');
        }
        m_before.remove();
        var m_after = $('<span style="display:inline-block;width:1px; height:1px;vertical-align: top; background:#FFF;"></spans>');
        n.append(m_after);
        var mao = m_after.offset();
        /// ловить правильно случаи, когда спан-тестер переносит строку
        if (n.outerHeight() > nh) {
            mao.top = o.top;
            mao.left = o.left + nw;
        }
        
        if ( (o.left+nw) - (mao.left + parseInt(n.css('padding-right')) ) > 10) {
            bottom_right_offset = nw - (mao.left - o.left);
            bottom_bottom_offset = o.top+nh-mao.top;
            panes.bottom_right = make_pane({
                top:mao.top,
                left:mao.left,
                width:size,
                height:bottom_bottom_offset
            }, 'right');
            panes.bottom_bottom = make_pane({
                top:mao.top,
                left:mao.left,
                width: bottom_right_offset,
                height:size
            }, 'bottom');
        }
        
        m_after.remove();
    }
    if (pane_position==='fixed') {
        o.top -=$(window).scrollTop();
    }
    panes.top = make_pane({
        top: o.top - size,
        left: (o.left + top_left_offset),
        width:(nw-top_left_offset ),
        height:size
    }, 'top');
    panes.bottom = make_pane({
        top:o.top + nh,
        left:o.left,
        width: nw - bottom_right_offset,
        height:size
    }, 'bottom');
    panes.left = make_pane({
        top: (o.top - size + top_top_offset),
        left:o.left - size ,
        width:size,
        height: (nh + size*2 - top_top_offset)
        
    }, 'left');
    panes.right = make_pane({
        top:o.top - size ,
        left:o.left + nw ,
        width:size,
        height: (nh + size*2 - bottom_bottom_offset) 
    }, 'right');
    n.data('fx_outline_panes', panes);
    n.data('fx_outline_style', style);
};

fx_front.prototype.outline_block_off = function(n) {
    var panes = n.data('fx_outline_panes');
    if (!panes) {
        return;
    }
    for (var i in panes) {
        panes[i].remove();
    }
    n.data('fx_outline_panes', null);
    n.off('.recount_outlines');
    $(window).off('.recount_outlines');
};

fx_front.prototype.outline_all_off = function() {
    $('.fx_outline_pane').remove();
};