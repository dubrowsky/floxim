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

function init_scripts($node) {
    $('.flexslider', $node).flexslider({
      animation: "slide"
    });
    if ($node.is('#tabs')) {
        $node.tabs();
    } else {
        $('#tabs', $node).tabs();
    }
    $().UItoTop({ easingType: 'easeOutQuart' });
    $("h2", $node).prepend("<div class='stripe_before'></div>");
    $("h2", $node).append("<div class='stripe_after'></div>");
    recount_h2($node);
}
 
function recount_h2($node) {
    $('h2 .stripe_after', $node).each(function() {
        var thiswidth = ($(this).parent().width() - $(this).prev().width()) / 2 - 17;
        $(this).css({width:thiswidth})
    });
    $('h2 .stripe_before', $node).each(function() {
        var thiswidth = ($(this).parent().width() - $(this).next().width()) / 2 - 17;
        $(this).css({width:thiswidth});
    });
    
    $('#carousel', $node).elastislide({
            imageW 	: 550,
            minItems	: 1,
            easing		: '',
            margin		: 0,
            border		: 0
    });
}

$(window).load( function () {
    init_scripts($('body'));
});
$(window).resize(function() {
    recount_h2($('body'));
});

if ($fxj) {
    $fxj('html').on('fx_infoblock_loaded', function (e) {
        init_scripts($(e.target));
    });

    $fxj('html').on('fx_select', '.flexslider', function () {
        $(this).flexslider('pause');
    });

    $fxj('html').on('fx_deselect', '.flexslider', function () {
        $(this).flexslider('play');
    });
    /*
    $fxj('html').on('fx_set_front_mode', function (){
        if ($fx.front.mode === 'view') {
            $('#tabs').tabs('enable');
            console.log('tabs enabld');
        } else {
            $('#tabs').tabs('disable');
            console.log('tabs disabld');
        }
    });
    */
}