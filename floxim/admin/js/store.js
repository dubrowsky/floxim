
fx_store = function ( data ) { 
    this.containers = {};
    this.filters = data.filter_form || [];
    this.shown_items_num = 0;
    this.count = 0;
    this.current_filter = {};
    this.essence_type = data.essence_type;
    
    this.init();
    this.set_total_num(data.count);
    this.draw_items(data.items);
}

fx_store.prototype.init = function () {
    this.load_containers();
    this.init_filters();
    this.init_more_button();
};

fx_store.prototype.load_containers = function () {
    this.containers.main = $('<div />').addClass('fx_admin_store_container');
    this.containers.filter = $('<div />').addClass('fx_admin_store_filter');
    this.containers.total = $('<div />').addClass('fx_admin_store_total');
    this.containers.total_num = $('<span />');
    this.containers.items = $('<div />').addClass('fx_admin_store_items');
    this.containers.more = $('<div />').addClass('fx_admin_store_more');
    
    this.containers.main.append(this.containers.filter);
    this.containers.total.html('Найдено: ').append(this.containers.total_num);
    this.containers.main.append(this.containers.total);
    this.containers.main.append(this.containers.items);
    this.containers.main.append(this.containers.more);
}

fx_store.prototype.init_filters = function () { 
    var cont_f = this.containers.filter;
    $.each( this.filters, function (k, filter){
        var c = $fx_form.draw_field(filter, cont_f);
        cont_f.append(c);
    });
    
    var self = this;
    $fx.panel.one('fx.fielddrawn', function(){
        var changable = $('input, select', cont_f);
        changable.change(function(){
            self.current_filter = {};
            changable.each(function(){
                var val = $(this).attr('type') === 'radio' ?  $('input[name="'+$(this).attr('name')+'"]:checked').val() : $(this).val();
                self.current_filter[ $(this).attr('name') ] = val;

            }); 
            self.handle_change();       
        });
    });
}

fx_store.prototype.init_more_button = function (count) {
    var button = $('<span>').text('Еще');
    var self = this;
    button.click( function() {
       self.handle_more(); 
    });
    this.containers.more.html(button);
}

fx_store.prototype.set_total_num = function (count) {
    this.count = count;
    this.containers.total_num.text(count);
}

fx_store.prototype.get_main_container = function () {
    return this.containers.main;
}

fx_store.prototype.handle_change = function () {
    var data = this.current_filter;
    data.action = 'store';
    data.essence = this.essence_type;
    data.reason = 'apply_filter';
    
    var self = this;
    $fx.post(data, function(response){
        self.clear_items();
        self.draw_items( response.items );
        self.set_total_num(response.count);
    });
}

fx_store.prototype.handle_more = function ( ) {
    var data = this.current_filter;
    data.position = this.shown_items_num +1 ;
    data.action = 'store';
    data.essence = this.essence_type;
    data.reason = 'next_page';
    
    var self = this;
    $fx.post(data, function(response){
        self.draw_items( response.items );
    });
}

fx_store.prototype.draw_items = function (items) {
	var items_cont = this.containers.items;
    var item_div;
    var self = this;
    if (items === null) {
    	return;
    }
    $.each( items, function (k, item) {
        self.shown_items_num++;
        
        item_div = $('<div>').addClass('fx_admin_store_item');
        if ( self.shown_items_num % 2 ) {
            item_div.addClass('fx_admin_store_item_odd');
        }
        item_div.html('');
        if ( item.icon ) item_div.append('<img src="'+item.icon+'" width="150" height="150" />');
        item_div.append('<h2>'+item.name+'</h2>');
        if ( item.price ) {
            item_div.append('<h3>'+item.price+' $</h3>');
        }
        else {
            item_div.append('<h3>бесплатно</h3>');
        }
        if ( item.description ) item_div.append('<p>'+item.description+'</p>');
        if ( item.url ) item_div.append('<a target="_blank" href="'+item.url+'">посмотреть описание на сайте</a>');
        item_div.append('<div style="clear:both;"></div>');
        
        if ( self.essence_type == 'infoblock' ) {
            item_div.append( self.get_setup_button_front(item.store_id) );
        }
        else {
            item_div.append( self.get_setup_button(item.store_id) );
        }
        
        items_cont.append(item_div);
        
        
    });
    
    this.redraw_more();  
}

fx_store.prototype.redraw_more = function () {
    if (this.count > this.shown_items_num) {
        this.containers.more.show();
    }
    else {
        this.containers.more.hide();
    }
}

fx_store.prototype.clear_items = function () {
    this.containers.items.html('');
    this.shown_items_num = 0;
}

fx_store.prototype.get_setup_button = function ( store_id ) {
    var cont = $('<div/>').addClass('fx_admin_store_setup');
    var button = $('<button>').text('Установить');
    var self = this;
    button.click( function () {
        cont.html('Установка...');
        self.setup(store_id, function(){
            cont.html('Установлено');
        });
       return false; 
    });
    cont.append(button);
    return cont;
}

fx_store.prototype.get_setup_button_front = function ( store_id ) {
    var cont = $('<div/>').addClass('fx_admin_store_setup');
    var button = $('<button>').text('Установить и использовать');
    var self = this;
    button.click( function () {
        cont.html('Установлено. Дальше будут переход на страницу настроек');
       /* self.setup(store_id, function(){
            $fx.store_after_install();
        });*/
       return false; 
    });
    cont.append(button);
    return cont;
}

fx_store.prototype.setup = function ( store_id, callback ) {
    var data = {};
    data.action = 'store';
    data.essence = this.essence_type;
    data.store_id = store_id;
    data.posting = 1;
    
    $fx.post(data, callback);
}