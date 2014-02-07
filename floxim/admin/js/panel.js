(function() {
    $fx.front_panel = {
        panel: null,
        second_row_height:37,
        show_form: function(data, params) {
            if (!params.view) {
                params.view = 'vertical';
            }
            this.params = params;
            
            // disable hilight & select, hide node panel
            $fx.front.disable_hilight();
            $fx.front.disable_select();
            var node_panel = $fx.front.get_node_panel()
            if (node_panel !== null)
                node_panel.hide();
            
            this.prepare_form_data(data);
            this.panel = $('#fx_admin_extra_panel .fx_admin_panel_body');
            this.footer = $('#fx_admin_extra_panel .fx_admin_panel_footer');
            
            this.stop();
            
            this.footer
                    .html('')
                    .css({
                        overflow: 'hidden',
                        'border-top':'1px solid #CCC',
                        padding:'3px'
                    })
                    .show();
            
            var p = this.panel;
            p.css('overflow', 'auto');
            p.parent().css({height:'1px',overflow:'hidden'});
            p.show();
            if (!data.form_button) {
                data.form_button = ['save'];
            }
            data.form_button.unshift('cancel');
            data.class_name = 'fx_form_'+params.view;
            data.button_container = this.footer;
            p.fx_create_form(data);
            var $form = $('form', p);
            $form.on('fx_form_cancel', function() {
                if (params.oncancel) {
                    params.oncancel($form);
                }
                $fx.front_panel.hide();
            });
            this.panel.on('keydown', function(e) {
                if (e.which === 27) {
                    $form.trigger('fx_form_cancel');
                    return false;
                }
            });
            $form.on('fx_form_sent', function(e, data) {
                $fx.front_panel.hide();
                if (params.onfinish) {
                    params.onfinish(data);
                }
            });
            p.css('opacity', 0.1).animate({opacity:1}, 300);
            $('#fx_admin_control .editor_panel').hide();
            setTimeout(function() {
                $fx.front_panel.animate_panel_height();
                $(':input:visible', $form).first().focus();
                if (params.onready) {
                    params.onready($form);
                }
                $('form', p).resize(function() {
                    if ($fx.front_panel._is_moving) {
                        return;
                    }
                    $fx.front_panel.animate_panel_height();
                });
            }, 100);
        },
        animate_panel_height: function(panel_height) {
            var p = this.panel.parent();
            var footer_height = this.footer.outerHeight();
            var max_height = Math.round(
                ($(window).height() - this.second_row_height - footer_height) * 0.75
            );
            if (typeof panel_height === 'undefined') {
                var form = $('form', p);
                if (form.length > 0) {
                    var form_height = $('form', p).outerHeight();
                    if (form_height > max_height) {
                        form_height = max_height;
                    }
                    panel_height = form_height + footer_height;
                    this.panel.css('height', form_height);
                } else{
                    panel_height = 0; //this.second_row_height;
                }
            }
            var body_default_margin = $('body').data('fx_default_margin');
            if (!body_default_margin) {
                body_default_margin = parseInt($('body').css('margin-top'));
                $('body').data('fx_default_margin', body_default_margin);
            }
            
            var body_offset = body_default_margin + panel_height;
            if (panel_height > 0) {
                body_offset -= this.second_row_height;
            }
            var height_delta = body_offset - parseInt($('body').css('margin-top'));
            this._is_moving = true;
            p.animate({height: panel_height+'px'}, 300, function() {
                $fx.front_panel._is_moving = false;
            });

            $('body').animate(
                {'margin-top':body_offset + 'px'},
                300
            );
            //$('.fx_outline_style_selected').animate({
            //    top: (height_delta > 0 ? '+=' : '-=')+ Math.abs(height_delta)
            //}, 300);
            $('.panel_overlay').animate({
                top: (height_delta > 0 ? '+=' : '-=')+ Math.abs(height_delta)
            }, 300);
        },
        stop: function() {
            this.panel.stop(1,1);
            $('body').stop(1,1);
            $('.fx_outline_style_selected').stop(1,1);
        },
        load_form: function(form_options, params) {
            $fx.post(
                form_options, 
                function(json) {
                    $fx.front_panel.show_form(json, params);
                }
            );
        },
        hide: function() {
            var p = this.panel;
            var footer = this.footer;
            $('form', p).remove();
            this.animate_panel_height();
            p.animate({opacity:0},300, function () {
                p.hide();
                footer.hide();
                $fx.front.enable_select();
                var node_panel = $fx.front.get_node_panel()
                if (node_panel !== null)
                    node_panel.show();
            });
        },
        prepare_form_data: function(data) {
            var is_horizontal = this.params.view === 'horizontal';
            $.each(data.fields, function(key, field) {
                if (is_horizontal) {
                    field.context = 'panel';
                }
            });
        }
    };
})();