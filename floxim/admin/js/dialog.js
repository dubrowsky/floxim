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


        open_dialog: function(data, settings) {
            settings = $.extend({
                onsubmit:function(e) {
                    $fx_dialog.click_save.apply($fx_dialog, e);
                },
                onfinish:null
            }, settings);
            $fx_dialog.settings = $.extend($fx_dialog.settings, settings);
            main_cont = '#fx_dialog';
            $fx_dialog.main = $(main_cont);
            $fx_dialog.steps = [];

            if ( data.dialog_not_auto_open ) {
                $fx_dialog.settings.autoOpen = false;
            }

            $fx_dialog.settings.open = $fx_dialog.open_listener;
            $fx_dialog.settings.close = $fx_dialog.close_listener;

            $fx_dialog.main.dialog($fx_dialog.settings);

//            var widget = $fx_dialog.main.dialog("widget"),
//                widget_width = widget.width(),
//                $window = $(window),
//                min_gap = 50;
//
//            widget.draggable("option",
//                             "containment",    // set to "window" to prevent dragging outside the screen borders
//                             [
//                                 -widget_width + min_gap,
//                                 0,
//                                 $window.width() - min_gap,
//                                 $window.height() - min_gap
//                             ])
//                  .draggable("option", "scroll", false);

            $fx_dialog.main.dialog("widget")
                  .draggable("option", "containment", "window")
                  .draggable("option", "scroll", false);

            $fx_dialog.main.closest('.ui-dialog').addClass('fx_overlay');

            $fx_dialog.main.dialog("option", "buttons", []);

            $fx_dialog.add_button("save", fx_lang("Сохранить"), $fx_dialog.settings.onsubmit);
            $fx_dialog.add_button("cancel", fx_lang("Отменить"), $fx_dialog.close);

            if ( data.dialog_title ) {
                $fx_dialog.set_title(data.dialog_title);
            }

            $fx_dialog.main.fx_create_form(data);

            $('form', $fx_dialog.main).submit( $fx_dialog.settings.onsubmit );

            $("#nc_tabs", $fx_dialog.main).tabs();
        },

        close: function() {
            // таймаут - чтобы события на кнопках срабатывали на .fx_overlay
            setTimeout(function() {
                $fx_dialog.opened = false;
                $fx_dialog.main.dialog('close');
                $fx_dialog.close_listener();
            }, 50);
        },

        click_save: function () {
            $('.ui-state-error', $fx_dialog.main).removeClass("ui-state-error");
            $("#nc_dialog_error").html('');
            $('form', $fx_dialog.main).trigger('fx_form_submit');
            $('form', $fx_dialog.main).ajaxSubmit( $fx_dialog.form_sent );
            return false;
        },

        form_sent: function(data) {
            if (typeof data === 'string') {
                try {
                    data = $.parseJSON( data );
                } catch(e) {
                    $("#nc_dialog_error", $fx_dialog.main).writeError(data);
                    return false;
                }
            }

            if (data.reload) {
                $fx.reload(data.reload);
                return false;
            }

            if ( data.step ) {
                var prev_closer = $fx_dialog.settings.onclose;
                $fx_dialog.settings.onclose = function() {
                    $fx_dialog.settings.onclose = prev_closer;
                    $fx_dialog.open_dialog(data, $fx_dialog.settings);
                }
                $fx_dialog.close();
            }
            else if ( data.status == 'ok') {
                if (typeof $fx_dialog.settings.onfinish === 'function') {
                    $fx_dialog.settings.onfinish(data);
                }
                $fx_dialog.main.dialog('close');
            }
            else {
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

        open_listener: function () {
            if ( !$fx_dialog.opened ) {
                $fx_dialog.opened = true;
                $fx_dialog.button_disable('save');
            }
            $('.ui-widget-overlay').css('z-index', 10001);
            // move focus to the dialog so it could be closed with an ESC key
            $('.ui-dialog').focus();
        },

        close_listener: function () {
            $fx_dialog.opened = false;
            $fx.panel.trigger('fx.dialogclose');
            if (typeof $fx_dialog.settings.onclose  === 'function') {
                $fx_dialog.settings.onclose();
            }
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