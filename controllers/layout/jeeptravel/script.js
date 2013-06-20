$(function(){
    $('.main-section .gallery .switcher A').on('click', function(){
        var $this = $(this);
        var $li = $this.closest('LI');

        if (!$li.hasClass('active')) {
            var index = $li.index('.main-section .gallery .switcher LI');
            $li.addClass('active').siblings().removeClass('active');

            var $slides = $('.main-section .gallery .gallery_item');
            $slides.filter(':visible').stop().fadeOut();
            $slides.eq(index).fadeIn();
        }

        return false;
    });

    $('.main-section .gallery .btn-prev, .main-section .gallery .btn-next').on('click', function(){
        var $this = $(this);
        var $dots = $('.main-section .gallery .switcher LI');
        var index = $dots.filter('.active').index('.main-section .gallery .switcher LI');

        if ($this.hasClass('btn-prev')) {
            index--;
            if (index < 0) {
                index = $dots.length - 1;
            }
        } else {
            index++;
            if (index >= $dots.length) {
                index = 0;
            }
        }
        console.log('index', index, this.className);
        $dots.eq(index).find('A').triggerHandler('click');

        return false;
    });

    $('.company .btn-prev, .company .btn-next').on('click', function(){
        var $this = $(this);

        var $faces = $('.company .collective LI');

        $faces.filter('.active').removeClass('active');

        if ($this.hasClass('btn-prev')) {
            $faces.eq(1).addClass('active');

            $faces.last().hide(200, function(){
                $(this).insertBefore($faces.first()).show(200);
            });
        } else {
            $faces.eq(3).addClass('active');

            $faces.first().hide(200, function(){
                $(this).insertAfter($faces.last()).show(200);
            });
        }

        return false;
    });
    
    $('.img-slider').on('click', '.preview', function() {
        if (!$(this).hasClass('active')) {
            var previews = $(this).closest('.img-slider').find('.preview');
            var index = previews.index(this);
            var images = $(this).closest('.img-list').find('.img-block');
            images.filter(':visible').stop().fadeOut();
            images.eq(index).fadeIn();
            //alert(index + ' clickd (of '+images.length);
            return false;
            $li.addClass('active').siblings().removeClass('active');

            var $slides = $('.main-section .gallery .gallery_item');
            $slides.filter(':visible').stop().fadeOut();
            $slides.eq(index).fadeIn();
        }

        return false;
    });
});