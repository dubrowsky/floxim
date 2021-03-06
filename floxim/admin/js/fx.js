(function($) {
window.$fx = {
    history:{},       
        
    KEYCODE_ESC: 27,
        
    init: function(options) {
        $fx.settings = options;    
        $fx.buttons_map = options.buttons.map;
        $fx.history = options.history;
        $fx.panel = $('#fx_admin_panel');
       
        $(function () {
            $fx.admin = false;
            $fx.buttons = new fx_buttons($fx.settings.buttons.source);
            
            $fx.main_menu = new fx_main_menu($fx.settings.mainmenu);
            $fx.main_menu.load();
                
            $fx.additional_menu = new fx_additional_menu();
            $fx.additional_menu.load();
            $(window).hashchange($fx.set_mode);
            
            $(window).hashchange();
            
            if ($fx.mode === 'page') {
                $fx.front = new fx_front();
                var c_mode = $.cookie('fx_front_mode') || 'view';
                $fx.front.load(c_mode);
            }
            
            $('html').on('click', '.fx_button', $fx.buttons.form_button_click);
            var ajax_counter = 0;
            $(document).ajaxSend(function() {
                ajax_counter++;
                if (ajax_counter === 1) {
                    $('.fx_preloader').css('visibility', 'visible');
                }
            });
            $(document).ajaxComplete(function() {
                ajax_counter--;
                if (ajax_counter === 0) {
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
        if ( $fx.mode === 'admin' ) {
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
    },
        
    parse_hash: function() {
        var hash_to_parse = $fx.settings.hash !== undefined ? $fx.settings.hash : window.location.hash.slice(1);
        
        if (hash_to_parse === '' && window.location.pathname === '/floxim/') {
            hash_to_parse = 'admin.administrate.site.all';
        }
        
        var s_pos = hash_to_parse.indexOf('(');
            
        $fx.hash_param = [];
            
        if ( s_pos > 1 ) {
            $fx.hash_param = hash_to_parse.substr(s_pos).slice(1,-1).split(',');
            hash_to_parse = hash_to_parse.substr(0,s_pos);
        }
        $fx.hash = hash_to_parse.split('.');
          
        if ( $fx.hash[0] === '' || ($fx.hash[0] !== 'page' && $fx.hash[0] !== 'admin' )  ) {
            $fx.hash[0] = 'page';
        }
               
        if ( $fx.hash[1] === undefined || $fx.hash[1] === '' ) {
            $fx.hash[1] = 'view';
        }
        $fx.mode = $fx.hash[0]; // page or admin
    },

    draw_additional_text: function (data) {
        var text = data.text;
        $("#fx_admin_additionl_text").html(text).show();
        var current = 0;
        $("#fx_admin_additionl_text a").each(function(){
            var link = data.link[current];
            $(this).click(function(){
                if ( typeof link === 'function' ) {
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
        if ( e.keyCode === 46 ) {
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
        if ( status !== 'wait') {
            //window.setTimeout(function() {
                //$("#fx_admin_statustext").fadeOut('slow');
            //}, 10000);
        }
    },
    
    reload:function(new_location) {
        if (/^#/.test(new_location)) {
            document.location.hash = new_location.replace(/^#/, '');
            return;
        }
        $('html, body').html('').css({
            background:'#EEE',
            color:'#000', 
            margin:'30px 0', 
            font:'bold 22px arial',
            textAlign:'center'
        }).html('reloading...');
        document.location.href = typeof new_location === 'string' ? new_location : document.location.href.replace(/#.*$/, '');  
    },
        
    post: function ( data, callback ) {
        data.fx_admin = true;
        if (!callback) {
            callback = function(data) {
                /*
                if (data.location) {
                    
                }
                console.log('data caught', data);
                */
            };
        }
        $.ajax({
            url: $fx.settings.action_link,
            type: "POST",
            data: data,
            dataType: "JSON",
            //async: false,
            success: [
                function(json) {
                    if (json.reload) {
                        $fx.reload(json.reload);
                        return;
                    }
                },
                callback
            ],
            error: function(jqXHR, textStatus, errorThrown) {
                if ( textStatus === 'parsererror') {
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
        if (button.post) {
            $fx.post(button.post, function(data) {
                $('#fx_dialog_form *').remove();
                $fx_dialog.main.dialog("option", "buttons", [] );
                $fx_form.draw_fields(data, $('#fx_dialog_form'));
            });
            return;
        }
        if (button.act_as === 'save') {
            $('form', $fx_dialog.main).append(
                '<input type="hidden" name="fx_dialog_button" value="'+button_key+'" />'
            );
            $fx_dialog.click_save();
        }
    }
};
})($fxj);