$(function(){
    $('html').on('click', '.gallery .switcher A', function(e){
        e.preventDefault();
        change_slide($(this).parent().attr("data-slideid"));
    });

    $('html').on('click', '.gallery .btn-prev, .gallery .btn-next', function(){
        var switcher_index = $('.gallery .switcher li.active').index('.gallery .switcher li');
        var switchers_length = $('.gallery .switcher li').length;
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
        change_slide($('.gallery .switcher li').eq(switcher_index).attr('data-slideid'));
        return false;
    });
    function change_slide(id) {
        var slides = $('.gallery .gallery_item');
        var slide = $('.gallery .gallery_item.slideid'+id);
        if (slide.length>0) {
            slides.filter(':visible').stop().fadeOut().removeClass('gallery_item_active');
            //slides.fadeOut();
            slide.fadeIn().addClass('gallery_item_active');
            $('.gallery .switcher li.slideid'+id).addClass('active').siblings().removeClass('active');
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