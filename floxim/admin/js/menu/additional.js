fx_additional_menu = function ( ) {
    this.container = $('#fx_admin_additional_menu');
}

fx_additional_menu.prototype.load = function () {
    /*
    var menu = this.container;
    var logout = $('<a style="cursor:pointer;"/>').text(fx_lang('выход'));
    logout.appendTo(menu);
    */
    var logout = this.container.find('a.fx_logout');
    logout.click(function() {
       $(this).append(
        '<form action="/floxim/" method="POST" style="width:1px; height:1px; overflow:hidden;">'+
            '<input type="hidden" name="essence" value="module_auth" />' +
            '<input type="hidden" name="action" value="logout" />' +
            '<input type="submit" />' +
        '</form>');
        $(this).find('form').submit();
    });
}