$(function(){
    var set_height = function() {
        var slider_height = $(window).height() - parseInt($('body').css('margin-top')) - $('.nav').height();
        //console.log(slider_height, $('.slider .slide').innerHeight())
        $('.slider .slide').css({
                'max-height': slider_height,
                'height': 'auto',
        })
        $('.slider').css({
                'max-height': slider_height,
                'height': 'auto',
        })
        var slide_inner = $('.slider .slide').innerHeight();
        if (slide_inner < slider_height) {
            slider_height = slide_inner;

            $('.slider .slide').css({
                'max-height': 'auto',
                'height': slider_height,
            });
            $('.slider').css({
                'max-height': 'auto',
                'height': slider_height,
            });

        }
        console.log(slide_inner, slider_height)
    };
    $('.slider .slide img').eq(0).load(set_height)
    $(window).resize(set_height);
    $('html').on('click', '.slider .switcher A', function(e){
        e.preventDefault();
        change_slide($(this).parent().attr("data-slideid"));
    });

    $('html').on('click', '.slider .btn-prev, .slider .btn-next', function(e){
        var switcher_index = $('.slider .switcher li.active').index('.slider .switcher li');
        var switchers_length = $('.slider .switcher li').length;
        if ($(this).hasClass('btn-next')) {
           switcher_index++;
           if (switcher_index >= switchers_length) {
               switcher_index = 0;            
           }         
        } else {
           switcher_index--;
           if (switcher_index < 0) {
               switcher_index = switchers_length-1;            
           }
        }
        change_slide($('.slider .switcher li').eq(switcher_index).attr('data-slideid'));
        return false;
    });
    function change_slide(id) {
        var slides = $('.slider .slide');
        var slide = $('.slider .slide.slideid'+id);
        if (slide.length>0) {
            slides.removeClass('slide_active').fadeOut();
            slide.fadeIn('slow', function() {slide.addClass('slide_active')});
            $('.slider .switcher li.slideid'+id).addClass('active').siblings().removeClass('active');
            window.location.hash = '#slideid'+id;
        }
    }    
    function change_slide_by_hash () {
      var hash = window.location.hash.substring(1);
      if (hash.indexOf("slideid") === 0) {
            var id = hash.substring(7);
            change_slide(id);
        }
    }
    $("html").on("fx_infoblock_loaded", change_slide_by_hash);
    change_slide_by_hash ();

});