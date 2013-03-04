(function($){
    fx_dialog = {
        
        settings: {
            height: 430, //'auto',
            width: '70%',
            modal: true,
            title: '',
            zIndex: 78887
        },
        steps: [],
        
            
        open_dialog: function(data, main_cont, submit_handler) {
            if ( !main_cont ) {
                main_cont = '#fx_dialog';
            }
            $fx_dialog.main = $(main_cont);
            $fx_dialog.steps = [];
            
            if ( data.dialog_not_auto_open ) {
                $fx_dialog.settings.autoOpen = false;
            }
            
            $fx_dialog.settings.open = $fx_dialog.open_listener;
            $fx_dialog.settings.close = $fx_dialog.close_listener;
            
            $fx_dialog.main.dialog($fx_dialog.settings);
            
            var save_handler = $fx_dialog.click_save;
            if (submit_handler) {
                save_handler = function(e) {
                    submit_handler.apply($('form', $fx_dialog.main));
                }
            }
            
            $fx_dialog.main.dialog("option", "buttons", []);
            
            $fx_dialog.add_button("save", "Сохранить", save_handler);
            $fx_dialog.add_button("cancel", "Отменить", $fx_dialog.click_cancel);
            
            $fx_dialog.main.fx_create_form(data);
            
            $('form', $fx_dialog.main).submit( function(e) {
                if (submit_handler) {
                    submit_handler.apply(this, [e]);
                } else {
                    $fx_dialog.click_save();
                }
            	return false;
            });
            
            $("#nc_tabs", $fx_dialog.main).tabs();
            
            
        /*
            $fx.panel.bind('fx.fielddrawn', function () {
                // hack
                if ( $fx_dialog.steps.length == 2 && $('#fx_step_10').length > 0 ) {
                    $fx_dialog.add_button('back', "Вернуться", function(){
                        $fx_dialog.steps = [];
                        $fx_form.show_step(1);
                        // работает только для Store!
                        $("input[name=phase]", $fx_dialog.main).val(2);
                    });
                }
                else {
                    $fx_dialog.hide_button('back');
                }
            });*/
        },
        
        close: function() {
        	console.log('g_close');
        	this.click_cancel.apply($fx_dialog.main);
        },

        click_save: function () {
            $("textarea.fx_wysiwyg", $fx_dialog.main).elrte("updateSource");
            $('.ui-state-error', $fx_dialog.main).removeClass("ui-state-error");
            $("#nc_dialog_error").html('');
      
            // for debug
            //$('form', $fx_dialog.main).submit();
            $('form', $fx_dialog.main).trigger('fx_form_submit');
            console.log('call ajax submit');
            $('form', $fx_dialog.main).ajaxSubmit( $fx_dialog.form_sent );
      
            return false;
        },
        
        form_sent: function(data) {
        	if (typeof data === 'string') {
				try {
					data = $.parseJSON( data );
				} catch(e) {
					$("#nc_dialog_error", $fx_dialog.main).writeError(data);
					console.log('error parsing', data);
					return false;
				}
			}

			if ( data.step ) {
				/*
				$('.fx_step_content').hide();
				$fx_form.draw_fields(data, $('#nc_dialog_form'));
				console.log('fields drawn');
				$('form', $('#nc_dialog_form')).submit( function() {
					$fx_dialog.click_save();
					return false;
				});
				*/
				console.log('it s a dialog - close and open');
				$fx_dialog.close();
				$fx_dialog.open_dialog(data);
			}
			else if ( data.status == 'ok') {
				console.log('trigger ok');
				$fx.panel.trigger('fx.dialog.ok', data);
			}
			else {
				console.log('with errors');
				$("#nc_dialog_error", $fx_dialog.main).writeError(data['text']);
				if ( data.fields ) {
					$.each( data.fields, function(k,v){
						$('[name="'+v+'"]').addClass("ui-state-error");
					});
				}
			
				if ( data.errors ){ 
					var text_errors = [];
					$.each ( data.errors, function (k,v) {
						text_errors.push((typeof v == 'string') ? v : v.text);
						if ( v.field ) $('[name="'+v.field+'"]').addClass("ui-state-error");
					});
					$("#nc_dialog_error", $fx_dialog.main).writeError(text_errors);
				}
			}
        },
                                
        click_cancel: function() {
            $fx_dialog.opened = false;
            $(this).dialog('destroy'); 
            $fx_dialog.close_listener();
        },              

        open_listener: function () {
            if ( !$fx_dialog.opened ) {
                $fx_dialog.opened = true;
                $fx_dialog.button_disable('save');
            }
            $('.ui-widget-overlay').css('z-index', 10001);
        },
        
        close_listener: function () {
            $fx_dialog.opened = false;
            $fx.panel.trigger('fx.dialogclose');
            console.log('closing dialog');
        },
        
        button_disable: function (button) {
            var element = $fx_dialog.get_button(button);
            element.attr('disabled', 'disabled').css('opacity',0.3);   
        },
        
        button_enable: function (button) {
            var element = $fx_dialog.get_button(button);
            element.removeAttr('disabled').css('opacity',1);   
        },
        
        get_button: function ( button ) {
            return $('.fx_dialog_'+button+'_button');
        },
        
        add_button: function (key, text, callback ) {
            var exist_button = $fx_dialog.get_button(key);
            if ( exist_button.length ) {
                exist_button.show();
                $fx_dialog.rename_button(key, text);
                return false;
            }
            
            var buttons = $fx_dialog.main.dialog( "option", "buttons" );
            if ( !buttons || !buttons.length ) buttons = [];

            button = {
                text: text
            };
            button['class'] = "fx_dialog_"+key+"_button fx_dialog_button";
            button['click'] = callback;
            buttons.push(button);
            
            $fx_dialog.main.dialog("option", "buttons", buttons );
        },
        
        add_extend_button: function ( button ) { 
            if ( typeof button == 'object' && button.length >= 1 ) {
                $.each(button, function(k,v){
                    $fx_dialog.add_extend_button(v);
                });
            }
            else {
                $fx_dialog.add_button(button.key, button.text, function(){
                    $fx.click_extend_button(button.key, button);
                });
            }

        },
        
        // эмуляция нажатия "сохранить"
        submit: function () {
            $fx_dialog.get_button('save').click();
        },
        
        set_title: function ( title ) {
            $fx_dialog.main.dialog( "option", "title", title );
        },
        
        hide_button: function ( button ) {
            $fx_dialog.show_hide_button('hide', button);
        },
        
        show_button: function ( button ) {
            $fx_dialog.show_hide_button('show', button);
        },
        
        show_hide_button: function ( action, button ) {
            if ( typeof button == 'object' ) {
                $.each(button, function (k,v){
                    $fx_dialog.hide_button(v);
                });
                return false;
            } 

            var selector = button ? $fx_dialog.get_button(button) : $('.fx_dialog_button'); 
            if ( action == 'hide') {
                $(selector, $fx_dialog.main).hide();
            }
            else {
                $(selector, $fx_dialog.main).show();
            }
            
            return false;
        },
        
        add_step: function (step) {
            $fx_dialog.steps.push(step); 
            $fx_dialog.steps.unique();
        },
        
        rename_button : function (button, name) {
            $('span',$fx_dialog.get_button(button)).text(name);
        }
    }
    

    
})(jQuery);

window.fx_dialog = window.$fx_dialog = fx_dialog;