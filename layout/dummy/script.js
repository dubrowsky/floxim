$(document).ready(function () {
    $('.log-in').click(function () {
        $(this).find('.form').not(':visible').fadeIn();   
        setTimeout( function() { 
            $('html').off('click.close_form').on('click.close_form', function(e) {
              if ($(e.target).closest('.log-in').length !== 0) {
                return;
              }
                $('.log-in .form').fadeOut();
                $('html').off('click.close_form');
            });
        }, 50);
    }); 
    
    $('html').on('mouseover', '.dropdown', function(e) {
        var dd = $(this);
        console.log('movr');
        dd.addClass('hover');
        clearTimeout(dd.data('mouseout_timeout'));
    });
    $('html').on('mouseout', '.dropdown', function(e) {
        /*if (this !== e.target) {
            console.log(this, e.target);
            return;
        }*/
        
        //console.log('lost misc');
        var dd = $(this);
        dd.data('mouseout_timeout', setTimeout(
            function() {
                dd.removeClass('hover');
                dd.attr('style', '');
            }, 500
        ));
    });
});