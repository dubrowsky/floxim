$(document).ready(function () {
    $('.log-in').click(function () {
        $(this).find('.form').not(':visible').fadeIn();   
        setTimeout( function() { 
            $('html').off('click.close_form').on('click.close_form', function(e) {
                console.log('qww')
              if ($(e.target).closest('.log-in').length !== 0) {
                return;
              }
                $('.log-in .form').fadeOut();
                $('html').off('click.close_form');
            })
        }, 50);
    })
    
})