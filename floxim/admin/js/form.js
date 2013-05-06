(function($){
    fx_form = {
        to_click: [],
        to_change: [],
        jsonlink_post: [],
        step_contents: {},

        get_settings_from: function () {
            return {
                id:'nc_dialog_form', 
                action:$fx.settings.action_link,
                target:'nc_upload_target'
            };
        },
        
        show_step: function ( step ) {
            $('.fx_step_content').each(function() { 
                if ( $(this).attr('id') == 'fx_step_'+step ) {
                    $(this).show();
                } 
                else {
                    $(this).remove();
                }
            });
            $fx_form.preparation( $fx_form.step_contents[1].settings, $fx_form.step_contents[1].container, 0 );
            $fx.panel.trigger('fx.fielddrawn');
        },
     
        draw_fields: function(settings, container, main_content) {
        	if (settings.fields === undefined) {
        		return;
        	}
        	
            $fx_form.main_content = main_content || false;
            
            var prepared_data = $fx_form.preparation(settings, container);
            settings = prepared_data.settings;
            container = prepared_data.container;
            
            $fx_form.df_change = [];
            $fx_form.to_click = [];
            //$fx_form.to_change = [];
            
            
            if (settings.tabs) {
                $fx_form.init_tabs(settings, container);
            }
            
            if ( settings.areas ) {
                $fx_form.init_areas(settings.areas, container);
            }
            
            $fx_form.to_change = $();

            $.each(settings.fields, function(i, json){
                var _el = $fx_form.draw_field(json, container);
                
                
                if ( json.name == 'posting' && json.value == 1 || settings.step ) {
                    $fx_dialog.button_enable('save');
                }
                
                // ajax change
                if (json.post && json.type !== 'button') {
                    json.post['fx_admin'] = 1;
                    
                    $(':input[name]',  $("<div />").append(_el) ).each(function() {
                        //if ($(this).data('events') === undefined) { // hax prevent muli-events
                            $(this).change(function(){
                            	var changed = $(this);
                            	
                            	var form_vals = {};
                            	
                            	$('input, textarea, select', $(this).closest('form')).each(function(){
									var c_field_name = $(this).attr('name');
									var c_field_type = $(this).attr('type');
									if (c_field_name != 'posting' && c_field_type != 'button') {
										if (c_field_type == 'radio') {
											var val = $('input[name="'+$(this).attr('name')+'"]:checked').val();
										} else {
											var val = $(this).val();
										}
										form_vals[c_field_name] = val;
									}
                                });
                                
                                
                                var data_to_post = $.extend({}, form_vals, json.post);
                                
                                $fx.container = json.child;

                                $fx_form.show_loader();
                                
                                $fx.post(data_to_post, function(fields){
                                        var con_id = 'nc_container_'+changed.attr('name').replace('[', '_').replace(']', '_');
                                        $fx_form.draw_fields(fields, con_id);
                                        $('#'+con_id).addClass('nc_container_child_'+json.type);
                                        $fx_form.hide_loader();
								});
                            });
                            
                            if (json.change_on_render) {
                            	$fx_form.to_change = $fx_form.to_change.add(this);
                            }
                        //}
                    });
                }
            
                if ( json.area ) {
                    $('#' + $fx_form.get_area_id(json.area) ).append(_el);
                } else {
                    if (json.post && json.name && $('#nc_container_'+json.name.replace('[', '_').replace(']', '_')).length == 0 ) {
                        var append_after = '<div id="nc_container_'+json.name.replace('[', '_').replace(']', '_')+'" />';
                    }
                
                    if (json.layer) {
                        if ( $('#fx_layer_'+json.layer, container).length <= 0 ) {
                        	var clicker_title = false;
                        	if (settings.layers && settings.layers[json.layer]) {
                        		clicker_title = settings.layers[json.layer];
                        		var clicker = $('<div class="fx_layer_name"><span></span></div>');
								$('span', clicker).html(clicker_title.on).click(function(){
									$('#fx_layer_'+json.layer, container).toggle();
								}).toggle(function(){
										$(this).text(clicker_title.off);
									}, 
									function(){
										$(this).text(clicker_title.on);
									}
								);
								container.append(clicker);
                            }
                            container.append($('<div/>', {
                                id:'fx_layer_'+json.layer, 
                                style:clicker_title ? 'display:none;' : ''
                            }));
                        }
                        $('#fx_layer_'+json.layer, container).append(_el);
                    } else if (settings.tabs || $('#nc_tabs').length == 1) {
                        if (json.tab) {
                            $('#'+settings.form.id+'_'+json.tab, container).append(_el);
                        } else {
                            $(container).append(_el); 
                        }
                        
                    } else {
                        $(container).append(_el);
                    }

                    if (append_after) {
                        $(container).append(append_after);
                        append_after = '';
                    }
                    
                    if (json.parent) {
                    	if (json.parent instanceof Array) {
                            var parent = {};
                            parent[json.parent[0]] = json.parent[1];
                    	} else {
                            var parent = json.parent;  
                    	}
                        
                    	var check_parent_state = function() {
                            var do_show = true;
                            $.each(parent, function(pkey, pval) {
                                var pexp = '==';
                                if (/^!=/.test(pval)) {
                                    pval = pval.replace(/^!=/, '');
                                    pexp = '!=';
                                } else if (/^\~/.test(pval)) {
                                    pval = pval.replace(/^\~/, '');
                                    pexp = 'regexp';
                                }
                                var par_inp = $(':input[name="'+pkey+'"]');
                                if (par_inp.length == 0) {
                                    return;
                                }
                                
                                if (par_inp.attr('type') == 'checkbox') {
                                    var par_val = par_inp.get(0).checked * 1;
                                } else {
                                    var par_val = par_inp.val();
                                }
				
                                if (par_inp.attr('type') == 'radio') {
                                    par_val = $(':input[name="'+pkey+'"]:checked').val();
                                }
                                switch (pexp) {
                                    case '==':
                                        do_show = (par_val == pval);
                                        break;
                                    case '!=':
                                        if (!par_inp.is(':visible')) {
                                            do_show = true;
                                        } else {
                                            do_show = (par_val != pval);
                                        }
                                        break;
                                    case 'regexp':
                                        var prex = new RegExp(pval);
                                        do_show = prex.test(par_val);
                                        break;
                                }
                            });
                            if (do_show) {
                                _el.show();
                            } else {
                                _el.hide();
                            }
                            _el.find(':input').trigger('change');
                    	};
                    	
                    	_el.hide();
                    	var parent_selector = [];
                    	$.each(parent, function(pkey, pval) {
                            parent_selector.push(':input[name="'+pkey+'"]');
                    	});
                    	parent_selector = parent_selector.join(', ', parent_selector);
                    	
                    	$(container).on('change', parent_selector, check_parent_state);
                    	
                    	setTimeout(function() {
                            check_parent_state.apply($(parent_selector).get(0));		
                    	}, 50);
                    }
                }
            });

            $fx_form.init_parent_change();
            
            if ($fx_form.to_change !== undefined && $fx_form.to_change) {
                var names = [];
                $fx_form.to_change.each(function(){
                    var nm = $(this).attr('name');
                    if ($.inArray(nm, names) === -1) {
                        names.push(nm);
                        $(this).change(); // hax, changle only one
                    }
                });
            }
            
            $.each ( $fx_form.to_click, function() {
                this.click();
            });
            
   
            if ( $("input[name='posting']").length > 0 && $fx.admin) {
                if ( $("input[name='essence']").length == 0) {
                    container.append('<input type="hidden" name="essence"  value="'+$fx.admin.get_essence()+'" />');
                }
                if ( $("input[name='action']").length == 0) {
                    container.append('<input type="hidden" name="action" value="'+$fx.admin.get_action()+'" />');
                }
                container.append('<input type="hidden" name="fx_admin" value="1" />');
                container.append('<input type="hidden" name="fx_token" value="'+$fx.g_data.main.token+'" />');
            }
            
            if ( settings.form_button ) {
                $.each(settings.form_button, function (key,value) {
                    if ( value == 'save' ) {
                        var form = $('#'+settings.form.id);
                        if ( $("input[name='essence']", form).length == 0) {
                            form.append('<input type="hidden" name="essence"  value="'+$fx.admin.get_essence()+'" />');
                        }
                        if ( $("input[name='action']", form).length == 0) {
                            form.append('<input type="hidden" name="action" value="'+$fx.admin.get_action()+'" />');
                        }
                        
                        form.append(
                        	'<input type="hidden" name="posting" value="1" />'+
                        	'<input type="hidden" name="fx_admin" value="1" />'+
                        	'<input type="hidden" name="fx_token" value="'+$fx.g_data.main.token+'" />'+
                        	'<input type="submit" value="Сохранить" />'
                        );
                        
                        var status_block = $("#fx_admin_status_block");
                        
                        $(form).submit(function() {
                        	$(".ui-state-error").removeClass("ui-state-error");
                            $(this).trigger('fx_form_submit');
                            
                            
                            $(this).ajaxSubmit(function ( data ) {
                                try {
                                    data = $.parseJSON( data );
                                }
                                catch(e) {
                                    status_block.show();
                                    status_block.writeError(data);
                                    return false;
                                }
                
                                if ( data.status == 'ok') {
                                    status_block.show();
                                    status_block.writeOk( data.text ? data.text : 'Ok');
                                    if ( data.fields) {
                                        $fx_form.draw_fields(data, $('#nc_dialog_error'));
                                    }
                                }
                                else {
                                    status_block.show();
                                    status_block.writeError(data['text']);
                                    for ( i in data.fields ) {
                                        $('[name="'+data.fields[i]+'"]').addClass("ui-state-error");
                                    }
                                }
                            }); 
 
                            return false;
                        });
                        // ctrl+shift+s
                        $('html').keydown( function(event){
                            if ( event.shiftKey && event.ctrlKey && event.keyCode == 83 ) {
                                $(form).submit();
                            }
                        });
                    }
                });
            }
            
            
            if ( settings.call_func ) {
                fx_call_user_func( '$fx.' + settings.call_func, '' );
            }
            
            $fx_form.add_elrte();
            $fx.panel.trigger('fx.fielddrawn');

        },
        
        preparation: function (settings, container, clear_container) {
            if ( clear_container === undefined ) clear_container = 1;
            if (settings.form === undefined) {
                settings.form = $fx_form.get_settings_from();
            }
            
            if ( settings.clear_previous_steps ) {
                $('.fx_step_content').remove();
            }
            
            var step = settings.step;
            if ( step ) {
                container.append('<div id="fx_step_'+step+'" class="fx_step_content"/>');
                container = $('#fx_step_'+step);
                var step_content = {
                    settings:settings, 
                    container:container
                };
                $fx_form.step_contents[step] = step_content;
                
                $fx_dialog.add_step(step);
            }
            
            if ( settings.dialog_button ) {
                $fx_dialog.add_extend_button( settings.dialog_button );
            }
            if ( settings.dialog_hide_button ) {
                $fx_dialog.hide_button(settings.dialog_hide_button);
            }
            if ( settings.dialog && settings.dialog.title ) {
                $fx_dialog.set_title(settings.dialog.title);
            }
     
            if ( $fx_form.main_content  ) {
                if ( settings.buttons ) {
                    $fx.buttons.draw_buttons(settings.buttons);
                }
                else {
                    $fx.buttons.draw_buttons();
                }
            }
                 
            if ( settings.essence ) {
                $fx.admin.set_essence(settings.essence);
            }
            
            if (typeof(container) == 'string') {
                container = $( (container[0] === '#' ? container : '#'+container) );
            }
            
            if  ( clear_container ) {
                $(container).html('');
            }
            
            
            return {
                settings:settings,  
                container:container
            };
        },
        
        init_tabs: function ( settings, container ) {
            $(container).append('<div id="fx_tabs"></div>');
            var cont = '';
            var _ul = $('<ul />');
            var i = 0;
            var active = 0;
            var keys = [];
            $.each(settings.tabs, function(key,val){
                if ( key == 'change_url') return true;
                keys.push(key);
                if ( val.active ) active = i;
                i++;
                $(_ul).append('<li><div class="fx_tab"><div class="fx_tab_corner"></div><a rel="'+key+'" href="#'+settings.form.id+'_'+key+'">'+(val.name !== undefined ? val.name : val)+'</a></div></li>');
                cont += '<div id="'+settings.form.id+'_'+key+'"></div>'
            });
            $('#fx_tabs', container).append(_ul, cont);
            $("#fx_tabs", container).tabs().tabs('select', active);
                
            if ( settings.tabs.change_url ) {
                $('.fx_tab a', container).click(function(){
                    var last_i = $fx.hash_param.length;
                    var find_key = false;
                    while ( last_i-- ) {
                        if ( $.inArray( $fx.hash_param[last_i], keys) > -1 ) {
                            $fx.hash_param[last_i] = $(this).attr('rel');
                            $fx.hash_param = $fx.hash_param.slice(0,last_i+1);   
                            find_key = true;
                            break;
                        }
                    }
                    if ( !find_key ) {
                        $fx.hash_param.push( $(this).attr('rel') );
                    }

                    window.location.hash = $fx.hash.join('.')+'('+$fx.hash_param.join(',')+')';
                });
            }
        },
        
        init_areas: function ( areas, container ) {
            $.each ( areas, function (key, area){
                var area_cont = $('<div id="'+ $fx_form.get_area_id(key)+'"/>');
                if ( area['class'] ) {
                    area_cont.addClass(area['class']);
                }
                if ( area.name ) {
                    area_cont.append('<b>'+area.name+'</b><br/>')
                }
                container.append(area_cont);
            });
        },
        
        get_area_id: function ( key ) {
            return 'fx_admin_area_'+key;
        },
        

        /**
     * @todo убрать повторяющиеся типы, проверка наличия функции
     */
        draw_field: function(json, container) {
            if (json.type === undefined) json['type'] = 'input';
 
            var label = '', type='';
            switch(json.type) {
                case 'hidden': case 'string': case 'short': case 'medium': case 'long': case 'int':
                    type = 'input';
                    break;
                case 'textarea': case 'text':
                    type = 'textarea';      
                    break;
                case 'bool':
                    type = 'checkbox';
                    break;
                default:
                    type = json.type;
                    break;
            }
            
            if (type === undefined) {
            	type = 'textarea';
            }
            
            label = fx_call_user_func('$fx_fields.'+type, json);
            if (label !== '') return label;
        },

        add_elrte: function() {
            $('textarea.fx_wysiwyg', $fx_dialog.main).elrte({
                toolbar: 'nc_maxi_toolbar', 
                lang:'ru', 
                height: 200
            });
        /*
            if ($('textarea.fx_code').length > 0) {
                //if ( typeof CodeMirror == 'undefined' ) {
                    $.getScript('/floxim/lib/codemirror/codemirror.all.js', function(){
                        $('textarea.fx_code').each( function(){
                            if ( $(this).data('editor_loader') ) {
                                return true;
                            }
                            
                            $(this).data('editor_loader', 1);
                            var editor = CodeMirror.fromTextArea(this, {
                                mode: "htmlmixed",
                                lineNumbers: true,
                                matchBrackets: true,
                                tabMode: "indent"
                            });
                        });
                    });
                //}
            }*/
           
                    
            
        /*
            if ($('textarea.fx_code').length > 0) {
                var init = function(name) {
                    editAreaLoader.init({
                        id:name,
                        language:'ru',
                        syntax: $('#'+name).data('syntax'),
                        allow_toggle:false,
                        start_highlight:true,
                        plugins: 'charmap',
                        charmap_default: 'Mathematical Operators',
                        display:'later',
                        toolbar:'go_to_line, search, fullscreen, |, undo, redo, |, syntax_selection,|, select_font, charmap,|, change_smooth_selection, highlight, reset_highlight,|, help',
                        EA_toggle_on_callback: '_edit_area_toggle_on_callback'
                    });
                }; 
                if (typeof editAreaLoader == 'undefined') {
                    $.getScript('/floxim/lib/edit_area/edit_area_full.js', function(){
                        editAreaLoader.window_loaded();
                    });
                }
               
                $.each( $('textarea.fx_code'), function () {
                    var textarea_id = $(this).attr('id');
                    $(this).before('<span style="float:right;"> Подсветка синтаксиса: <span class="dashed-link" onclick="_edit_area_show(\''+textarea_id+'\', this)">включить</span>; Перенос по словам: <span class="dashed-link" onclick="_edit_area_wordwrap(\''+textarea_id+'\', this)">выключить</span></span>');
                    init(textarea_id);
                });
                        
            }*/
        },
        
        update_available_buttons: function () {
            var btn, selected = $('.fx_admin_selected', '#fx_admin_content');
            var len = selected.length;
            

            if ( !len ) {
                btn = [];
            }
            else if ( len == 1 ) {
                btn = ['edit', 'settings','on','off', 'delete', 'rights', 'change_password', 'map', 'export', 'download'];
            }
            else {
                btn = ['on','off','delete'];
            }
            btn.push('add', 'upload', 'import', 'store');
            
            if ( len >= 1 ) {
                $.each (selected, function(k,v){
                   var  not_available_buttons = $(v).data('fx_not_available_buttons');
                   if ( not_available_buttons ) {
                       btn = array_diff(btn,not_available_buttons);
                   }
                });
            }
            $fx.buttons.set_active_buttons(btn);
        },
        
        show_loader: function () {
            if ( !$('.fx_ajax_loader').length && $fx_dialog.main) {
                $fx_dialog.main.append( $('<div />').addClass('fx_ajax_loader') );
            }    
        },
        
        hide_loader: function() {
            $('.fx_ajax_loader').remove();
        },
 
        init_parent_change: function () {  
            $.each($fx_form.df_change, function(i, input) {
                input = $(input);
                if ( input.data('events') !== undefined ) {
                    var len = input.data('events').change.length;
                }
                if (!len) {
                    input.change(function(){ 
                        var changed = $(this);
                        var sel_id = $fx_form.rid('nc_form_field_'+changed.attr('name')+'_'+changed.val()); 
                        
                        $('div[id^="nc_form_field_"]', $(this).closest('.nc_admin_group')).each(function(){
                            var flds = $('input, select, textarea', $(this));
                            if ( $(this).attr('id') != sel_id ) {
                                if ($(this).data('unactive')) $(this).hide();
                                flds.prop('disabled', true);
                            }
                            else {
                                if ($(this).data('unactive')) $(this).show();
                                flds.prop('disabled', false);
                            }

                        });
                    });
                //input.change();
                }

 
            });
        },
        
        rid: function ( str ) {
            return str.toString().replace('[', '_').replace(']', '_');
        },
        
        send_jsonlink: function (control_names, i) {
            $fx.post_front( fx_form.jsonlink_post[i], function (data){
                if (control_names) {
                    var _values = data['value'];
                    if (Object.prototype.toString.apply(_values) != '[object Array]') {  // проверка на массив
                        _values = new Array(_values);
                    }
                    $.each(control_names, function(k, v) {
                        var _value = _values.shift();
                        if ( typeof _value !== undefined ) {
                            var element = $('#'+v);
                            switch (element.attr('type')) {
                                case 'checkbox' :
                                    element.attr('checked', (_value ? 'checked' : null));
                                    break;
                                case 'radio' :
                                    $('#'+v+'_'+_value).attr('checked', 'checked' );
                                    break;
                                default:
                                    if (element.hasClass('fx_color')) {
                                        $('input[name='+v+']', element).val(_value);
                                        $('div.colorSelector2>div', element).css('backgroundColor', _value);
                                        $('div.colorpickerHolder2', element).ColorPickerSetColor(_value);
                                        break;
                                    }
                                    else {
                                        element.val(_value);
                                    }
                            }
                        }
                        else {
                            $fx.show_status_text('Ошибка: не удалось восстановить', 'error' );
                            return;
                        }
                    });
                }
            });
        }
        
    }
})(jQuery);

function _load(url)
{
    var e = document.createElement("script");
    e.src = url;
    e.type="text/javascript";
    document.getElementsByTagName("head")[0].appendChild(e); 
}

function _edit_area_show(editor_id, sender) {
    editAreaLoader.toggle(editor_id);
    if (editAreas[editor_id].displayed){
        sender.innerHTML = 'выключить';
        sender.parentNode.style.paddingBottom = '0px';
    }
    else {
        sender.innerHTML = 'включить';
        sender.parentNode.style.paddingBottom = '5px';
    }
}

function _edit_area_wordwrap(editor_id, sender) {
    if (document.getElementById(editor_id).getAttribute('wrap') == 'off'){
        document.getElementById(editor_id).removeAttribute('wrap');
        sender.innerHTML = 'выключить';
        if (editAreas[editor_id].displayed){
            document.getElementById('frame_'+editor_id).contentWindow.editArea.set_word_wrap(true);
        }
    }
    else {
        document.getElementById(editor_id).setAttribute('wrap', 'off');
        sender.innerHTML = 'включить';
        if (editAreas[editor_id].displayed){
            document.getElementById('frame_'+editor_id).contentWindow.editArea.set_word_wrap(false);
        }
    }
}

function _edit_area_toggle_on_callback(editor_id) {
    document.getElementById('frame_'+editor_id).contentWindow.editArea.set_word_wrap(document.getElementById(editor_id).getAttribute('wrap') != 'off');
}

window.fx_form = window.$fx_form = fx_form;