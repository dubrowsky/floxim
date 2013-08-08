fx_additional_menu = function ( ) {
    this.container = $('#fx_admin_additional_menu');
}

fx_additional_menu.prototype.load = function () {
    var menu = this.container;
    /*
    $.each( ['undo', 'redo'], function(k, v) {
        var item = $('<div>').addClass('fx_admin_button').addClass('fx_admin_button_'+v).appendTo(menu);
        item.click( function(){
            console.log('history will be here some day...');
        });
    });
    */

    var logout = $('<a style="cursor:pointer;"/>').text(fx_lang('выход'));
    logout.appendTo(menu);
    logout.click(function() {
       $(this).append(
        '<form action="/floxim/" method="POST" style="width:1px; height:1px; overflow:hidden;">'+
            '<input type="hidden" name="essence" value="module_auth" />' +
            '<input type="hidden" name="action" value="logout" />' +
            '<input type="submit" />' +
        '</form>');
        $(this).find('form').submit();
    });
    console.log('patcher');
    $('<a class="fx_check_updates"><img src="http://floxim.org/getfloxim/check_updates.gif?version=#FX_VERSION_HASH#" /></a>').appendTo(menu);
}