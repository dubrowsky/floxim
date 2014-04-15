function init_scripts($node) {
    var find = function(selector) {
        return $(selector, $node).add($node.filter(selector));
    };
    
    find('.main_menu').find('>ul').superfish({
        speed:1,
        delay:500
    });
    var $tabs = find('.tabs');
    $tabs.tabs();
    var $fake_tab = $('#tab-fake-0', $tabs);
    if ($fake_tab.length){
        var $all_tabs = $tabs.find('.tab-data>div');
        var fake_index = $all_tabs.index($fake_tab);
        $tabs.tabs('select', fake_index);
    }
    var $slider = find('.flexslider');
    function make_slider($nodes) {
        $nodes = $nodes.filter(':visible').not('.flexslider_active');
        $nodes.each(function() {
            $(this).flexslider({
                animation: "slide",
                //animationSpeed:5000,
                //slideshowSpeed:1000,
                keyboard: !window.$fxj
            });
        });
        setTimeout(function() {
            $nodes.addClass('flexslider_active');
        },50);
    };
    $tabs.off('tabsselect').on('tabsselect', function(e, ui) {
        setTimeout(function() {
            make_slider($('.flexslider', ui.panel));
        },5);
        e.stopPropagation();
    });
    make_slider($slider);
}


$(window).load( function() {init_scripts($('html'));});

if (window.$fxj) {
    $fxj('html').on('fx_infoblock_loaded', function (e){
        var $ib = $(e.target);
        init_scripts($ib);
    });
    $fxj('html').on('fx_select', '.flexslider_active', function(e) {
        //return;
        var $slider = $(this);
        var $slide = $(e.target).closest('.slide');
        if ($slide.length) {
            var slide_index = $('.slide', $slider).not('.clone').index($slide);
            //console.log('slide to '+slide_index);
            $slider.flexslider(slide_index);    
        }
        $slider.flexslider('stop');
        $fxj('html').one('fx_deselect', null, function () {
            $slider.flexslider('play');
        });
    });
    $fxj('html').on('fx_select', '.tabs .tab', function() {
        var $tab = $(this);
        var $tabs = $tab.closest('.tabs');
        var $all_tabs = $('.tab', $tabs);
        $tabs.tabs('select', $all_tabs.index($tab));
    });
    $fxj('html').on('fx_select', '.sf-js-enabled', function(e) {
        var $target = $(e.target);
        var $lis = $($target.parents('li').get().reverse()).add($target);
        //console.log($lis);
        $lis.trigger('mouseover').show();
        //for (var i = 0; i < $lis.length; i++){
            
        //}
        //console.log('superfishing', e.target);
        $(this).pauseSuperfish();
        $fxj('html').one('fx_deselect', function(e) {
            $(this).playSuperfish();
            $(e.target).closest('li').trigger('mouseleave');
        });
    });
};

$(function(){
// IPad/IPhone
	var viewportmeta = document.querySelector && document.querySelector('meta[name="viewport"]'),
	ua = navigator.userAgent,

	gestureStart = function () {viewportmeta.content = "width=device-width, minimum-scale=0.25, maximum-scale=1.6";},

	scaleFix = function () {
		if (viewportmeta && /iPhone|iPad/.test(ua) && !/Opera Mini/.test(ua)) {
			viewportmeta.content = "width=device-width, minimum-scale=1.0, maximum-scale=1.0";
			document.addEventListener("gesturestart", gestureStart, false);
		}
};
	
	scaleFix();
	// Menu Android
	if(window.orientation!=undefined){
  var regM = /ipod|ipad|iphone/gi,
   result = ua.match(regM)
  if(!result) {
   $('.sf-menu li').each(function(){
    if($(">ul", this)[0]){
     $(">a", this).toggle(
      function(){
       return false;
      },
      function(){
       window.location.href = $(this).attr("href");
      }
     );
    } 
   })
  }
 }
});
var ua=navigator.userAgent.toLocaleLowerCase(),
 regV = /ipod|ipad|iphone/gi,
 result = ua.match(regV),
 userScale="";
if(!result){
 userScale=",user-scalable=0"
}