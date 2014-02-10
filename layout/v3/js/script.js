(function(){
	$('html').on('mouseover', 'nav .main-menu .main-menu-item', function (e) {
		if ($(this).hasClass('dropdown')){
			var nav = $(this).closest('nav');
			$('nav .main-menu .main-menu-item.dropdown.active').removeClass('active');
			$(this).addClass('active');
			var offset = $(this).offset().left;
			var subs = $(this).find('.width-helper');
			subs.css({
				'left':-offset,
				'width': nav.width()
			});
	        clearTimeout($(this).data('mouseout_timeout'));
       	} else {
       		$('nav .main-menu .main-menu-item').removeClass('active');
	        clearTimeout($(this).data('mouseout_timeout'));
       	}
	})

	$('html').on('mouseout', 'nav .main-menu .main-menu-item', function (e) {
		if ($(this).hasClass('dropdown')){
			var $this = $(this);
	        $this.data('mouseout_timeout', setTimeout(
	            function() {
	                $this.removeClass('active');
	            }, 500
	        ));
	    }
	})
	$('html').on('click', '.icon.off a', function (e) {
		e.preventDefault();
		var node = $(this).parent();
		var nav = node.closest('nav')
		if (!node.hasClass('active')) {
			$('.icon').removeClass('active');
			node.addClass('active');
			//var width = nav.width();
			var width = $(window).width();
			var offset = node.offset();
			node.find('.width-helper').css({
				'width': width-41,
				'left': -offset.left
			});
			node.find('form input').not('[type="hidden"]').eq(0).focus();
			$('html').on('click.clickover', function(e) {
				if (!$.contains(node.get(0), e.target)) {
					node.removeClass('active')
					$('html').off('click.clickover');
				}
			})
		} else {
			node.removeClass('active');
		}
		return false;
	});
	function full_back(){
		var height = $(window).height();
		$('.full-back').height(height-120);
	}
	$(document).ready(full_back);
	$(window).resize(full_back);
    $("html").on("fx_infoblock_loaded", full_back);
})();