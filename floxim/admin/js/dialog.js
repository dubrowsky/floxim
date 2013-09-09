(function($){
    fx_dialog = {

        settings: {
            height: 430, // this is the minimum height when there's enough space on the screen
            minHeight: 200, // this is the minimum height on a small screen
            width: '70%',
            modal: true,
            title: '',
            zIndex: 78887,
            resizable: false
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
            var main_cont = '#fx_dialog';
            $fx_dialog.main = $(main_cont);
            $fx_dialog.steps = [];

            // Dialog container is invisible (visibility: hidden) when it opens;
            // it becomes visible when it has 'fx_dialog_opened' class (visibility: visible).
            // That class is assigned to the dialog container in $fx_dialog.on_ready().
            // This is done to prevent flicker due to auto-size calculation.

            if ( data.dialog_not_auto_open ) {
                $fx_dialog.settings.autoOpen = false;
            }

            $fx_dialog.settings.open = $fx_dialog.open_listener;
            $fx_dialog.settings.close = $fx_dialog.close_listener;

            $fx_dialog.main.dialog($fx_dialog.settings);
            
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

            $fx_dialog.resize();
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

        resize: function() {
            var dialog_content = $fx_dialog.main,
                max_height = $(window).height() - 40;

            dialog_content.dialog("option", {
                height: "auto",         // let the jQuery UI do the height calculation
                maxHeight: max_height   // need this when this method is used as the window resize handler
            });

            setTimeout(function() {
                var settings = $fx_dialog.settings,
                    height = Math.max(
                        settings.minHeight,
                        Math.min(
                          max_height,
                          Math.max(
                              dialog_content.dialog("widget").outerHeight() + 1,
                              settings.height
                          )
                    )
                );

                // set fixed height and center the dialog
                dialog_content.dialog("option", { height: height, position: "center" });
                // ok, we can show the dialog now
                $fx_dialog.on_ready();
            }, 1); // there’s a cohesion with fx_form.add_parent_condition()   :(
        },

        // Dialog is "ready" to be shown after it's size was calculated in resize()
        on_ready: function() {
            var widget = $fx_dialog.main.dialog("widget");
            if (!widget.hasClass("fx_dialog_opened")) {
                // show the dialog container
                widget.addClass("fx_dialog_opened");
                // move focus to the dialog so it could be closed with an ESC key
                $('.ui-dialog-content').attr('tabindex', '-2');
                $('input:visible', $('.ui-dialog-content')).first().focus();
            }
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
            $(window).on("resize.fx_dialog", $fx_dialog.resize);
        },

        close_listener: function () {
            $fx_dialog.opened = false;
            $fx.panel.trigger('fx.dialogclose');
            if (typeof $fx_dialog.settings.onclose  === 'function') {
                $fx_dialog.settings.onclose();
            }
            $fx_dialog.main.dialog("widget").removeClass("fx_dialog_opened");
            $(window).off("resize.fx_dialog");
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

            if (!$fx_dialog.main) {
                return;
            }
            var buttons = $fx_dialog.main.dialog( "option", "buttons" );
            if ( !buttons || !buttons.length ) buttons = [];

            var button = {
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