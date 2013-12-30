(function($){
    $.fn.edit_in_place = function(command) {
        var eip = this.data('edit_in_place');
        if (!eip) {
            eip = new fx_edit_in_place(this);
            this.data('edit_in_place', eip);
            this.addClass('fx_edit_in_place');
        }
        if (!command) {
            return eip;
        }
        switch(command) {
            case 'destroy':
                eip.stop();
                this.data('edit_in_place', null);
                this.removeClass('fx_edit_in_place');
                break;
        }
    };
})(jQuery);

function fx_edit_in_place( node ) { 
    this.node = node;
    this.panel_fields = [];
    this.is_content_editable = false;
    
    this.ib_meta = node.closest('.fx_infoblock').data('fx_infoblock');
    
    var eip = this;
    
    this.node.on('keydown.edit_in_place', function(e) {
        eip.handle_keydown(e);
    });
    $('html').on('keydown.edit_in_place', function(e) {
       eip.handle_keydown(e);
    });
    
    // редактировать нужно содержимое узла
    if (this.node.data('fx_var')) {
        this.meta = node.data('fx_var');
        this.start(node.data('fx_var'));
    }
    // редактирование атрибутов узла
    for( var i in this.node.data()) {
        if (!/^fx_template_var/.test(i)) {
            continue;
        }
        var meta = this.node.data(i);
        meta.is_att = true;
        this.start(meta);
    }
}

fx_edit_in_place.prototype.handle_keydown = function(e) {
    if (e.which === 27) {
        if (e.isDefaultPrevented && e.isDefaultPrevented()) {
            return;
        }
        this.stop();
        this.restore();
        $fx.front.deselect_item();
        return false;
    }
    if (e.which === 13 && (!this.is_wysiwyg || e.ctrlKey)) {
        $fx.front.deselect_item();
        this.save().stop();
        e.which = 666;
        $(this.node).closest('a').blur();
        return false;
    }
}

fx_edit_in_place.prototype.start = function(meta) {
	var edit_in_place = this;
	switch (meta.type) {
            case 'datetime':
                var field = this.add_panel_field(meta);
                break;
            case 'image': case 'file': 
                var field = this.add_panel_field(
                    $.extend({}, meta, {
                        value:meta.filetable_id || '',
                        path:meta.value && meta.value != '0' ? meta.value : false
                    })
                );
                console.log(meta.value, meta.value ? 'tru' : 'fls');
                field.on('fx_change_file', function() {
                    edit_in_place.save().stop();
                });
                break;
            case 'color':
                this.add_panel_field(meta);
                break;
        case 'select':
            this.add_panel_field(meta);
            break;
        case 'bool':
            this.add_panel_field(meta);
            break;
        case 'string': case 'html': case '': case 'text': case 'int': case 'float':
            if (meta.is_att) {
                this.add_panel_field(meta);
            } else {
                this.is_content_editable = true;
                if (!$($fx.front.get_selected_item()).hasClass('fx_content_essence')) {
                    setTimeout(function() {
                        $fx.front.stop_essences_sortable();
                    }, 50);
                }
                this.node.addClass('fx_var_editable');
                if ( (meta.type === 'text' && meta.html) || meta.type === 'html') {
                    this.is_wysiwyg = true;
                    this.node.data('fx_saved_value', this.node.html());
                    this.make_wysiwyg();
                }
                if (!((meta.type === 'text' && meta.html) || meta.type === 'html')) {
                    this.node.data('fx_saved_value', this.node.html());
                }
                this.node
                    .attr('contenteditable', 'true')
                    .focus();
            }
            break;
	}
        $('html').one('fx_deselect.edit_in_place', function() {
            edit_in_place.save().stop();
	});
};

fx_edit_in_place.prototype.add_panel_field = function(meta) {
    meta = $.extend({}, meta);
    if (meta.var_type === 'visual') {
            meta.name = meta.id;
    }
    if (!meta.type) {
        meta.type = 'string';
    }
    var field = $fx.front.add_panel_field(meta);
    field.data('meta', meta);
    this.panel_fields.push(field);
    return field;
};

fx_edit_in_place.prototype.stop = function() {
    if (this.stopped) {
        return this;
    }
    for (var i =0 ;i<this.panel_fields.length; i++) {
        this.panel_fields[i].remove();
    }
    this.panel_fields = [];
    this.node.data('edit_in_place', null);
    this.node.attr('contenteditable', null);
    this.node.removeClass('fx_var_editable');
    if (this.is_content_editable && this.is_wysiwyg) {
        this.destroy_wysiwyg();
    }
    $('*').off('.edit_in_place');
    this.node.blur();
    this.stopped = true;
    return this;
};

fx_edit_in_place.prototype.save = function() {
    console.log('saving');
    if (this.stopped) {
        console.log('alr st');
        return this;
    }
    var node = this.node;
    var vars = [];
    // редактируем текст узла
    var is_content_editable = this.is_content_editable;
    if (is_content_editable) {
        if (this.is_wysiwyg && this.source_area.is(':visible')) {
            this.node.redactor('toggle');
        }
        var text_val = this.is_wysiwyg ? node.redactor('get') : node.text();
        var html_val = this.is_wysiwyg ? node.redactor('get') : node.html();
        var saved_val = node.data('fx_saved_value');

        if (text_val !== saved_val && html_val !== saved_val ) {
            vars.push({
                'var':this.meta,
                value:this.is_wysiwyg ? html_val : text_val
            });
        }
    }
    for (var i = 0; i < this.panel_fields.length; i++) {
        var pf = this.panel_fields[i];
        var pf_meta= pf.data('meta');
        var old_value = pf_meta.value;
        if (pf_meta.type === 'bool') {
            var c_input = $('input[name="'+pf_meta['name']+'"][type="checkbox"]', pf);
            var new_value = c_input.is(':checked') ? "1" : "0";
        } else {
            var new_value = $(':input[name="'+pf_meta['name']+'"]', pf).val();
        }
    
        if (old_value !== new_value) {
            vars.push({
                'var': pf_meta,
                value:new_value
            });
        }
    }
    // ничего не поменялось
    if (vars.length === 0) {
        return this;
    }
    $fx.front.disable_infoblock(node.closest('.fx_infoblock'));
    $fx.post(
        {
            essence:'infoblock',
            action:'save_var',
            infoblock:this.ib_meta,
            vars: vars,
            fx_admin:true
        }, 
        function() {
            $fx.front.reload_infoblock(node.closest('.fx_infoblock').get(0));
	}
    );
    return this;
};

fx_edit_in_place.prototype.restore = function() {
    if (!this.is_content_editable) {
        return this;
    }
    var saved = this.node.data('fx_saved_value');
    this.node.html(saved);
    return this;
}

fx_edit_in_place.prototype.make_wysiwyg = function () {
    //return;
    var doc = this.node[0].ownerDocument || this.node[0].document;
    var win = doc.defaultView || doc.parentWindow;
    var sel = win.getSelection();
    var is_ok = false;
    if (sel) {
        var cp = sel.focusNode;
        var is_ok = $.contains(this.node[0], sel.focusNode);
    }
    if (is_ok) {
        console.log('add marker')
        var range = sel.getRangeAt(0);
        range.collapse(true);
        range.insertNode($('<span id="fx_marker-1">&#x200b;</span>')[0]);
        range.detach();
    }
    if (!this.node.attr('id')) {
        this.node.attr('id', 'stub'+Math.round(Math.random()*1000));
    }
    $('#fx_admin_control').append('<div class="editor_panel" />');
    var linebreaks = this.meta.var_type == 'visual';
    var _node = this.node;
    this.node.redactor({
        //focus:true,
        linebreaks:linebreaks,
        toolbarExternal: '.editor_panel',
        imageUpload : '/floxim/admin/controller/redactor-upload.php',
        buttons: ['formatting', '|', 'bold', 'italic', 'deleted', '|',
                'unorderedlist', 'orderedlist', 'outdent', 'indent', '|',
                'image', 'video', 'file', 'table', 'link', '|',
                'fontcolor', 'backcolor', '|', 'alignment', '|', 'horizontalrule'],
        initCallback: function() {
            var marker = _node.find('#fx_marker-1');
            var selection = window.getSelection();
            if (selection) {
                var range = document.createRange();
            }
            if (marker.length != 0) {
                range.selectNodeContents(marker[0]);
                range.collapse(false);
                selection.removeAllRanges();
                selection.addRange(range);
                console.log('marker');
                marker.remove();
            } else {
                console.log('start')
                range.setStart(_node[0], 0);
                range.collapse(true);
                selection.removeAllRanges();
                selection.addRange(range);
            }
            this.sync();
            _node.data('fx_saved_value', this.get());
        }

    });

    this.source_area = $('textarea[name="'+ this.node.attr('id')+'"]');
    this.source_area.addClass('fx_overlay');
    this.source_area.css({
        position:'relative',
        top:'0px',
        left:'0px'
    });
};

fx_edit_in_place.prototype.destroy_wysiwyg = function() {

    
    this.node.redactor('destroy');
    var marker = this.node.find('#fx_marker-1');
    marker.remove();
    $('#fx_admin_control .editor_panel').remove();
    this.node.get(0).normalize();
};