(function() {
    $fx.front_panel = {
        show_form: function(data) {
            var p = $('#fx_admin_extra_panel');
            if ($('form', p).length == 0) {
                p.html('<form />');
            }
            $('form', p).css('opacity', 1);
            $('form', p).animate({opacity:0}, 100, null, function() {
                p.css('height', '37px');
                p.fx_create_form(data);
                var cancel = $t.jQuery('input', {
                    type:'button',
                    label:'cancel'
                });
                cancel.on('click', function() {
                    $fx.front_panel.hide();
                });
                $('form', p).append(cancel);

                var panel_height = $('form', p).height() + 10; // form with margins

                var body_default_margin = $('body').data('fx_default_margin');
                if (!body_default_margin) {
                    body_default_margin = parseInt($('body').css('margin-top'));
                    $('body').data('fx_default_margin', body_default_margin);
                }

                var body_offset = 
                            body_default_margin 
                            + panel_height
                            - 37; // height of admin panel's second line
                p.animate({height: panel_height+'px'}, 300);
                $('form', p).css('opacity', 0.1).animate({opacity:1}, 300);
                $('body').animate(
                    {'margin-top':body_offset + 'px'},
                    300
                );
            });
        },
        load_form: function(options) {
            $fx.post(
                options, 
                function(json) {
                    $fx.front_panel.show_form(json)
                }
            );
        },
        hide: function() {
            var p = $('#fx_admin_extra_panel');
            p.animate({height:'0px'}, 200);
            $('body').animate({'margin-top' : $('body').data('fx_default_margin')+'px'},200);
        }
    };
})();