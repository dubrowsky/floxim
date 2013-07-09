(function($){
    $.fn.edit_in_place = function() {
        if (!this.data('edit_in_place')) {
            this.data('edit_in_place', new fx_edit_in_place(this));
        }
    }
})(jQuery);

function fx_edit_in_place( node ) { 
    this.node = node;
    this.panel_fields = [];
    this.config_elrte = {
        toolbar: 'fx_einp_toolbar',
        lang:'ru',
        allowSource: false
    };
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
    if (e.which == 27) {
        if (e.isDefaultPrevented && e.isDefaultPrevented()) {
            return;
        }
        this.stop();
        this.restore();
        $fx.front.deselect_item();
        return false;
    }
    if (e.which == 13 && (!this.is_wysiwyg || e.ctrlKey)) {
        this.save().stop();
        $fx.front.deselect_item();
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
			console.log('dtf', field);
			break;
		case 'image': case 'file': 
			var field = this.add_panel_field(
				$.extend({}, meta, {
					value:meta.filetable_id,
					path:meta.value
				})
			);
			field.on('fx_change_file', function() {
				edit_in_place.save().stop();
			});
			break;
		case 'color':
			this.add_panel_field(meta);
			break;
        case 'select':
            console.log(meta);
            this.add_panel_field(meta);
            break;
		case 'string': case 'html': case '': case 'text':
			if (meta.is_att) {
				this.add_panel_field(meta);
			} else {
                this.is_content_editable = true;
				this.node.addClass('fx_var_editable')
					.attr('contenteditable', 'true')
					.data('fx_saved_value', this.node.html())
					.focus();
                if ( (meta.type == 'text' && meta.html) || meta.type == 'html') {
                    this.is_wysiwyg = true;
                    this.make_wysiwyg();
                }
			}
			break;
	}
	this.node.closest('.fx_selected').one('fx_deselect.edit_in_place', function() {
		edit_in_place.save().stop();
	});
}

fx_edit_in_place.prototype.add_panel_field = function(meta) {
    meta = $.extend({}, meta);
	if (meta.var_type == 'visual') {
		meta.name = meta.id;
	}
    if (!meta.type) {
        meta.type = 'string';
    }
	var field = $fx.front.add_panel_field(meta);
	field.data('meta', meta);
	this.panel_fields.push(field);
	return field;
}

fx_edit_in_place.prototype.stop = function() {
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
	return this;
}

fx_edit_in_place.prototype.save = function() {
	var node = this.node;
	var vars = [];
	// редактируем текст узла
    var is_content_editable = this.is_content_editable;
	if (is_content_editable) {
        if (this.is_wysiwyg && this.source_area.is(':visible')) {
            this.node.redactor('toggle');
        }
        var val = this.is_wysiwyg ? node.html() : node.text();
		if (val != node.data('fx_saved_value') ) {
			vars.push({
				'var':this.meta,
				value:val
			});
		}
	}
	for (var i = 0; i < this.panel_fields.length; i++) {
		var pf = this.panel_fields[i];
		var pf_meta= pf.data('meta');
		var old_value = pf_meta.value;
		var new_value = $(':input[name="'+pf_meta['name']+'"]', pf).val();
		if (old_value != new_value) {
			vars.push({
				'var': pf_meta,
				value:new_value
			});
		}
	}
	// ничего не поменялось
	if (vars.length == 0) {
		return this;
	}
    
    $fx.post({
		essence:'infoblock',
		action:'save_var',
		infoblock:this.ib_meta,
		vars: vars,
		fx_admin:true
	}, function(res) {
		if (is_content_editable) {
			node.html(val);
			node.data('fx_saved_value', val);
		}
		$fx.front.reload_infoblock(node.closest('.fx_infoblock').get(0));
	});
	return this;
}

fx_edit_in_place.prototype.restore = function() {
    if (!this.is_content_editable) {
        return this;
    }
    var saved = this.node.data('fx_saved_value');
    
    this.node.html(saved);
    return this;
}

fx_edit_in_place.prototype.make_wysiwyg = function () {
    if (!this.node.attr('id')) {
        this.node.attr('id', 'stub'+Math.round(Math.random()*1000));
    }
    $('#fx_admin_control').append('<div class="editor_panel" />');
    var linebreaks = this.meta.var_type == 'visual';
    this.node.redactor({
        focus:true,
        linebreaks:linebreaks,
        toolbarExternal: '.editor_panel',
        imageUpload : '/floxim/admin/controller/redactor-upload.php',
        buttons: ['html', '|', 'formatting', '|', 'bold', 'italic', 'deleted', '|',
                'unorderedlist', 'orderedlist', 'outdent', 'indent', '|',
                'image', 'video', 'file', 'table', 'link', '|',
                'fontcolor', 'backcolor', '|', 'alignment', '|', 'horizontalrule']

    });
    this.source_area = $('textarea[name="'+ this.node.attr('id')+'"]');
    this.source_area.addClass('fx_overlay');
    this.source_area.css({
        position:'relative',
        top:'0px',
        left:'0px'
    });
}

fx_edit_in_place.prototype.destroy_wysiwyg = function() {
    this.node.redactor('destroy');
    $('#fx_admin_control .editor_panel').remove();
}