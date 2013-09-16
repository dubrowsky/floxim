(function($){
fx_form = {
    get_settings_from: function () {
        return {
            id:'fx_dialog_form', 
            action:$fx.settings.action_link,
            target:'nc_upload_target'
        };
    },
            
    draw_fields: function(settings, $form_node) {
        if (settings.fields === undefined) {
            return;
        }

        if (settings.tabs) {
            $fx_form.init_tabs(settings, $form_node);
        }
        
        if ($fx.mode !== 'page') {
            $fx.buttons.draw_buttons(settings.buttons);
        }

        $.each(settings.fields, function(i, json) {        
            var target = json.tab
                            ? $('#'+settings.form.id+'_'+json.tab, $form_node)
                            : $form_node;
            $fx_form.draw_field(json, target);
        });
        
        if (typeof settings.form_button === 'undefined') {
            settings.form_button = [];
        }
        var submit_added = false;
        $.each(settings.form_button, function (key,options) {
            if (typeof options === 'string') {
                options = {key:options};
            }
            options.type = 'button';
            if (!options.label) {
                options.label = options.key;
            }
            if (typeof options.is_submit === 'undefined') {
                options.is_submit = true;
            }
            switch (options.key) {
                case 'cancel':
                    options.class = 'cancel';
                    options.is_submit = false;
                    break;
            }
            var b = $t.jQuery('input', options);
            $form_node.append(b);
            if (options.key === 'cancel') {
                b.on('click', function() {
                    $form_node.trigger('fx_form_cancel');
                });
            }
            if (options.is_submit) {
                b.on('click', function() {
                    $form_node.submit();
                });
                if (!submit_added) {
                    $form_node.append(
                        '<input '+
                            ' type="submit" '+
                            ' style="position:absolute; top:-10000px; left:-10000px" />'
                    );
                    submit_added = true;
                }
            }
        });
        console.log(settings.form_button);
        $form_node.submit($fx_form.submit_handler);
    },
            
    submit_handler : function() {
        var status_block = $("#fx_admin_status_block");
        var $form = $(this);
        $(".ui-state-error").removeClass("ui-state-error");
        
        $form.trigger('fx_form_submit');

        $form.ajaxSubmit(function ( data ) {
            try {
                data = $.parseJSON( data );
            }
            catch(e) {
                status_block.show();
                status_block.writeError(data);
                return false;
            }

            if ( data.status === 'ok') {
                status_block.show();
                status_block.writeOk( data.text ? data.text : 'Ok');
                $form.trigger('fx_form_ok');
                /*
                if ( data.fields) {
                    $fx_form.draw_fields(data, $('#nc_dialog_error'));
                }
                */
            }
            else {
                status_block.show();
                status_block.writeError(data['text']);
                for ( i in data.fields ) {
                    $('[name="'+data.fields[i]+'"]').addClass("ui-state-error");
                }
            }
            $(window).hashchange();
        });
        return false;
    },

    init_tabs: function ( settings, container ) {
        $(container).append('<div id="fx_tabs"></div>');
        var cont = '';
        var _ul = $('<ul />');
        var i = 0;
        var active = 0;
        var keys = [];
        $.each(settings.tabs, function(key,val){
            if ( key === 'change_url') {
                return true;
            }
            keys.push(key);
            if ( val.active ) {
                active = i;
            }
            i++;
            $(_ul).append(
                '<li><div class="fx_tab"><div class="fx_tab_corner"></div>'+
                '<a rel="'+key+'" href="#'+settings.form.id+'_'+key+'">'+
                    (val.name !== undefined ? val.name : val)+
                '</a></div></li>'
            );
            cont += '<div id="'+settings.form.id+'_'+key+'"></div>'
        });
        $('#fx_tabs', container).append(_ul, cont);
        $("#fx_tabs", container).tabs({
            active: active
        });

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

    draw_field: function(json, target) {
        if (json.type === undefined) {
            json.type = 'input';
        }
        var type='';
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

        var node = $fx_fields[type](json);
        target.append(node);
        if (node === '') {
            return null;
        }
        // ajax change
        if (json.post && json.type !== 'button') {
            // creating container for extra json-loaded fields
            var post_container = $('<div class="container"></div>').appendTo(target);
            
            node.on('change', function(){
                var form_vals = {};
                $('input, textarea, select', node.closest('form')).each(function(){
                    var c_field_name = $(this).attr('name');
                    var c_field_type = $(this).attr('type');
                    if (c_field_name !== 'posting' && c_field_type !== 'button') {
                        var val;
                        if (c_field_type === 'radio') {
                            val = $('input[name="'+$(this).attr('name')+'"]:checked').val();
                        } else {
                            val = $(this).val();
                        }
                        form_vals[c_field_name] = val;
                    }
                });
                var data_to_post = $.extend({}, form_vals, json.post);
                $fx.post(data_to_post, function(fields){
                    post_container.html('');
                    $fx_form.draw_fields(fields, post_container);
                });
            });
            node.trigger('change');
        }
        return node;
    },

    add_parent_condition: function(parent, _el, container) {
        if (parent instanceof Array) {
            parent = {};
            parent[parent[0]] = parent[1];
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
                if (par_inp.length === 0) {
                    return;
                }

                if (par_inp.attr('type') === 'checkbox') {
                    var par_val = par_inp.get(0).checked * 1;
                } else {
                    var par_val = par_inp.val();
                }

                if (par_inp.attr('type') === 'radio') {
                    par_val = $(':input[name="'+pkey+'"]:checked').val();
                }
                switch (pexp) {
                    case '==':
                        do_show = (par_val === pval);
                        break;
                    case '!=':
                        if (
                            par_inp.css('display') === 'none' ||
                            par_inp.closest('.field').css('display') === 'none'
                            ) {
                            do_show = true;
                        } else {
                            do_show = (par_val !== pval);
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
        }, 1);
    }
};
})(jQuery);

window.fx_form = window.$fx_form = fx_form;

(function($) {
    $.fn.fx_create_form = function(options) {
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
        $fx_form.draw_fields(settings, _form);

        if (options.buttons_essence) {
            $fx.admin.set_essence(options.buttons_essence);
        }

        return this;
    };

    $.fn.writeError = function(message){
        if ( typeof message === 'string' ) {
            message = [message];
        }
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
    };

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
    };


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
    };
})(jQuery);