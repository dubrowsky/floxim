/**
 * Сортировка происходит на основе $fx.g_data.sortable
 */
fx_sort = function () {
    // в некоторых случаях надо запретить соритровку элементов, находящихся в определенном контейнере
    // например, при выборе блока с объектами, сами объекты нельзя сортировать, перемещать
    // можно только сам блок. prohibiting_parent и содержит такой контейнер.
    this.prohibiting_parent = false;
}

fx_sort.prototype.update = function ( mode ) {
    var self = this;
    $(".ui-sortable").sortable({disabled: true});
 
    this.load_prohibiting_parent();
    $.each ($fx.g_data.sortable, function (k, data) {
        if ( mode == data.mode ) {
            self.init_sort_item(data);
        }
    });
}

fx_sort.prototype.init_sort_item = function ( data ) {
    var selector = $(data.parent);
    if ( selector.length < 1 ) return false;
    var items = this.get_items(selector);

    var suitable = true;
    if ( this.prohibiting_parent ) {
        var prohibiting_parent = this.prohibiting_parent;
        $.each( items, function(k,item){
            var found = $(item).parent().closest( $('.fx_page_block_'+prohibiting_parent) );
            if ( found.length ) {
                suitable = false;
                return false;
            }
        });
    }
    if ( !suitable ) {
        return false;
    }
    var self = this;
    
    selector.sortable({
        placeholder:  data.without_placeholder ? false : 'fx_sortable_placeholder',
        cursor: 'move',
        disabled: false,
        items: items,
        start: function(event, ui){ 
            var h = ui.item.height();
            if ( h < 5  ) {
                h = 50;
            }
            ui.placeholder.height(h); 
            var w = ui.item.width();
            ui.placeholder.width(w);
        },
        update: function(event, ui) {
            var pos = [];
            var post_data = {};
            $.extend(true, post_data, data.post);
            self.get_items(selector).each( function(){
               var id = $fx.g_data.blocks[ $(this).data('key') ].post.id; 
               pos.push( id );
            });
            post_data.pos = pos;
            $fx.post_front(post_data);
        }
    });

}

fx_sort.prototype.get_items = function ( selector ) {
    var key = selector.data('key');
    var items = $('.fx_sortable_'+key, selector);
    return items;
}

fx_sort.prototype.load_prohibiting_parent = function () {
    this.prohibiting_parent = false;
    var selected = $('.fx_admin_selected');
    if ( selected.length == 1 && $fx.g_data.blocks[selected.data('key')] && $fx.g_data.blocks[selected.data('key')].hidden ) {
        this.prohibiting_parent = selected.data('key');
    }
}