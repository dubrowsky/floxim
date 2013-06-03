
fx_dialog_file = function ( field_id, data, callback ) {
    this.main_container = $('#fx_dialog_file');
    this.current_file_container = $('<div id="fx_dialog_file_current" />');
    this.select_container = $('<div />');
    this.editor_container = $('<div />');
    this.callback = callback;
    this.mode = false;
    this.field_id = field_id;
    
    if ( data ) {
        this.set_current_file(data);
    }
}

fx_dialog_file.prototype.open = function () {
    var settings = {
        height: 600,
        width: '80%',
        modal: true,
        title: 'File editor',
        zIndex: 78888
    };
    var self = this;
    
    this.main_container.dialog(settings);
    
    this.main_container.dialog("option", "buttons", []);
    this.add_button('save', 'Готово', function (){
        self.save_action();
    });
    this.add_button('upload', 'Выбрать файл', function (){
        self.show_select_form();
    });
    this.add_button('edit', 'Редактировать', function (){
        self.show_editor();
    });
    
    this.main_container.append(this.select_container);
    this.main_container.append(this.editor_container);
    
    this.show_select_form();
}

fx_dialog_file.prototype.show_select_form = function () {
    this.set_active_layer('select');
    this.mode = 'select';
    this.hide_button('upload');
    this.show_button('edit');
    
    var form_data = {};
    var self = this;
        
    form_data.tabs = {
        upload: 'Закачать', 
        existing: 'Выбрать из ранее закаченных', 
        google: 'Найти в Google'
    };
    var filehtml = $('<input name="file" type="file" />');
    filehtml.change(function(){
        $('form', self.main_container).ajaxSubmit( function ( data ) {
            data = $.parseJSON(data);
            self.set_current_file(data);
        });
    });
    
    form_data.fields = [];
    form_data.fields.push({
        name:'label', 
        tab: 'upload',
        type: 'label',
        'label' : filehtml
    });
    form_data.fields.push( this.hidden('essence', 'file') );
    form_data.fields.push( this.hidden('action', 'upload') );
    form_data.fields.push( this.hidden('posting', '1') );
    form_data.fields.push( this.hidden('field_id', this.field_id) );
    
    $( "#nc_tabs" ).live( "tabsselect", function(event, ui) {
        if ( ui.index == 1 ) {
            $( "#nc_tabs" ).die("tabsselect");
            var post = {
                essence: 'file', 
                action: 'get_filelist'
            };
            $fx.post(post, function(data) {
                self.show_existing_images(data);
            });
        }
    });

    form_data.fields.push( {
        type:'label', 
        label: '<div id="fx_dialog_file_existing" />', 
        tab: 'existing'
    });
    form_data.fields.push( {
        type:'label', 
        label: 'The Google Image Search API has been officially deprecated as of May 26, 2011.', 
        tab: 'google'
    });
    
    this.select_container.fx_create_form(form_data);
    this.select_container.prepend(this.current_file_container);
}

fx_dialog_file.prototype.show_editor = function () {
    this.set_active_layer('editor');
    this.mode = 'editor';
    this.show_button('upload');
    this.hide_button('edit');
    
    var post = {
        essence: 'file', 
        action: 'image_editor',
        path: this.current_file.path
    };
        
    var self = this;
    $fx.post(post, function(data){
        self.editor_container.html(data.html);
    });
        
}

fx_dialog_file.prototype.set_active_layer = function ( layer ) {
    if ( layer == 'select' ) {
        this.select_container.show();
        this.editor_container.hide();
    }
    else if ( layer == 'editor') {
        this.select_container.hide();
        this.editor_container.show();
    }
}

fx_dialog_file.prototype.show_existing_images = function( data ) {
    var container = $('#fx_dialog_file_existing');
    var self = this;
    
    if ( data ) {
        var img_count = fx_object_length(data), img_loaded = 0;
        $.each( data, function (key, image) {            
            var img = $('<img   src="'+image.src+'" />');
            img.load( function() { 
                img_loaded++;
                // галерею надо применять только после полной загрузки изображения
                if ( img_loaded == img_count ) {
                    $('#fx_dialog_file_existing').gpGallery('img');
                }
            }).appendTo(container);     
            
            img.click(function(){
                var post = {
                    essence: 'file', 
                    action: 'upload', 
                    'file[source_id]':key, 
                    posting:1
                };
                $fx.post( post, function(response) {
                    self.set_current_file(response);
                });
                $('img', '#fx_dialog_file_existing').removeClass('fx_dialog_file_selected');
                img.addClass('fx_dialog_file_selected');
            });
        }); 
    }
}

fx_dialog_file.prototype.set_current_file = function( data ) {
    this.current_file = {};
    this.current_file.path = data.path;
    this.current_file.filename = data.filename;
    this.current_file.file_id = data.file_id;
            
    this.show_current_file();
}

fx_dialog_file.prototype.show_current_file = function () {
    if ( !this.current_file ) return false;
    
    if ( $('img', this.current_file_container).length ) {
        $('img', this.current_file_container).attr('src',this.current_file.path );
    }
    else {
        this.current_file_container.html("");
        this.current_file_container.append('<img src="'+this.current_file.path+'" style="max-width:100px" />');
    }
    
}

fx_dialog_file.prototype.save_action = function () {
    var self = this;
    
    if ( this.mode == 'editor' ) {
        pie_ajax_post_save ( function (){
            if (self.callback && typeof(self.callback) === "function") {
                self.callback(self.current_file);
            }
            self.main_container.dialog('close');
        }); 
    }
    else {
        if (self.callback && typeof(self.callback) === "function") {
            self.callback(self.current_file);
        }
        self.main_container.dialog('close');
       
    }

}

fx_dialog_file.prototype.hidden = function ( key, value ) {
    return {
        type: 'hidden', 
        name: key, 
        value: value
    };
}

fx_dialog_file.prototype.add_button = function (key, text, callback ) {

            
    var buttons = this.main_container.dialog( "option", "buttons" );
    if ( !buttons || !buttons.length ) buttons = [];

    var button = {
        text: text
    };
    button['class'] = "fx_dialog_"+key+"_button fx_dialog_button";
    button['click'] = callback;
    buttons.push(button);
            
    this.main_container.dialog("option", "buttons", buttons );
}

fx_dialog_file.prototype.show_button = function (button ) {
    this.get_button(button).show();
}

fx_dialog_file.prototype.hide_button = function (button ) {
    this.get_button(button).hide();
}

fx_dialog_file.prototype.get_button =  function ( button ) {
    return $('.fx_dialog_'+button+'_button');
}