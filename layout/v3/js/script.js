(function(){
    var ratio = 0.364;
    $(document).ready(resizeBanner)
    $(window).resize(resizeBanner)

    function resizeBanner() {
        $.each($('.banner, .slider'), function(i, item) {
            var $item = $(item);
            var width = $item.width();
            $item.height(width*ratio);
        })
    }

    function desk () {

        $('nav .main-menu').css('width', 'auto');
        console.log('desk', $('nav .main-menu'))
        $('html').on('mouseover.res', 'nav .main-menu .main-menu-item', function (e) {
            if ($(this).hasClass('dropdown')){
                var nav = $(this).closest('nav');
                $('nav .main-menu .main-menu-item.dropdown.active').removeClass('active');
                $(this).addClass('active');
                var offset = $(this).offset().left;
                var subs = $(this).find('.width-helper');
                subs.css({
                    'left':-offset
                });
                clearTimeout($(this).data('mouseout_timeout'));
            } else {
                $('nav .main-menu .main-menu-item').removeClass('active');
                clearTimeout($(this).data('mouseout_timeout'));
            }
        })


        $('html').on('mouseout.res', 'nav .main-menu .main-menu-item', function (e) {
            if ($(this).hasClass('dropdown')){
                var $this = $(this);
                $this.data('mouseout_timeout', setTimeout(
                    function() {
                        $this.removeClass('active');
                    }, 500
                ));
            }
        })


        function setMenuWidth() {
            var width = $(window).width();
            $('nav .main-menu .width-helper').width(width);
        }
        $(document).on('ready.res', setMenuWidth);
        $(window).on('resize.res', setMenuWidth);

    }

    function mob () {

        $('html').on('click.res', 'nav .menu-icon', function (e) {
            var ul = $(this).parent().find('.main-menu');
            if (!ul.hasClass('active'))
                ul.addClass('active');
            else
                ul.removeClass('active');
        })

        $('html').on('click.res', 'nav .main-menu .main-menu-item.dropdown a .more', function (e){
            e.preventDefault();
            var menu = $(this).closest('.main-menu-item');
            if (!menu.hasClass('active')) {
                menu.addClass('active');
                $(this).text('-');
            }
            else {
                menu.removeClass('active');
                $(this).text('+');
            }

        })

        $('html').on('click.res', 'nav .main-menu .close', function (e){
            var menu = $(this).parent();
            if (!menu.hasClass('active')) {
                menu.addClass('active');
            }
            else {
                menu.removeClass('active');
            }

        })

        function setMenuWidth() {
            var width = $(window).width();
            $('nav .main-menu').width(width);
        }
        $(document).on('ready.res', setMenuWidth);
        $(window).on('resize.res', setMenuWidth);
    }
	$('html').on('click', '.icon > a', function (e) {
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

    $(window).resize(function() {
        var width = $(window).width();
        $('.icons .icon .width-helper').width(width-41);
        $.each($('.icons .icon .width-helper'), function(i, item) {
            var $item = $(item);
            var $parent = $item.parent();
            var offset = $parent.offset().left;
            $item.css('left', -offset);
        })
    })

    function WidthChange(mq) {
        $('html').off('.res');
        $(document).off('.res');
        $(window).off('.res');
        if (mq.matches) {
            mob();
        } else {
            desk();
        }
    }

    if (matchMedia) {
        var mq = window.matchMedia("(min-width:320px) and (max-width:1000px)");
        mq.addListener(WidthChange);
        WidthChange(mq);
    }

})();