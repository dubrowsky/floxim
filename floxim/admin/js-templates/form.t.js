<!--[form_row]-->
<?
if ( (_c.type == 'checkbox' || _c.type == 'bool') && typeof _c.values == 'undefined') {
?>
<div class="field field_checkbox">
	<?=$t.input(_c)?>
	<label style="display:inline;" for="<?=_c.name?>"><?=_c.label?></label>
</div>
<?
} else if ((_c.type == 'select' || _c.type == 'radio') && _c.hidden_on_one_value && !_c.extendable && _c.values && fx_object_length(_c.values) == 1) {
	print($t.input({type:'hidden', name:_c.name,value:fx_object_get_first_key(_c.values)}));
} else if (_c.type == 'hidden' || _c.type == 'button' || _c.type == 'list' || _c.type == 'iconselect') {
	print($t.input(_c));
} else {
?>
<div class="field<?=_c.className ? ' '+_c.className : ''?>">
	<?=$t.label(_c)?>
	<?=$t.input(_c)?>
</div>
<?}?>




<!--[data_input]-->
<input type="hidden" value="<?=$t.clear($.toJSON(_c))?>" class="data_input" />

<!--[data_attr]-->
<?
print('data-', (_o && _o.key != undefined ? _o.key : 'inline'),	 '="', $t.clear($.toJSON(_c)), '"');
?>

<!--[label]-->
<div class="form_label">
	<label for="<?=_c.name?>"><?=_c.label?></label>
	<?if (_c.current) {?>
		<span class="fx_admin_current"> (по умолчанию &mdash; <?=_c.current?>)</span>
	<?}?>
</div>

<!--[field_label]-->
<label class="<?=_c.status ? 'ui-corner-all ui-state-'+_c.status : ''?>">
<?=_c.label || _c.value?>
</label>

<!--[field_id_name]-->
name="<?=_c.name?>" id="<?=_c.name?>"

<!--[input]-->
	<input 
		type="<?=_c.password ? 'password' : 'text'?>" 
		<?=$t.field_id_name(_c)?> 
		value="<?=$t.clear(_c.value || '')?>" />

<!--[input]-->
<input <?=$t.field_id_name(_c)?> value="<?=$t.clear(_c.value)?>" type="hidden" />
<!--test-->
_c.type == 'hidden'

<!--[input]-->
<input type="file" <?=$t.field_id_name(_c)?> />
<?if (_c.old) {?>
	<div class="old">
		<span>РўРµРєСѓС‰РёР№ С„Р°Р№Р»: </span><a target="_blank" href="<?=_c.path?>"><?=_c.real_name?></a>
		<a class="delete_link delete_<?=_c.old?>">СѓРґР°Р»РёС‚СЊ</a>
		<input type="hidden" name="<?=_c.name?>[delete]" value="-1" />
	</div>
<?}?>
<!--test-->
_c.type == 'file'
<!--jquery:form_row-->
html.find('a.delete_link').click(function(){
	var delete_id = this.className.match(/delete_(\d+)/)[1];
	var par = $(this).parent();
	par.find('input').val(delete_id);
	par.hide();
});


<!--[input]-->
<div class="image_field">
    <div class="file_input">
    	<input type="file" name="file" id="image_file" />
	</div>
	<a class="download_file" style="cursor: pointer">Загрузить изображение</a>
    <input type="hidden" <?=$t.field_id_name(_c)?> />
    <span class="real_name"><?=_c.old ? _c.real_name : ''?></span>
    <br style="clear:both;" />
	<!-- img style="max-width:100px; float:left;" <?=_c.old ? 'src="'+_c.path+'"' : ''?> / -->
</div>
<!--test-->
_c.type == 'image'
<!--jquery:form_row-->

/* html.find('div.image_field div.file_input input[type=file]').on('change',function () { */
html.find('div.image_field a.download_file').on('click', function(){
	console.log('ololo');
    $.ajaxFileUpload({
        /* url:'_test/doajaxfileupload.php', */
        url:'/floxim/index.php',
        secureuri:false,
        fileElementId:'image_file',
        dataType: 'json',
        /* data: { name:'logan', id:'id' }, */
        /* TODO: выяснить что передавать в fx_admin */
        data: { essence:'file', fx_admin:1, action:'upload_save' },
        success: function ( data, status ) {
            /*$('div.image_field div.file_input').html('');
            $('div.image_field div.file_input').html('<input type="file" name="file" id="image_file" />');*/
        },
        error: function (data, status, e) {
        	console.log(e);
        }
    });
});

<!--[input]-->
<?
var c_val = _c.value || '';
c_val = $t.clear(c_val);
?>
<textarea 
	style="height:190px; margin-bottom:40px;"
	<?=$t.field_id_name(_c)?>
	class="<?=_c.wysiwyg ? 'fx_wysiwyg' : ''?> <?=_c.code ? 'fx_code fx_code_'+_c.code : ''?>"><?=c_val?></textarea>
<!--test-->
_c.field_type == 'textarea'

<!--jquery:form_row-->
html.find('textarea.fx_code').each( function(){
	var code_type = this.className.match(/fx_code_([^\s]+)/)[1];
	if (!window.CodeMirror) {
		var codemirror_path = '/floxim/lib/codemirror/';
		$.ajax({
				url:codemirror_path+'codemirror.all.php',
				dataType:'script',
				async:false,
				success:function(res) {
				}
		});
		$(document.head).append('<link type="text/css" rel="stylesheet" href="'+codemirror_path+'lib/codemirror.css" />');
	}
	var config_map = {
		html:'htmlmixed',
		php:'php',
		css:'css'
	};
	
	code_type = config_map[code_type] || 'htmlmixed';
	
	var config = {
		mode:code_type,
		lineNumbers: true,
		matchBrackets: true,
		tabMode: "indent"
	};
	var cCodeMirror = CodeMirror.fromTextArea(this, config);
	$(this).data('codemirror', cCodeMirror);
	setTimeout(function() {
			cCodeMirror.refresh();
	},50);
	
	var save_cm_fields = function() {
		$('textarea.fx_code', $(this)).each(function() {
				$(this).data('codemirror').save();
		});
		return false;
	};
	
	$(document.body).off('fx_form_submit.save_cm_fields').on('fx_form_submit.save_cm_fields', 'form', save_cm_fields);
});

<!--[input]-->
<?
if (_c.extendable) {
	_c.values.fx_new = _c.extendable === true ? 'Add...' : _c.extendable;
}
?>
	<select 
		id="<?=_c.id? _c.id : _c.name?>"
		name="<?=_c.name+(_c.multiple? '[]' : '')?>"
		class="<?=_c.inline ? 'inline' : ''?> <?=_c.extendable ? ' extendable' : ''?>"
		<?=_c.multiple ? ' multiple="multiple"' : ''?>>
		<?for (var vk in _c.values) {
            var val = _c.values[vk], opt_name = null;
            if (typeof val == 'string') {
                var opt_val = vk;
                var opt_name = val;
            } else if (val instanceof Array) {
                var opt_val = val[0];
                var opt_name = val[1];
            }
            
			if (opt_name !== null) {
				var is_selected = opt_val == _c.value || ( _c.value instanceof Array  && $.inArray(opt_val, _c.value) > -1);
				?><option value="<?=opt_val?>"<?=is_selected ? ' selected="selected"':  ''?>><?=opt_name?></option><?
			}
		}?>
	</select>
	<?if (_c.extendable){?>
		<input type="text" name="fx_new_<?=_c.name?>" class="new" style="display:none;" />
	<?}?>
<!--test-->
_c.type == 'select'
<!--jquery:form_row-->
html.find('select.extendable').change(function() {
	var new_inp = $(this).parent().find('input.new');
	$(this).val() === 'fx_new' ? new_inp.show().focus() : new_inp.hide();
});

<!--[input]-->
<div class="fx_admin_group">
	<?for (var vk in _c.values) {
		var i = 0;
		if (typeof _c.values[vk] == 'string') {
			i++;
			var is_checked = (_c.value && _c.value == vk) || (!_c.value && _c.selected_first && i == 1);?>
			<label class="fx_admin_radio_label">
				<input 
					type="radio" 
					value="<?=vk?>" 
					id="<?=_c.name+'_'+vk?>"
					name="<?=_c.name?>"
					<?=is_checked  ? ' checked="checked"' : ''?>>
					<?=_c.values[vk]?>
			</label>
		<?
		}
	}?>
</div>
<!--test-->
_c.type == 'radio'

<!--[input]-->
<?
var c_val = _c.value !== undefined ? _c.value : fx_object_get_first_key(_c.values);
?>
<div class="fx_admin_radio_facet">
	<input type="hidden" <?=$t.field_id_name(_c)?> value="<?=c_val?>" />
	<?for(var vk in _c.values) {?>
		<span class="val_<?=vk?> <?=vk == c_val ? 'fx_admin_radio_facet_current' : ''?>"><?=_c.values[vk]?></span>
	<?}?>
</div>
<!--test-->
_c.type == 'radio_facet'
<!--jquery:form_row-->
html.find('.fx_admin_radio_facet span').click(function () {
	var par = $(this).parent();
	par.find('.fx_admin_radio_facet_current').removeClass('fx_admin_radio_facet_current');
	$(this).addClass('fx_admin_radio_facet_current');
	par.find('input').val(this.className.match(/val_([^\s]+)/)[1]).change();
});

<!--[input]-->
<input type="checkbox" style="display:inline;" <?=$t.field_id_name(_c)?> value="1" <?=_c.value && _c.value !== '0' ? ' checked="checked"' : ''?> />

<!--test-->
(_c.type == 'checkbox' || _c.type == 'bool') && typeof _c.values == 'undefined'

<!--[input]-->
<div class="fx_admin_group fx_admin_multi_checkbox">
	<?
	for (var vk in _c.values) {
		var iid = _c.name+'_'+vk;
		var is_checked = (_c.value instanceof Array && $.inArray(vk, _c.value) > -1) || ( _c.value == vk);
		?>
		<div class="val">
			<input 
				style="display:inline;" 
				type="checkbox"
				id="<?=iid?>" name="<?=_c.name?>[]" 
				value="<?=vk?>" 
				<?=is_checked ? ' checked="checked"' : ''?> />
			<label class="fx_admin_checkbox_label" for="<?=iid?>"><?=_c.values[vk]?></label>
		</div>
	<?}?>
</div>
<!--test-->
_c.type == 'checkbox' && typeof _c.values != 'undefined' 

<!--[input]-->
<?_c.value = _c.value || '#00ff00';?>
<div class="field_colorpicker">
	<input <?=$t.field_id_name(_c)?> value="<?=_c.value?>" type="hidden" />
	<div style="position:relative; height:36px;">
		<div class="colorSelector2"><div style="background-color: <?=_c.value?>"></div></div>
		<div class="colorpickerHolder2"></div>
	</div>
</div>
<!--test-->
_c.type == 'color'
<!--jquery:form_row-->
html.find('.field_colorpicker').each(function() {
	$('head').append('<link rel="stylesheet" media="screen" type="text/css" href="/floxim/lib/colorpicker/css/colorpicker.css" />'+
		'<script type="text/javascript" src="/floxim/lib/colorpicker/js/colorpicker.js"></script>');
	var cp = $(this);
	var holder = $('.colorpickerHolder2', cp);
	var widt = false;
	holder.ColorPicker({
		flat: true,
		color: _c.value,
		onSubmit: function(hsb, hex, rgb, el) {
			$('.colorSelector2 div', cp).css('backgroundColor', '#' + hex);
			holder.stop().animate({
				height: widt ? 0 : 173
			}, 500);
			$('input', cp).attr('value', '#' + hex);
			widt = !widt;
		}
	});
    $('>div', holder).css('position', 'absolute');
    $('.colorSelector2 div', cp).css('backgroundColor', _c.value);

	$('.colorSelector2', cp).bind('click', function() {
		$('.colorpickerHolder2', cp).stop().animate({
			height: widt ? 0 : 173
		}, 500);
		widt = !widt;
	});
});


<!--[input]-->
<div class="fx_iconselect">
	<div class="fx_iconselect_filter">
		<select>
			<option value="">Все</option><?
			var groups = {}, c_val, group_index = 0;
			
			for(var vk in _c.values) {
				var v = _c.values[vk];
				
				if (_c.value && _c.value == v.id ) {
					c_val = v.id !== undefined ? v.id : vk;
				}
				
				if (v.group !== undefined && groups[v.group] === undefined) {
					group_index++;
					groups[v.group] = group_index;
					?><option value="<?=group_index?>" <?=_c.group == v.group ? ' selected="selected"' : ''?>><?=v.group?></option><?
				}
			}
			?>
		</select>
		<div style="clear:both;"></div>
		<input type="hidden" class="iconselect_value" <?=$t.field_id_name(_c)?> value="<?=c_val?>" />
		<div class="items"><?
		for (var vk in _c.values) {
			var v = _c.values[vk];
			?><div class="item group_<?=groups[v.group]?> <?=c_val == v.id ? ' selected' : ''?>">
			<?=$t.data_input({filter_string: v.name.toLowerCase(), value:v.id,key:vk})?>
				<img src="<?=v.icon?>" /><div class="name"><?=v.name?></div>
			</div><?
		}?>
		</div>
	</div>
</div>
<!--test-->
_c.type == 'iconselect'

<!--jquery:form_row-->
html.find('.item').click(function() {
	var i = $(this);
	var par = i.closest('.fx_iconselect');
	var data = $t.inline_data(i);
	$('.selected', par).removeClass('selected');
	i.addClass('selected');
	par.find('input.iconselect_value').val(data.value !== undefined ? data.value : data.key).change();
});

html.find('select').change( function(){
	var sel = $(this);
	var groupval = $("option:selected", sel).val();
	var is = sel.closest('.fx_iconselect');
	
	if (groupval == '') {
		$('.item', is).show();
	} else {
		$('.item', is).hide();
		setTimeout(function(){$('.group_'+groupval, is).show();},50);
	}
});

<!--[input]-->
<div class="fx_itemselect">
<?for (var vk in _c.values) {?>
	<div class="fx_itemselect_item value_<?=vk?> <?=_c.value && _c.value.contains(vk) ? 'selected' : ''?>">
		<?=_c.values[vk]?>
	</div>
<?}?>
<br style="clear:both;" />
</div>
<!--test-->
_c.type == 'itemselect'

<!--jquery:form_row-->
html.find('.fx_itemselect').each( function() {
	var is = $(this); 
	is.update_value = function() {
		$('input', is).remove();
		$('.selected', is).each( function() {
			var id = this.className.match(/value_([^\s]+)/)[1];
			var name = _c.name +( _c.multiple ? '[]' : '' );
			is.append('<input type="hidden" name="'+name+'" value="'+id+'" />');
		});
	};
	$('.fx_itemselect_item', is).click(function(e){
		if (_c.multiple && (e.metaKey || e.ctrlKey)) {
			$(this).toggleClass('selected');
		} else {
			$('.selected', is).removeClass('selected');
			$(this).addClass('selected');
		}
		is.update_value();
	});
	is.update_value();
});

<!--[input]-->
<div class="fx_fieldset" id="fx_fieldset_<?=_c.name?>">
	<div class="fx_fieldset_label">
	<?for(var i = 0; i < _c.labels.length; i++) {
		if (typeof _c.labels[i] != 'string') {continue;}?>
		<td><label><?=_c.labels[i]?></label></td>
	<?}?>
	</div>
	<div class="fx_fieldset_rows">
	<?
	if (!_c.values) {
		_c.values = [];
	}
	for (var row = 0; row < _c.values.length; row++) {
		var val_num = 0;
		var inputs = [];
		for (var  val_key in _c.values[row]) {
			inputs.push($.extend( {}, _c.tpl[val_num], {
				name:_c.name+'['+val_key+']['+val_num+']',
				value:_c.values[row][val_key]
			}));
			val_num++;
		}
		print($t.fieldset_row(inputs, {index:row}));
		?>
	<?}?>
	</div>
	<?if ( _c.without_add === undefined ) {?>
		<span class="fx_fieldset_add">Добавить</span>
	<?}?>
	<br style="clear:both;" />
</div>
<!--test-->
_c.type == 'set'

<!--jquery:form_row-->
html.find('.fx_fieldset').each(function() {
	var fs = $(this);
	fs.on('click', '.fx_fieldset_remove', function() {
		$(this).parents('.fx_fieldset_row').remove();	
	});
	$('.fx_fieldset_add', fs).click( function() {
		var inputs = [];
		var index = $('.fx_fieldset_row', fs).length > 0 ? $('.fx_fieldset_row', fs).last().get(0).className.match(/row_(\d+)/)[1]*1 + 1 : 1;
		for (var i = 0; i < _c.tpl.length; i++) {
			inputs.push( $.extend({}, _c.tpl, {name:_c.name+'['+_c.tpl[i].name+']['+index+']'}));
		}
		$('.fx_fieldset_rows', fs).append($t.jQuery('fieldset_row', inputs, {index:index}));
	});
});

<!--[fieldset_row]-->
<div class="fx_fieldset_row row_<?=_o.index?>">
	<?for (var i = 0; i< _c.length; i++) {
		print($t.input(_c[i]));
	}?>
	<span class="fx_fieldset_remove">Удалить</span>
</div>

<!--[input]-->
<table>
	<?if (_c.labels) {?>
		<tr>
			<?$.each(_c.labels, function(k, v) {?>
				<th><?=_c.labels[i]?></th>
			<?});?>
		</tr>
	<?} 
	if (_c.values) {
		$.each(_c.values, function(k, row) {?>
			<tr>
			<?$.each(_v, function(key,value){?>
				<td><?=value?></td>
			<?});?>
			</tr><?
		});
	}?>
</table>
<!--test-->
_c.type == 'table'

<!--[input]-->
	<button class="fx_button" <?=$t.data_attr(_c)?>>
		<?=_c.label || ''?>
	</button>
<!--test-->
_c.type == 'button'

<!--jquery:form_row-->
html.find('.fx_button').add(html.filter('.fx_button')).click(function() {
	var data = $t.inline_data($(this));
	if ( data.postdata || data.post ) {
		var postdata = data.postdata || data.post;
		if (data.send_form) {
			var form = $(this).closest('form');
			var formdata = {};
			$.each(form.serializeArray(), function(num, field) {
				formdata[field.name] = field.value;
			});
			postdata = $.extend(formdata, postdata);
		}
		$fx.post(postdata);
		return false;
	}
	if (data.func) {
		fx_call_user_func(json.func);
	}
	if (data.dialog) {
		$fx.post(data.dialog, function(res){
			 $fx_dialog.open_dialog(res);
		});
		return false;
	}
	if (data.url) {
		document.location.hash = $fx.mode + '.' + data.url.replace(/^#/, '');
	}
	return false;
});

<!--[input]-->
<div class="fx_list<?=_c.className ? ' '+_c.className : ''?>">
<?if (_c.filter && _c.values && _c.values.length > 10) {?>
	<div class="fx_list_filter"><label>Найти:<input type="text" /></label></div>
<?}
?>
<input type="hidden" <?=$t.field_id_name(_c)?> class="list_value_input"/>
<table class="fx_list_table">
<?
if (_c.labels !== undefined && _c.values ) {?>
	<tr class="fx_list_labels">
		<?$.each(_c.labels, function(name, label){?>
			<td>
				<?=typeof label == 'object' ? label.label : label?>
				<?
				if (label.filter && label.filter == 'select') {
					var l_values = {};
					$.each(_c.values, function(i,value) {
						l_values[value[name]]=true;
					});
					?>
					<select class="filter filter_<?=name?>">
						<option value="">--все--</option>
						<?
						$.each(l_values, function(value_name, i) {
							?><option value="<?=value_name?>"><?=value_name?></option><?
						});
						?>
					</select>
					<?
				} else if (label.filter == 'text') {
					?><input class="filter filter_<?=name?>" type="text" placeholder="search..." /><?
				}
				?>
			</td>
		<?});?>
	</tr>
<?}
if (_c.values) {
	$.each(_c.values, function(i, value) {
		?><tr class="fx_list_row <?=value.unchecked ? 'fx_admin_unchecked' : ''?> <?=i == 0 ? ' fx_list_row_first' : ''?>" <?=$t.data_attr(value)?>>
			<?
			print($t.list_field_row(value, {labels:_c.labels}));
			?>
		</tr><?
	});
}
?>
</table>
</div>

<!--test-->
_c.type == 'list'

<!--jquery:form_row-->
html.filter('.fx_list').each( function() {
	var list =  $(this);
	$('.fx_list_filter input', list).keyup(function(){
		var t = $(this).val().toLowerCase();
		$('.fx_list_row', list).each(function(){
			var row = $(this);
			row.text().toLowerCase().indexOf(t) !== -1 ? row.show() : row.hide();
		});
	});
	function filter_list() {
		var vals = [];
		$('.filter', list).each(function(){
			var cv = $(this).val();
			if (cv != '') {
				vals.push(cv.toLowerCase());
			}
		});
		
		$('.fx_list_row', list).each(function(){
			var row = $(this);
			var row_text = row.text().toLowerCase();
			row.show();
			$.each(vals, function(vid, v) {
				if (row_text.indexOf(v) === -1) {
					row.hide();
				}
			});
		});
	}
	$('select.filter', list).change(filter_list);
	$('input:text.filter', list).keyup(filter_list);
	function set_input_value() {
		var inp = list.find('input.list_value_input');
		var ids = [];
		list.find('.fx_admin_selected').each(function(){
			ids.push( $(this).data('id'));
		});
		inp.val(ids.join(','));
	}
	$('.fx_list_row', list).each( function() {
			var row = $(this);
			var value = $t.inline_data(row);
			row.data(value);
			if ( value.id !== undefined ) {
				row.click(function(e){
					var c_row = $(this);
					if (e.metaKey || e.ctrlKey) {
						c_row.toggleClass('fx_admin_selected');
					} else {
						if ($(this).hasClass('fx_admin_selected') && $('.fx_list_row.fx_admin_selected').length === 1) {
							c_row.removeClass('fx_admin_selected');
						} else {
							$('.fx_list_row').removeClass('fx_admin_selected');
							c_row.addClass('fx_admin_selected');
						}
					}
					if (c_row.parents('.ui-dialog').length == 0) {
						fx_form.update_available_buttons();
					}
					set_input_value();
				});
			}
			$(this).hover(function() {
				$(this).addClass('fx_list_row_hover');	
			}, function() {
				$(this).removeClass('fx_list_row_hover');
			});
	});
	list.sortable({
		disabled: false,
		placeholder: 'fx_sortable_placeholder',
		axis: 'y',
		cursor: 'move',
		start: function(event, ui){ 
			var h = ui.item.height();
			if ( h < 5  ) {
				h = 50;
			}
			ui.placeholder.height(h); 
		},
		items:'.fx_list_row',
		update: function(event, ui) {
			var pos = [];
			var order = $('.fx_list_row', $(this)).sortable('serialize');      
			if ( order.length < 1 ) return false;
			order.each( function() {
				var id = $(this).data('id');
				pos.push( id );
			});
			var post = {
				'essence': $fx.admin.get_essence(),
				'action': 'move',
				'positions': pos
			};
			$fx.post_front(post);
		}
	});
});
$fx_form.update_available_buttons();

<!--[list_field_row]-->
<?
var labels_length = $t.countLength(_o.labels);
var i = 0;
$.each(_o.labels, function(label_key, label) {
	i++;
	?><td class="fx_list_cell<?=i == labels_length ? ' fx_list_cell_last' : ''?>"><?=$t.list_field_cell(_c[label_key], {label:label,row:_c})?></td><?
});
?>

<!--[list_field_row]-->
<?if (_c.img) {?>
	<td class="fx_list_cell fx_list_cell_image"><?=$t.list_field_cell(_c.img,{type:'image'})?></td>
<?}?>
<td class="fx_list_cell fx_list_cell_last">
	<h2><?
	if (_c.header.url) {
		var url = /^\#/.test(_c.header.url) ? _c.header.url : '#' + $fx.mode + '.' + _c.header.url;
		?><a href="<?=url?>"><?=_c.header.name?></a><?
	} else {
		print(_c.header);
	}?></h2><?
	if (_c.text) {
		?><p class="fx_list_text"><?=_c.text?></p><?
	}
	if (_c.buttons) {
		print($t.list_field_cell(_c.buttons,{type:'buttons'}));
	}
	?>
</td>

<!--test-->
_o.labels === undefined || _o.labels.length == 0

<!--[list_field_cell]-->
<?=_c?>

<!--[list_field_cell]-->
<?
var url = _c.url == null ? null : (/^#/.test(_c.url) ? _c.url : '#' + $fx.mode + '.' + _c.url);
?>
<a <?=url == null ? '' : 'href="'+url+'"'?>><?=_c.name?></a>

<!--test-->
_c && _c.url !== undefined


<!--[list_field_cell]-->
<?=$t.input({type:'button',post:_c.button, label:_c.name})?>
<!--test-->
_c && _c.button !== undefined

<!--[list_field_cell]-->
<div class="fx_list_buttons">
	<?$.each(_c, function(i, button) {
		?><span><?=$t.input($.extend(button, {type:'button'}))?></span><?
	});?>
</div>

<!--test-->
_o && (_o.type == 'buttons' || _o.label && _o.label.type == 'buttons')

<!--[list_field_cell]-->
<img src="<?=_c?>" class="fx_list_image" />

<!--test-->
_o && (_o.label && _o.label.type == 'image' || _o.type == 'image') 

<!--[list_field_row]-->
<div class="fx_list_row_item"><img style="width:130px;" src="<?=_c.img?>" /></div>
<div class="fx_list_row_item" style="width:600px;">
	<h2><?
	if (_c.header.url) {
		?><a href="#<?=$fx.mode?>.<?=_c.header.url?>"><?=_c.header.name?></a><?
	} else {
		print(_c.header);
	}?></h2>
	<?=_c.text?>
</div>
<!--test-->
_o.template == 'imgh'

<!--[input]-->
<?
var colors = {
	red: ['#ff0000', 'Red'],
	orange: ['#FB940B', 'Orange'],
	yellow: ['#FFFF00', 'Yellow'],
	green: ['#00FF00', 'Green'],
	lightblue: ['#00FFFF', 'Light-blue'],
	blue: ['#0000FF',  'Blue'],
	purple: [ '#FF00FF', 'Purple'],
	grey:  ['#C0C0C0', 'Grey'],
	black: ['#000000',  'Black']
};
?><div class="fx_admin_colorbasic">
<input type="hidden" <?=$t.field_id_name(_c)?> value="<?=_c.value?>" /><?
$.each(colors, function (key, val) {
	val.push(key);
	?><div><span style="background-color:<?=val[0]?>;" class="color <?=key == _c.value ? 'fx_admin_colorbasic_selected' : ''?>" <?=$t.data_attr(val)?>></span></div><?
});
?></div>
<!--test-->
_c.type == 'colorbasic'

<!--jquery:form_row-->
$('.fx_admin_colorbasic', html).each( function() {
	var cb = $(this);
	$('.color', cb).click( function() {
		var c = $(this);
		$('.fx_admin_colorbasic_selected', cb).removeClass('fx_admin_colorbasic_selected');
		c.addClass('fx_admin_colorbasic_selected');
		$('input', cb).val( $t.inline_data( c )[2] );
	});
});

<!--[input]-->
<div class="fx_tree">
<input type="hidden" <?=$t.field_id_name(_c)?> class="tree_value_input"/>
<?=$t.tree_children(_c,{is_expanded:true})?>
</div>
<!--test-->
_c.type =='tree'

<!--jquery:form_row-->

function fx_tree_render(root) {
	function expand_item(item) {
		get_children(item).addClass('fx_tree_children_expanded');
		get_expander(item).addClass('fx_tree_expander_expanded');
		fx_fields.tree_open_node[item.data('id')] = true;
	}
	
	function collapse_item(item) {
		get_children(item).removeClass('fx_tree_children_expanded');
		get_expander(item).removeClass('fx_tree_expander_expanded');
		fx_fields.tree_open_node[item.data('id')] = false;
	}
	
	function is_expanded(item) {
		return get_children(item).hasClass('fx_tree_children_expanded');
	}
	
	function get_children(item) {
		return item.find('>ul');
	}
	
	function get_expander(item) {
		return item.find('.fx_tree_expander').first();
	}
	
	function toggle_item(item) {
		is_expanded(item) ? collapse_item(item) : expand_item(item);
	}
	
	root.on('click', '.fx_tree_expander', function() {
		toggle_item($(this).closest('.fx_tree_item'));
	}); 
	
	var tree_root = $('.fx_tree>ul', root);
	
	tree_root.nestedSortable({
		disableNesting: 'no-nest',
		distance:5,
		forcePlaceholderSize: true,
		handle: 'div',
		helper:	'clone',
		items: 'li.fx_tree_item',
		maxLevels: 10,
		opacity: .6,
		placeholder: 'placeholder',
		revert: 0,
		tabSize: 25,
		tolerance: 'pointer',
		toleranceElement: '> div',
		listType:'ul',
		start: function(e, ui) {
			collapse_item(ui.helper);
			collapse_item(ui.item);
			ui.helper.css({height:'auto'});
			ui.placeholder.css({height:ui.helper.height()+'px'});
			setTimeout(function() {
				tree_root.nestedSortable('refreshPositions');
			}, 100);
		},
		change: function(e, ui) {
			var par = $(ui.placeholder).closest('.fx_tree_item');
			if (par.length > 0 && !is_expanded(par)) {
				expand_item(par);
				setTimeout(function() {
					tree_root.nestedSortable('refreshPositions');
				}, 100);
			}
		},
		update: function(e,ui) {
			fx_tree_refresh(root);
		},
		sort: function(e, ui) {
			var p_o = ui.placeholder.offset();
			var h_o = ui.helper.offset();
			var too_far = 150;
			if (Math.abs( p_o.top - h_o.top) > too_far || Math.abs(p_o.left - h_o.left) > too_far) {
				ui.placeholder.addClass('placeholder_invalid');
			} else {
				ui.placeholder.removeClass('placeholder_invalid');
			}
		},
		stop: function(e, ui) {
			if (ui.placeholder.hasClass('placeholder_invalid')) {
				tree_root.nestedSortable('cancel');
				return;
			}
			var options = {
				fx_admin:1, 
				essence:'subdivision', 
				posting:1, 
				action:'move',
				darged : ui.item.data('id')
			};
			var next_item = ui.item.next('.fx_tree_item');
			var prev_item = ui.item.prev('.fx_tree_item');
			if (next_item.length > 0) {
				options.type = 'before';
				options.target = next_item.data('id');
			} else if (prev_item.length > 0) {
				options.type = 'after';
				options.target = prev_item.data('id');
			} else {
				options.target = ui.item.parent().closest('.fx_tree_item').data('id') || 0;
				options.type = 'last';
			}
			$.post($fx.settings.action_link, options);
		}
	});
	tree_root.disableSelection();
	
	root.on('click', '.fx_tree_label', function(e){
		var c_row = $(this);
		if (e.target != this && !$(e.target).hasClass('fx_tree_title')) {
			return;
		}
		if (e.metaKey || e.ctrlKey) {
			c_row.toggleClass('fx_admin_selected');
		} else {
			if ($(this).hasClass('fx_admin_selected') && $('.fx_tree_label.fx_admin_selected').length === 1) {
				$(this).removeClass('fx_admin_selected');
			} else {
				$('.fx_tree_label').removeClass('fx_admin_selected');
				$(this).addClass('fx_admin_selected');
			}
		}
		if (!c_row.data('id')) {
			c_row.data('id', c_row.closest('.fx_tree_item').data('id'));
		}
        root.find('.tree_value_input').val(c_row.data('id'));
		$fx_form.update_available_buttons();
	});
	
	fx_tree_refresh(root);
	
	for (var iid in fx_fields.tree_open_node) {
		if (fx_fields.tree_open_node[iid]) {
			expand_item( $('#fx_tree_item_'+iid, root) );
		}
	}
}

function fx_tree_refresh(root) {
	$('.fx_tree_expander', root).hide();
	$('.fx_tree_item', root).each( function(){
		var exp = $('.fx_tree_expander', $(this)).first();
		if ($('>ul > .fx_tree_item', $(this)).length > 0) {
			exp.show();
		}
	});
}
if ($('.fx_tree',html).length > 0) {
	fx_tree_render(html);
}

<!--[tree_children]-->
<ul class="fx_tree_children<?=_o && _o.is_expanded ? ' fx_tree_children_expanded' : ''?>">
<?
var children = _c.children || _c.values;
if(children) {
	$.each(children, function(index, item) {
		print(
			$t.tree_item(item, {
					is_first:index == 0,
					is_last:index == children.length - 1
			})
		);
	});
}
?>
</ul>

<!--[tree_item]-->
<?
if (!_c.metadata) {
    _c.metadata = {};
}
var id = _c.metadata.id || _c.data;
?>
<li class="fx_tree_item<?=_o.is_first ? ' fx_tree_item_first' : ''?> 
	<?=_o.is_last ? ' fx_tree_item_last' : ''?>
	<?=_c.metadata.checked == 0 ? ' fx_admin_unchecked' : ''?>" 
	data-id="<?=id?>" id="fx_tree_item_<?=id?>">
	<div class="fx_tree_label">
		<a class="fx_tree_title"><?=_c.data?></a>
		<span class="fx_tree_expander"><span></span></span>
	</div>
	<?=$t.tree_children(_c)?>
</li>