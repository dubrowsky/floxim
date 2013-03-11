fx_adminpanel = {
    g_data:{},    
    history:{},       
        
    KEYCODE_ESC: 27,
        
    set_data: function(data) {
        $.extend(true, $fx.g_data, data);
    },
        
    init: function(options) {
        $fx.settings = options;
        $fx.settings['elrte_toolbar'] = ['style', 'colors', 'alignment', 'links'];
        $fx.settings['elrte_maxi_toolbar'] = ['copypaste', 'undoredo', 'elfinder', 'style', 'alignment', 'direction', 'colors', 'format', 'indent', 'lists', 'media', 'tables'];
            
        $fx.buttons_map = options.buttons.map;
        $fx.history = options.history;
        $fx.panel = $('#fx_admin_panel');
       
        $fx.init_form_function();
            
        $(function () {
            elRTE.prototype.options.panels.nc_panel = ['horizontalrule', 'blockquote', 'stopfloat', 'smiley'];
            elRTE.prototype.options.toolbars.fx_einp_toolbar = $fx.settings.elrte_toolbar;
            elRTE.prototype.options.toolbars.nc_maxi_toolbar = ['copypaste', 'undoredo', 'elfinder', 'style', 'alignment', 'direction', 'colors', 'format', 'indent', 'lists', 'media', 'tables', 'nc_panel'];
                            
            
            $fx.admin = false;
            $fx.sort = new fx_sort();
            $fx.buttons = new fx_buttons($fx.settings.buttons.source);
                
            $fx.settings.mainmenu = {
                manage: {
                    name:'управление',
                    key: 'manage',
                    href: '/floxim/#admin.administrate.site.all'
                }, 
                develop : {
                    name:'разработка',
                    key :'develop',
                    href: '/floxim/#admin.component.group'
                }, 
                site : {
                    name: 'сайт',
                    key: 'site',
                    href: '/'
                }
            };
            $fx.main_menu = new fx_main_menu($fx.settings.mainmenu);
            $fx.main_menu.load();
                
            $fx.additional_menu = new fx_additional_menu();
            $fx.additional_menu.load();
                              
            $(window).hashchange($fx.set_mode);
            $(window).hashchange();

        });

        $(document).bind('keydown',$fx.key_down);
            
        $('html').click(function(){
            $fx.panel.trigger('fx.click', 'main');
        });
        $.fn.generate_selector = function(parent) {
            if (typeof(parent) == 'undefined') {
                parent = document;
            } else if (parent instanceof $) {
                parent = parent.get(0);
            }
            var selector = [];
            var node = this.first();
            while (node.length > 0 && node.get(0) !== parent) {
                selector.unshift(':nth-child(' + (node.index() + 1)+ ')');
                node = node.parent();
            }
            return selector.join(' '); 
        };
        $.fn.descendant_or_self = function(selector) {
            return this.find(selector).add( this.filter(selector));
        };
    },

    set_mode: function() {
        $fx.admin_buttons_action = {};
        $fx.parse_hash();
        $fx.panel.trigger('fx.startsetmode');
            
        // admin
        if ( $fx.mode == 'admin' ) {
            if ( !$fx.admin ) {
                $fx.admin = new fx_admin();
            }
            var len = $fx.hash.length;
            if ( len > 2 ) {
                $fx.admin.set_essence($fx.hash[len-2]);
                $fx.admin.set_action($fx.hash[len-1]);
            }
            $fx.admin.load();
        }
        else {
            if ( !$fx.front) {
                $fx.front = new fx_front();
            }
            $fx.front.load($fx.hash[1]);
            document.body.className = document.body.className.replace(/fx_mode_[^\s]+/, '');
            $(document.body).addClass('fx_mode_'+$fx.hash.join('_'));
        }
    },
        
    parse_hash: function() {
        var hash_to_parse = $fx.settings.hash != undefined ? $fx.settings.hash : window.location.hash.slice(1);
        
        if (hash_to_parse == '' && window.location.pathname == '/floxim/') {
            hash_to_parse = 'admin.administrate.site.all';
        }
        
        var s_pos = hash_to_parse.indexOf('(');
            
        $fx.hash_param = [];
            
        if ( s_pos > 1 ) {
            $fx.hash_param = hash_to_parse.substr(s_pos).slice(1,-1).split(',');
            hash_to_parse = hash_to_parse.substr(0,s_pos);
        }
        $fx.hash = hash_to_parse.split('.');
          
        if ( $fx.hash[0] == '' || ($fx.hash[0] != 'page' && $fx.hash[0] != 'admin' )  ) {
            $fx.hash[0] = 'page';
        }
               
        if ( $fx.hash[1] === undefined || $fx.hash[1] == '' ) {
            $fx.hash[1] = 'view';
        }
        $fx.mode = $fx.hash[0]; // page or admin
              
        if ( window.location.hash == '#page.view' ) {
            window.location.hash = '';
        }
    },

    handle_button: function(action, options, from_pulldown ) {
        // К удалению!!!
        return;
        //console.log('handle_button', arguments);
        if ( options === undefined ) options = {};
        
        // чтоб можно было вызывать так:
        // handle_button({essence:'template',action:'set_preview'});
        if (typeof action == 'object' && action.action) {
        	options = action;
        	action = action.action;
        }
        
        var reload = false;
            
        var selected = $('.fx_admin_selected');
        var key = selected.data('key');
        var type = selected.data('type');

        var id = [];
        if (selected.length == 1) {
            id = selected.data('id');
        } else {
            selected.each(function(){
                id.push($(this).data('id'));
            });       
        }
        
        if (action == 'delete' && $fx.admin.get_essence && $fx.admin.get_essence() == 'site' && prompt('Если точно хотите удалить сайт, впечатайте "delete":') !== 'delete') {
            return false;
	}
            
        if ( action == 'on' || action == 'off' || action == 'delete') {
            options['posting'] = 1;
        }
            
        if ( action == 'on') {
            selected.removeClass('fx_admin_unchecked');
        }
        if ( action == 'off' ) {
            selected.addClass('fx_admin_unchecked');
            if ( $fx.mode == 'page' ) {
                selected.addClass('fx_admin_unchecked fx_admin_unchecked_'+$fx.hash[1]); 
            }
        }
        if ( action == 'delete' ) {
            selected.remove();
        }
                                                   
        if ($fx.mode == 'admin') {  
            options['id'] = id;
            options['essence'] = $fx.admin.get_essence();
            if ( options.action === undefined ) {
                options['action'] = action;
            } 
                  
        }
        else if ($fx.mode == 'page') {
            switch(action) {
                case 'select_block' :
                    $fx.front.select_level_up();
                    return false;
                    /*
                    selected.removeClass('fx_admin_selected');
                    $('.'+$fx.g_data.blocks[selected.data('key')].parent).addClass('fx_admin_selected');
                    $fx.front.update_view();
                    return false;
                    */
                    break;
                case 'add':
                    if ( !from_pulldown ) {
                        $fx.attempt_to_add(selected);
                        return false;
                    }

                    break;


                case 'edit': case 'on': case 'off': case 'delete':
                    var ids = [];
                    var fl = false;

                    $.extend(true, options, $fx.g_data.blocks[key].post);

                    if ( action != 'edit') {
                        $fx.show_status_text('Сохранение...', 'wait');
                    }
                        
                    $('.fx_admin_selected').removeClass('fx_admin_selected');
                    if ( action != 'delete' ) {
                        $fx.front.update_view();
                    }

                    break;
                case 'settings':
                    return;
                    if ( type == 'block') {
                        $.extend(true, options, $fx.g_data.blocks[key].post);
                    //options['infoblock_info'] = $fx.g_data.infoblock[options.infoblock];
                    }
                    else {
                        $.extend(true, options, $fx.g_data.fields[key]);
                    }
                    break;

                case 'page_settings': case 'design_settings': case 'page_rights':
                    options['essence']  = 'subdivision';
                    options['id']  = $('body').data('sub');
                    break;
                        
                case 'undo': case 'redo':
                    options['essence'] = 'history';
                    reload = true;
                    break;
       
             
            }
            options['action'] = action;
        }
       
        $fx.post(options, function(json){
            if ( reload ) {
                window.location.reload();
            }
            if ( json.status !== undefined) {
                $fx.show_status_text(json.text, json.status);
                if ( json.history !==  undefined && json.history.undo  !==  undefined ) $fx.history.undo = json.history.undo;
                if ( json.history !==  undefined && json.history.redo  !==  undefined ) $fx.history.redo = json.history.redo;
                $fx.update_history();
            }
            else {
                $fx_dialog.open_dialog(json);
            }
        });

        
    },

    attempt_to_add: function ( selected ) {
        if ( $fx.hash[1] == 'design' ) {
            $fx.attempt_to_add_design(selected);
        }
    },
           
    attempt_to_add_design: function ( selected ) {
        $fx.handle_button('add', {action:'add',essence:'infoblock'}, true);
        return;
        if ( selected.length ) {
            if ( $fx.g_data.addition_block ) {
                $.each ( $fx.g_data.addition_block, function (key, block) {
                    if ( block.mode && block.mode == $fx.hash[1] ) {
                        if ( block.decent_parent && selected.hasClass(block.decent_parent) ) {
                            var post_data = {};

                            $.extend(true, post_data, block.post);
                            post_data.subdivision_id = $('body').data('sub'); 
                            if ( !post_data.action ) {
                                post_data.action = 'add';
                            }
                            $fx.handle_button('add', post_data, true);
                        }
                    }
                });
            }
                
                
        }
       else {
        
            $fx.buttons.hide_panel();

            var text = {};
            text.text  = 'Выберите месторазмещение или <a>отменить</a>';
            text.link = [$fx.stop_add_mode];
            $fx.draw_additional_text(text);

            $('html').one( 'keydown',  function(e) {
                if ( e.keyCode == $fx.KEYCODE_ESC ) {
                    $fx.stop_add_mode();
                }
            }); 

            $fx.panel.one('fx.startsetmode', $fx.stop_add_mode);

            if ( $fx.g_data.addition_block ) {
                $.each ( $fx.g_data.addition_block, function (key, block) {
                    var preview;
                    if ( block.mode && block.mode == $fx.hash[1] ) {
                        preview = $(block.preview).addClass('fx_preview_placeholder').appendTo(block.preview_parent);
                        preview.bind('mouseenter mouseleave', function(){
                            preview.toggleClass('fx_preview_placeholder_hover');
                        });

                        preview.click(function(){
                            var post_data = {};

                            $.extend(true, post_data, block.post);
                            post_data.subdivision_id = $('body').data('sub'); 
                            if ( !post_data.action ) {
                                post_data.action = 'add';
                            }
                            $fx.stop_add_mode();
                            $fx.handle_button('add', post_data, true);
                        });
                    }  
                });
            }
       }
    },

    draw_additional_text: function (data) {
        var text = data.text;
        $("#fx_admin_additionl_text").html(text).show();
        var current = 0;
        $("#fx_admin_additionl_text a").each(function(){
            var link = data.link[current];
            $(this).click(function(){
                if ( typeof link == 'function' ) {
                    link();
                }
                else {
                    $fx.post(link);
                }
                return false;
            });
                
            current++;
        });
            
    },
    
    draw_additional_panel: function (data) {
    	$("#fx_admin_buttons").fx_create_form(data);
    },
        
    clear_additional_text: function ( ) {
        $("#fx_admin_additionl_text").html('');
    },
          
    key_down: function ( e ) {
        if ( e.keyCode == 46 ) {
            if ( $('.fx_admin_selected').length > 0 ) $fx.handle_button('delete');
            e.stopPropagation();
        }

        return true;
    },

    update_history: function () {
        if ( $fx.history.undo !== undefined && $fx.history.undo.length > 0 ) {
            $fx.buttons.update_button('undo', {
                'available' : true,
                'title': $fx.history.undo
            } );
        }
        else {
            $fx.buttons.update_button('undo', {
                'available' : false,
                'title': ''
            } );
        }
        if ( $fx.history.redo !== undefined && $fx.history.redo.length > 0 ) {
            $fx.buttons.update_button('redo', {
                'available' : true,
                'title': $fx.history.redo
            } );
        }
        else {
            $fx.buttons.update_button('redo', {
                'available' : false,
                'title': ''
            } );
        }
    },
    
    show_status_text: function ( text, status ) { 
        $("#fx_admin_statustext").removeClass();
        $("#fx_admin_statustext").html("<span>"+text+"</span>").addClass(status).fadeIn('slow');
        if ( status != 'wait') {
            window.setTimeout(function() {
                $("#fx_admin_statustext").fadeOut('slow');
            }, 10000);
        }
    },
        
    post: function ( data, callback ) {
        data.fx_admin = true;
        data.fx_token = $fx.g_data.main.token;
        $.ajax({
            url: $fx.settings.action_link,
            type: "POST",
            data: data,
            dataType: "JSON",
            async: false,
            success: [function(json) {
				if (json.reload) {
					$('html, body').html('').css({
						background:'#EEE',
						color:'#000', 
						margin:'30px 0', 
						font:'bold 22px arial',
						textAlign:'center'
					}).html('обновление страницы...');
					document.location.href = typeof json.reload == 'string' ? json.reload : document.location.href.replace(/#.*$/, '');
					return;
				}
				if ( json.history !==  undefined && json.history.undo  !==  undefined ) {
					$fx.history.undo = json.history.undo;
				}
				if ( json.history !==  undefined && json.history.redo  !==  undefined ) {
					$fx.history.redo = json.history.redo;
				}
				$fx.update_history();
            },
            callback],
            error: function(jqXHR, textStatus, errorThrown) {
                if ( textStatus == 'parsererror') {
                    $fx.show_status_text('Ошибка сервера:' + jqXHR.responseText, 'error');
                }
                return false;
            }  
        });
    },
    
    post_front: function (data, callback  ) {
        if ( data.posting === undefined ) {
            data.posting = 1;
        }
        if ( !data.action ) data.action = 'edit';
        
        $fx.show_status_text('Сохранение...', 'wait');
            
        $fx.post(data, function(json){
            $fx.show_status_text(json.text, json.status);
            if ( callback ) {
                callback(json);
            }  
        });
    },
    
    regexp: function(regexp, data) {
        return regexp.exec(data);
    },
            
    force_submit_form: function () {
        $fx_dialog.submit();  
    }, 
        
    stop_add_mode: function () {
        $fx.buttons.show_panel();
        $fx.clear_additional_text();
        $('.fx_preview_placeholder').remove();
    },
              
    click_extend_button: function (button_key, button) {
        if ( button_key == 'store') {
            $fx.show_store();
        } else if (button.post) {
            $fx.post(button.post, function(data) {
                $('#nc_dialog_form *').remove();
                //$('.ui-dialog-buttonset *').remove();
                $fx_dialog.main.dialog("option", "buttons", [] );
                $fx_form.draw_fields(data, $('#nc_dialog_form'));
            });
        } else if (button.act_as && button.act_as == 'save') {
            $('form', $fx_dialog.main).append(
                '<input type="hidden" name="fx_dialog_button" value="'+button_key+'" />'
            );
            $fx_dialog.click_save();
        }
    },
        
    show_store: function () {
        $("input[name=phase]", $fx_dialog.main).val(7);
        $("input[name=posting]", $fx_dialog.main).val(0);
        $fx_dialog.submit();
        $fx.panel.bind("fx.fielddrawn", $fx.store_hide_button);  
    },
        
    store_hide_button: function () {
        $fx_dialog.hide_button(['save','store']);
        $fx.panel.unbind("fx.fielddrawn", $fx.store_hide_button); 
            
    },
        
    store_after_install: function ( store_id ) {
        $("input[name=phase]", $fx_dialog.main).val(1);
        $fx_dialog.submit();
        $('#fx_step_1').remove();
        $('#fx_step_10').remove();
    },
        
    init_form_function: function () {
        $.fn.fx_create_form = function(options, main_content) {
        	var settings = {
                form: {
                    id:'nc_dialog_form', 
                    action:$fx.settings.action_link, 
                    target:'nc_upload_target'
                }
            };
            if (options) {
                $.extend(true, settings, options);
            }

            var _form = $('<form class="fx_admin_form" id="'+settings.form.id+'" action="'+settings.form.action+'" enctype="multipart/form-data" method="post" target="'+settings.form.target+'" />');
            $(_form).append('<iframe id="'+settings.form.target+'" name="'+settings.form.target+'" style="display:none;"></iframe><div id="nc_warn_text"></div>');
            this.html('<div id="nc_dialog_error"/>');
            this.append(_form);
            $fx_form.draw_fields(settings, _form, main_content);
            $fx_form.add_elrte();
            
            if (options.buttons_essence) {
            	$fx.admin.set_essence(options.buttons_essence);
            }
            
            return this;
        };

        $.fn.writeError = function(message){
            if ( typeof message == 'string' ) message = [message];
            return this.each(function(){
                var $this = $(this);

                var errorHtml = "<div class=\"ui-widget\">";
                errorHtml+= "<div class=\"ui-state-error ui-corner-all\" style=\"padding: 0 .7em;\">";
                errorHtml+= "<p>";
                errorHtml+= "<span class=\"ui-icon ui-icon-alert\" style=\"float:left; margin-right: .3em;\"></span>";
                errorHtml+= message.join('<br/>');
                errorHtml+= "</p>";
                errorHtml+= "</div>";
                errorHtml+= "</div>";

                $this.html(errorHtml);
            });
        }

        $.fn.writeAlert = function(message){
            return this.each(function(){
                var $this = $(this);

                var alertHtml = "<div class=\"ui-widget\">";
                alertHtml+= "<div class=\"ui-state-highlight ui-corner-all\" style=\"padding: 0 .7em;\">";
                alertHtml+= "<p>";
                alertHtml+= "<span class=\"ui-icon ui-icon-info\" style=\"float:left; margin-right: .3em;\"></span>";
                alertHtml+= message;
                alertHtml+= "</p>";
                alertHtml+= "</div>";
                alertHtml+= "</div>";

                $this.html(alertHtml);
            });
        }
            
            
        $.fn.writeOk = function(message){
            return this.each(function(){
                var $this = $(this);

                var alertHtml = "<div class=\"ui-widget\">";
                alertHtml+= "<div class=\"ui-state-highlight ui-corner-all\" style=\"padding: 0 .7em;\"><p>";
                alertHtml+= message;
                alertHtml+= "</p></div></div>";
                $this.html(alertHtml);
                
                setTimeout(function(){
                    $this.fadeOut('normal');
                }, 2000);
            });
        }
    }

}

window.$fx = fx_adminpanel;