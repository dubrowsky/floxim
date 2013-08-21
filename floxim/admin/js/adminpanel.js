fx_adminpanel = {
    history:{},       
        
    KEYCODE_ESC: 27,
        
    init: function(options) {
        $fx.settings = options;    
        $fx.buttons_map = options.buttons.map;
        $fx.history = options.history;
        $fx.panel = $('#fx_admin_panel');
       
        $fx.init_form_function();
            
        $(function () {
            $fx.admin = false;
            $fx.buttons = new fx_buttons($fx.settings.buttons.source);
            
            $fx.main_menu = new fx_main_menu($fx.settings.mainmenu);
            $fx.main_menu.load();
                
            $fx.additional_menu = new fx_additional_menu();
            $fx.additional_menu.load();
                              
            $(window).hashchange($fx.set_mode);
            $(window).hashchange();
            $('html').on('click', '.fx_button', $fx.buttons.form_button_click);
            var ajax_counter = 0;
            $(document).ajaxSend(function() {
                ajax_counter++;
                if (ajax_counter == 1) {
                    $('.fx_preloader').css('visibility', 'visible');
                }
            });
            $(document).ajaxComplete(function() {
                ajax_counter--;
                if (ajax_counter == 0) {
                    $('.fx_preloader').css('visibility', 'hidden');
                }
            });
        });

        $(document).bind('keydown',$fx.key_down);
            
        $('html').click(function(){
            $fx.panel.trigger('fx.click', 'main');
        });
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
            
        /*
         * если оставить, проматывает страницу в начало при переходе в #page.view
        if ( window.location.hash == '#page.view' ) {
            window.location.hash = '';
        }
        */
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
            console.log('delete click handled');
            e.stopPropagation();
        }

        return true;
    },

    update_history: function () {
        return;
        
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
    
    reload:function(new_location) {
        $('html, body').html('').css({
            background:'#EEE',
            color:'#000', 
            margin:'30px 0', 
            font:'bold 22px arial',
            textAlign:'center'
        }).html('обновление страницы...');
        document.location.href = typeof new_location == 'string' ? new_location : document.location.href.replace(/#.*$/, '');  
    },
        
    post: function ( data, callback ) {
        data.fx_admin = true;
        if (!callback) {
            callback = function(data) {
                $fx_dialog.open_dialog(data);
            }
        }
        $.ajax({
            url: $fx.settings.action_link,
            type: "POST",
            data: data,
            dataType: "JSON",
            //async: false,
            success: [function(json) {
				if (json.reload) {
					$fx.reload(json.reload);
					return;
				}
                /*
				if ( json.history !==  undefined && json.history.undo  !==  undefined ) {
					$fx.history.undo = json.history.undo;
				}
				if ( json.history !==  undefined && json.history.redo  !==  undefined ) {
					$fx.history.redo = json.history.redo;
				}
				$fx.update_history();
                */
            },
            callback],
            error: function(jqXHR, textStatus, errorThrown) {
                if ( textStatus == 'parsererror') {
                    $fx.show_status_text( fx_lang('Ошибка сервера:') + jqXHR.responseText, 'error');
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
                $('#fx_dialog_form *').remove();
                //$('.ui-dialog-buttonset *').remove();
                $fx_dialog.main.dialog("option", "buttons", [] );
                $fx_form.draw_fields(data, $('#fx_dialog_form'));
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
                    id:'fx_dialog_form', 
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