$(function(){
    $('html').on('click', '.img-slider .preview', function() {
        if (!$(this).hasClass('preview-active')) {
        	changepic($(this).attr("data-picid"));
        }
        //return false;
    });
    $('html').on('click', '.img-block.img-block-active', function() {
    	var next = $(this).next(); 
    	var id;
    	if (next.length > 0) {
    		id = next.attr("data-picid");
    	} else {
    		id = $(".img-block").eq(0).attr("data-picid");
    	}
		changepic(id)
    })
    function changepic (id) {
    	var pic = $(".img-block.picid"+id).filter(':hidden');
    	if (pic.length  == 1) {
    		var pics = $(".img-block");
    		pics.filter(':visible').stop().fadeOut().removeClass("img-block-active");
    		pic.addClass("img-block-active").fadeIn();
    		$(".img-slider .preview").removeClass("preview-active");
    		$(".img-slider .preview.picidprev"+id).addClass("preview-active")
    		window.location.hash = '#picid'+id;
    	}
	}
    
    $(document).ready (function () {
    	var hash = window.location.hash.substring(1);
    	if (hash.indexOf("picid") == 0) {
			var id = hash.substring(5);
			changepic(id);
    	}
    })
    
});