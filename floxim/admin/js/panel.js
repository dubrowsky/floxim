(function($) {
    
    function fx_front_panel() {
        var $body = $('#fx_admin_extra_panel .fx_admin_panel_body');
        var $footer = $('#fx_admin_extra_panel .fx_admin_panel_footer');
        var body_default_margin = null;
        
        this.show_form = function(data, params) {
            if (!params.view) {
                params.view = data.view ? data.view : 'vertical';
            }
            
            // disable hilight & select, hide node panel
            $fx.front.disable_hilight();
            $fx.front.disable_select();
            $fx.front.disable_node_panel();
            
            this.stop();
            $footer.html('').show();
            
            $body.css('height', '1px').show();
            
            if (params.view === 'horizontal') {
                $.each(data.fields, function(key, field) {
                    field.context = 'panel';
                });
            }
            
            if (!data.form_button) {
                data.form_button = ['save'];
            }
            data.form_button.unshift('cancel');
            data.class_name = 'fx_form_'+params.view;
            data.button_container = $footer;
            
            $form = $fx.form.create(data, $body);
            
            $form.on('fx_form_cancel', function() {
                if (params.oncancel) {
                    params.oncancel($form);
                }
                $fx.front_panel.hide();
            }).on('keydown', function(e) {
                if (e.which === 27) {
                    $form.trigger('fx_form_cancel');
                    return false;
                }
            }).on('fx_form_sent', function(e, data) {
                $fx.front_panel.hide();
                if (params.onfinish) {
                    params.onfinish(data);
                }
            });
            //setTimeout(function() {
                $fx.front_panel.animate_panel_height(null, function () {
                    $form.resize(function() {
                        $fx.front_panel.animate_panel_height();
                    });
                    $(':input:visible', $form).first().focus();
                    if (params.onready) {
                        params.onready($form);
                    }
                });
                
            //}, 100);
        };
        
        this.animate_panel_height = function(panel_height, callback) {
            if (this.is_moving) {
                return;
            }
            var max_height = Math.round(
                $(window).height() * 0.75
            );
            if (typeof panel_height === 'undefined' || panel_height === null) {
                var form_height = $('form', $body).outerHeight();
                panel_height = Math.min(form_height, max_height);
            }
            if (panel_height === $body.height()) {
                return;
            }
            if (body_default_margin === null) {
                body_default_margin = parseInt($('body').css('margin-top'));
            }
            var body_offset = body_default_margin + panel_height;
            
            var height_delta = body_offset - parseInt($('body').css('margin-top'));
            this.is_moving = true;
            var duration = 200;
            $body.animate(
                {height: panel_height+'px'}, 
                duration, 
                function() {
                    $fx.front_panel.is_moving = false;
                }
            );
            //p[0].scrollTop = 100;
            
            $('body').animate(
                {'margin-top':body_offset + 'px'},
                duration
            );
            $fx.front.get_front_overlay().animate({
                    top: (height_delta > 0 ? '+=' : '-=')+ Math.abs(height_delta)
                }, {
                    duration:duration,
                    complete: callback
            });
        };
        
        this.stop = function() {
            $body.stop(true,false);
            $('body').stop(true,false);
            $fx.front.get_front_overlay().stop(true,false);
            this.is_moving =  false;
        };
        this.load_form = function(form_options, params) {
            $fx.post(
                form_options, 
                function(json) {
                    $fx.front_panel.show_form(json, params);
                }
            );
        };
        this.hide = function() {
            //var p = this.panel;
            //var footer = this.footer;
            //$('form', p).remove();
            this.animate_panel_height(0, function () {
                $body.hide().html('');
                $footer.hide();
                $fx.front.enable_select();
                $fx.front.enable_node_panel();
            });
        };
        /*
        this.prepare_form_data = function(data) {
            var is_horizontal = this.params.view === 'horizontal';
            $.each(data.fields, function(key, field) {
                if (is_horizontal) {
                    field.context = 'panel';
                }
            });
        };
        */
    };
    
    $(function() {
        $fx.front_panel = new fx_front_panel();
    });
    
    return;
    $fx.front_panel = {
        second_row_height:37,
        panel: $('#fx_admin_extra_panel .fx_admin_panel_body'),
        footer: $('#fx_admin_extra_panel .fx_admin_panel_footer'),
        show_form: function(data, params) {
            if (!params.view) {
                params.view = 'vertical';
            }
            this.params = params;
            console.log(this.panel);
            
            // disable hilight & select, hide node panel
            $fx.front.disable_hilight();
            $fx.front.disable_select();
            $fx.front.disable_node_panel();
            
            this.prepare_form_data(data);
            
            this.stop();
            this.footer.html('').show();
            
            var p = this.panel;
            p.css('height', '1px');
            p.show();
            if (!data.form_button) {
                data.form_button = ['save'];
            }
            data.form_button.unshift('cancel');
            data.class_name = 'fx_form_'+params.view;
            data.button_container = this.footer;
            
            $form = $fx.form.create(data, p);
            
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
        animate_panel_height: function(panel_height, callback) {
            var p = this.panel;
            var max_height = Math.round(
                ($(window).height() - this.second_row_height) * 0.75
            );
            if (typeof panel_height === 'undefined' || panel_height === null) {
                var form = $('form', p);
                if (form.length > 0) {
                    var form_height = $('form', p).outerHeight();
                    if (form_height > max_height) {
                        form_height = max_height;
                    }
                    panel_height = form_height;
                } else{
                    panel_height = 0;
                }
            }
            var body_default_margin = $('body').data('fx_default_margin');
            if (!body_default_margin) {
                body_default_margin = parseInt($('body').css('margin-top'));
                $('body').data('fx_default_margin', body_default_margin);
            }
            
            var body_offset = body_default_margin + panel_height;
            
            var height_delta = body_offset - parseInt($('body').css('margin-top'));
            this._is_moving = true;
            var duration = 1000;
            p.animate(
                {height: panel_height+'px'}, 
                duration, 
                function() {
                    $fx.front_panel._is_moving = false;
                    //p.css('overflow', 'visible');
                }
            );
            p[0].scrollTop = 100;
            
            $('body').animate(
                {'margin-top':body_offset + 'px'},
                duration
            );
            $fx.front.get_front_overlay().animate({
                    top: (height_delta > 0 ? '+=' : '-=')+ Math.abs(height_delta)
                }, {
                    duration:duration,
                    complete: callback
            });
        },
        stop: function() {
            this.panel.parent().stop(true,false);
            $('body').stop(true,false);
            $fx.front.get_front_overlay().stop(true,false);
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
            this.animate_panel_height(0, function () {
                p.hide();
                footer.hide();
                $fx.front.enable_select();
                $fx.front.enable_node_panel();/*
                var node_panel = $fx.front.get_node_panel();
                if (node_panel !== null) {
                    node_panel.show();
                }*/
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
    console.log($fx.front_panel.panel.length, $('#fx_admin_extra_panel').length );
})($fxj);