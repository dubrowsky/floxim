fx_breadcrumb = function () {
    this.container = $('#fx_admin_breadcrumb');
}

fx_breadcrumb.prototype.load = function (data) {
    var self = this, title_part = [];
    this.container.html('');

    if ( data ) {
        var count = data.length;
        $.each(data, function(key, item){
        	var is_last = !(key < count - 1);
        		
            var element = $('<a/>').text(item.name);
            title_part.push(item.name);
            
            if (item.href && !is_last) {
            	var href = /^#admin\./.test(item.href) ? item.href : '#admin.'+item.href;
            	if (href.replace(/^#/, '') != document.location.hash.replace(/^#/, '')) {
            		element.attr('href', href);
            	}
            }
        
            /*if ( item.url || item.absolute_hash ) {
            if ( window.location.hash.replace('#admin.', '') != item.url ) {
                element.addClass('fx_admin_underlined');
            }
            element.click( function() {
                window.location.hash = item.absolute_hash  ? item.absolute_hash  :( $fx.mode + '.' + item.url);
                return false;
            });
        }*/
            self.container.append(element);
            if (!is_last) {
                self.container.append('<span>/</span>');
            }
        });
            
        // title
        if ( $fx.mode && $fx.mode == 'admin') {
            $("title").html( title_part.reverse().join(' / '));
        }
    }
  
}
