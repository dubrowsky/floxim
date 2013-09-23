$(function(){
    $('html').on('click', '.img-slider .preview', function() {
        if (!$(this).hasClass('preview-active')) {
        	change_pic($(this).attr("data-picid"));
        }
    });
    /*
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
    */
    function change_pic (id) {
    	var pic = $(".img-block.picid"+id);
    	if (pic.length  > 0 ) {
    		var pics = $(".img-block");
    		pics.filter(':visible').stop().fadeOut().removeClass("img-block-active");
    		pic.addClass("img-block-active").fadeIn();
    		$(".img-slider .preview").removeClass("preview-active");
    		$(".img-slider .preview.picidprev"+id).addClass("preview-active")
    		window.location.hash = '#picid'+id;
    	}
	}
	function change_pic_by_hash () {
    	var hash = window.location.hash.substring(1);
    	if (hash.indexOf("picid") == 0) {
			var id = hash.substring(5);
			change_pic(id);
    	}
	}
    $("html").on("fx_infoblock_loaded", function () {
    	change_pic_by_hash ();
    })
    $(document).ready (function () {
    	change_pic_by_hash ();
    })
    
});