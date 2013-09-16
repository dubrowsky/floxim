(function() {
    $fx.front_panel = {
        panel: null,
        second_row_height:37,
        show_form: function(data, params) {
            this.panel = $('#fx_admin_extra_panel');
            var p = this.panel;
            p.show();
            p.css('height', $fx.front_panel.second_row_height+'px');
            if (!data.form_button) {
                data.form_button = ['save'];
            }
            data.form_button.unshift('cancel');
            p.fx_create_form(data);
            var $form = $('form', p);
            $form.on('fx_form_cancel', function() {
                $fx.front_panel.hide();
            });
            $form.on('fx_form_ok', function() {
                console.log('form ok');
                $fx.front_panel.hide();
                if (params.onfinish) {
                    params.onfinish();
                }
            });
            p.css('opacity', 0.1).animate({opacity:1}, 300);
            var panel_height = $('form', p).height() + 10; // form with margins
            $('#fx_admin_control .editor_panel').hide();
            $fx.front_panel.animate_panel_height(panel_height);
        },
        animate_panel_height: function(panel_height) {
            var p = this.panel;
            var body_default_margin = $('body').data('fx_default_margin');
            if (!body_default_margin) {
                body_default_margin = parseInt($('body').css('margin-top'));
                $('body').data('fx_default_margin', body_default_margin);
            }

            var body_offset = 
                        body_default_margin 
                        + panel_height
                        - this.second_row_height; // height of admin panel's second line
            var height_delta = body_offset - parseInt($('body').css('margin-top'));
            p.animate({height: panel_height+'px'}, 300);

            $('body').animate(
                {'margin-top':body_offset + 'px'},
                300
            );
            $('.fx_outline_style_selected').animate({
                top: (height_delta > 0 ? '+=' : '-=')+ Math.abs(height_delta)
            }, 300);
        },
        load_form: function(form_options, params) {
            $fx.post(
                form_options, 
                function(json) {
                    $fx.front_panel.show_form(json, params)
                }
            );
        },
        hide: function() {
            this.animate_panel_height(this.second_row_height);
            var p = this.panel;
            p.animate({opacity:0},300, function () {
                p.hide();
                $('#fx_admin_control .editor_panel').show();
            });
        }
    };
})();