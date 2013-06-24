(function($){
    $.fn.edit_in_place = function() {
        var editor = new fx_edit_in_place(this);
    }
})(jQuery);

function fx_edit_in_place( node ) { 
    this.node = node;
    this.node.data('edit_in_place', this);
    this.panel_fields = [];
    this.config_elrte = {
        toolbar: 'fx_einp_toolbar',
        lang:'ru',
        allowSource: false
    };
    
    this.ib_meta = node.closest('.fx_infoblock').data('fx_infoblock');
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
		this.start(this.node.data(i));
	}
}

fx_edit_in_place.prototype.start = function(meta) {
	switch (meta.type) {
		case 'datetime':
			var field = this.add_panel_field(meta);
			break;
		case 'image': case 'file':
			var field_props = $.extend(meta, {
				value:meta.filetable_id,
				path:meta.value
			});
			var field = this.add_panel_field(field_props);
			break;
		case 'html':
			break;
		case 'text': default:
			this.start_text();
			break;
	}
	this.node.one('fx_deselect', function() {
		$(this).data('edit_in_place').stop().save();
	});
            
}

fx_edit_in_place.prototype.add_panel_field = function(meta) {
	var field = $fx.front.add_panel_field(meta);
	this.panel_fields.push(field);
	return field;
}

fx_edit_in_place.prototype.start_text = function () {
	this.node.addClass('fx_var_editable')
            .attr('contenteditable', 'true')
            .data('fx_saved_value', this.node.html())
            .focus();
};

fx_edit_in_place.prototype.stop = function() {
	for (var i =0 ;i<this.panel_fields.length; i++) {
		this.panel_fields[i].remove();
	}
	this.panel_fields = [];
	return this;
}

fx_edit_in_place.prototype.save = function() {
	var node = this.node;
	var val = node.html().replace(/<br[\s\/]*?>$/, '');
	if (val == node.data('fx_saved_value') ) {
		return;
	}
	$fx.post({
		essence:'infoblock',
		action:'save_var',
		infoblock:this.ib_meta,
		'vars': [
			{'var':this.meta,value:val}
		],
		fx_admin:true
	}, function(res) {
		node.html(val);
		node.data('fx_saved_value', val);
	});
	return this;
}
