(function () {
	$('html').on('click', '.icon.login.off a', function (e) {
		e.preventDefault();
		var node = $(this).parent();
		var nav = node.parent().parent().parent();
		if (!node.hasClass('active')) {
			node.addClass('active');
			var width = nav.width();
			node.find('.auth_form').css('width', width-80);
		} else {
			node.removeClass('active');
		}
		return false;
	});

	$('html').on('mouseover', '.nav .main-menu .menu-item.dropdown', function (e) {
		var nav = $(this).parent().parent().parent();
		$('.nav .main-menu .menu-item.dropdown.active').removeClass('active');
		$(this).addClass('active');
		var offset = $(this).offset().left;
		var subs = $(this).find('.menu-sub-items');
		subs.css({
			'left':-offset,
			'width': nav.width()
		});
        clearTimeout($(this).data('mouseout_timeout'));
	})

	$('html').on('mouseout', '.nav .main-menu .menu-item.dropdown', function (e) {
		var $this = $(this);
        $this.data('mouseout_timeout', setTimeout(
            function() {
                $this.removeClass('active');
            }, 500
        ));
	})

})();