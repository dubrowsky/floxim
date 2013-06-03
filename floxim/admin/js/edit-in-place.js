(function($){
    $.fn.edit_in_place= function(options, parent_data, callback ) {
        var editor = new fx_edit_in_place(this, options, parent_data, callback);
        editor.show_panel();
        editor.start_editing();
    }
})(jQuery);

fx_edit_in_place = function ( target, options, parent_data, callback ) { 
    this.target = target;
    this.options = options || {};
    this.parent_data = parent_data || {};
    this.callback = callback;

    // эти css применятся к управляющему элементу(например, input), чтобы сделать его максимально эдин-ин-плейсным
    this.used_css =  ['width','color', 'text-transform', 'font-weight', 'font-size',  'font-family','line-height', 'float'];
    this.config_elrte = {
        toolbar: 'fx_einp_toolbar',
        lang:'ru',
        allowSource: false
    };
        
    this.KEYCODE_ESC = 27;
    this.KEYCODE_ENTER = 13;
}

fx_edit_in_place.prototype.start_editing = function () {
    this.type = this.options.type || 'input';
    
    this.post_options = {};  
    if ( this.parent_data ) $.extend(this.post_options, this.parent_data);
    if ( this.options.post ) $.extend(this.post_options, this.options.post );
    
    // файл обрабатывается отдельно
    if ( this.type == 'file' || this.type == 'image' ) {
        var data = {};
        var file_field = {
            type:this.type, 
            label: 'File', 
            name:this.options.field
            };
        if ( this.options.fileinfo ) {
            $.extend(true, file_field,this.options.fileinfo );
        }
        
        data.fields = [file_field];
        $.each ( this.post_options, function (k,v) {
            data.fields.push({
                type:'hidden', 
                name:k,
                value:v
            });

        });
        data.fields.push({
            type:'hidden', 
            name:'action',
            value:'edit'
        });
        data.fields.push({
            type:'hidden', 
            name:'posting',
            value:1
        });

        $fx_dialog.open_dialog(data);
       
        return false;
    }
    
    this.old_value = this.target.html();
        
    var css = this.get_css();
    this.control_element = this.make_control_element();
    this.target.html('').append(this.control_element);
    this.apply_css(css);
    
    this.control_element.putCursorAtEnd();
    
    this.unbind_old_events();
    this.bind_events();
    
};

/*
 *@todo для некоторых полей можно увеличить ширину и высоту
 */
fx_edit_in_place.prototype.get_css = function () {
    var i, key,css = {};
    for ( i = 0; i < this.used_css.length; i++ ) {
        key = this.used_css[i];
        css[key] = this.target.css(key);
    }

    return css;
};

fx_edit_in_place.prototype.make_control_element = function () {
    if ( this.type == 'text' && this.options.wysiwyg ) {
        this.type = 'wysiwyg';
    }
    
    var self = this;
    
    var element;
    
    switch ( this.type ) { 
        case 'string': case 'int': case 'floatfield': case 'color': 
            element = $('<input />').val(this.old_value);
            break;
        case 'text':
            element = $('<textarea/>').val(this.old_value);
            break;
        case 'wysiwyg' :
            element = $('<span />');
            this.make_wysiwyg();
            break;
        case 'select':
            element = this.make_select_element();
            break;
        case 'datetime':
        	element = $('<input />').val(this.options.value); // yy-mm-dd
        	element.datetimepicker({
                changeMonth: true,
                changeYear: true,
                //dateFormat: 'dd.mm.yy',
                dateFormat: 'yy-mm-dd',
                timeFormat: 'hh:mm:ss',
                separator: ' ',
                dayNamesMin: ['Вс', 'Пн', 'Вт', 'Ср','Чт','Пт','Сб'],
                monthNamesShort:['январь', 'февраль', 'март','апрель','май','июнь','июль','август','сентябрь','октябрь','ноябрь','декабрь'],
                nextText: 'Следующий',
                prevText: 'Предыдущий',
                yearRange: '1950:c+20',
                firstDay: 1,
                showOn: "focus",
                beforeShow: function() {
                	self.is_canceled = true;
                },
                onClose: function () {
                	self.is_canceled = false;
                	$(this).focus().blur() // фокус может не стоять на инпуте - тогда ничего не произойдет
                }
            });
        	break;
        default:
            alert('Incorrent type '+this.type+' in edit-in-place');
    }
    
    return element;
};

fx_edit_in_place.prototype.make_wysiwyg = function () {
    this.editor_wysiwyg = new elRTE( this.target.get(0), 
        $.extend(true, this.config_elrte,  {
            height: this.target.height() + 30
        })
        );
       
    this.wysiwyg_show_toolbar();
    this.wysiwyg_apply_css(); 
    
}

fx_edit_in_place.prototype.wysiwyg_show_toolbar = function () {
    var old_toolbar = this.editor_wysiwyg.toolbar;
    $('.el-rte .statusbar').hide();
    this.toolbar = $('<div class="el-rte" />').append( old_toolbar.clone(1) );
    $("#fx_admin_control").append(this.toolbar);
    old_toolbar.remove();
}

fx_edit_in_place.prototype.wysiwyg_apply_css = function () {
    var css = this.get_css();
    css['width'] -= 10;
    css['margin'] = 0;
    $("iframe", ".workzone").contents().find("body").css(css);
    
    this.target.next().css({
        'z-index':'16777270'
    });
}

fx_edit_in_place.prototype.make_select_element = function () {
    var element = $('<select />');
    if (this.options.multiple) element.prop('multiple', 'multiple');
    element = this.fill_select_element(element);
    return element;
};

fx_edit_in_place.prototype.fill_select_element = function ( element ) {
    var self = this;
    $.each(this.options.values, function(i, v) {
        var option = $('<option />').attr('value', i).text(v);
        if ( self.is_selected(self.options.value, i) ) {
            $(option).prop('selected', 'selected');
        }
        $(element).append(option);
    });
    
    return element;
};

fx_edit_in_place.prototype.is_selected = function ( value, current ) {
    return (value && ( (( value instanceof Array ) && $.inArray(current,value) > -1) || value ==  current ));
} 

fx_edit_in_place.prototype.apply_css = function ( css ) {
    this.control_element.css(css);
};

fx_edit_in_place.prototype.bind_events = function () {
    var self = this;
    var event_data = {
        obj: self
    };
    
    var save_later = function(e) {
		setTimeout( function() {self.handler_save(e)}, 200 );
	}
    
    self.save_button.click(event_data, self.handler_save);
    self.cancel_button.click(event_data, self.handler_cancel);
    
    
    self.control_element.blur(event_data, save_later);
    self.control_element.keydown(event_data, self.handler_keydown);
    if (self.type == 'wysiwyg') {
    	self.editor_wysiwyg.$doc.keydown(event_data, self.handler_keydown);
    	self.editor_wysiwyg.$doc.blur(event_data, save_later);
    }
};

fx_edit_in_place.prototype.unbind_old_events = function () {
    var self = this;
    
    self.save_button.unbind('click', self.handler_save);
    $fx.panel.unbind('fx.click',self.handler_save );
    self.cancel_button.unbind('click', self.handler_cancel);
    self.control_element.unbind('blur');
    self.control_element.unbind('keydown',self.handler_keydown);
};

fx_edit_in_place.prototype.handler_save = function ( event ) {
    var self = event.data.obj;
    
    if (self.is_canceled) {
    	return false;
    }
    
    // как будто отмена, чтоб событие не обрабатывалось дважды - 
    // например по нажатию Enter и по blur() на дейтпикере
    self.is_canceled = true; 
    
    
    self.unbind_old_events();
    
    var val = self.get_value();
    if ( self.old_value != val ) {
        self.post_options[self.options.field] = val;
        if ( !self.post_options.action ) self.post_options.action = 'edit';
        self.save();
        self.options.value = val;
    }
        
    self.show_new_value(val);
    self.finish();

    return false;
}

fx_edit_in_place.prototype.handler_cancel = function ( event ) {
    var self = event.data.obj;
    
    self.unbind_old_events();
    
    self.restore_old_value();
    self.finish();
    
    self.is_canceled = true;
    
    return false;
}

fx_edit_in_place.prototype.handler_keydown = function ( event ) {
    var self = event.data.obj;
    
    if (event.keyCode == self.KEYCODE_ENTER ) {
    	var el = self.control_element.get(0);
    	if ( el.nodeName == 'INPUT' && el.type == 'text' || 
    		 el.nodeName == 'TEXTAREA' && event.ctrlKey ||
    	 	self.type == 'wysiwyg' && event.ctrlKey) 
    		{
    		self.handler_save(event);
			return false;	
    	}
    }
    
    if ( event.keyCode == self.KEYCODE_ESC ) {
    	self.handler_cancel(event);
    }
     
    return true;
    
    
}



fx_edit_in_place.prototype.get_value = function () {
    return this.type == 'wysiwyg' ? this.editor_wysiwyg.val() : this.control_element.val();
}


fx_edit_in_place.prototype.save = function () {
    var self = this;
    $fx.post_front(this.post_options, function(json){
        if ( json.status == 'error' ) {
            self.restore_old_value();
        }
    });
}

fx_edit_in_place.prototype.show_new_value = function ( val ) {
    // для selecta надо выводить не id, а сами значения
    if ( this.type == 'select' ) {
        val = (this.options.multiple ? 
            $('option:selected', this.control_element).map(function(){
                return this.text
            }).get().join(', ') : 
            $("option:selected", this.control_element).html() );
    }
    
    this.set_html(val);
}

fx_edit_in_place.prototype.restore_old_value = function ( ) {
	if (this.editor_wysiwyg) {
		this.editor_wysiwyg.val(this.old_value);
	}
	this.set_html(this.old_value);
}

fx_edit_in_place.prototype.set_html= function (value ) {
    this.control_element.closest('.nc_editor').html(value);
}

fx_edit_in_place.prototype.finish = function () {
    if ( this.editor_wysiwyg ) {
        this.toolbar.remove();
        this.editor_wysiwyg.destroy();
    }
    
    this.hide_panel();
    
    if ( this.callback ) {
        this.callback();
    }
}

fx_edit_in_place.prototype.show_panel = function () {
    var cont = $("#fx_admin_editinplace_buttons");
    if ( !cont.length ) {
        cont = $('<div id="fx_admin_editinplace_buttons" />').appendTo("#fx_admin_control");
        cont.html('<span id="fx_admin_editinplace_buttons_save">Сохранить</span><span id="fx_admin_editinplace_buttons_cancel">Отменить</span>');
    }
    cont.show();

    this.save_button =  $('#fx_admin_editinplace_buttons_save');
    this.cancel_button =  $('#fx_admin_editinplace_buttons_cancel');
}

fx_edit_in_place.prototype.hide_panel = function () {
    $("#fx_admin_editinplace_buttons").hide();
}

   
