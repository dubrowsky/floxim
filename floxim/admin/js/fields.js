(function($){
    fx_fields = {
        tree_open_node: {},
        
        html: function (json) {
          return json.html;  
        },
        
        label: function(json) {
        	/*
				var _label = $('<label />');
				if ( json.status !== undefined ) {
					_label.addClass('ui-corner-all')
					_label.addClass('ui-state-' + json.status);
				}
				var label = json.label ? json.label : json.value;
				$(_label).append(label);
				return _label;
            */
            return $t.jQuery('field_label', json);
        },
        
        input: function(json) {
        	/*
				var _label = $('<label />');
				var type = json.password ? 'password' : (  json.type === 'hidden' ? 'hidden' : 'text' );   
				var _field = $('<input type="'+type+'" name="'+json.name+'" id="'+json.name+'" />');
				
				if (json.type != 'file'){
					$(_field).val(json.value);
				} 
				
				if (json.label) {
					var label_name = json.label;
					if (json.current) {
						label_name += '<span class="fx_admin_current"> (по умолчанию - '+json.current+')</span>'
					}
					$(_label).append(label_name, _field);
				} 
				return (json.type === 'hidden' || json.label === undefined ? _field : _label);
            */
            return $t.jQuery('form_row', json);
        },
    
        file: function (json) {
        	/*
				var _label = $('<label />');
				var _field = $('<input type="file" name="'+json.name+'" />');
				if (json.label) $(_label).append(json.label, _field);
				
				if ( json.old ) {
					var old = $('<div><span>Текущий файл: </span><a target="_blank" href="'+json.path+'">'+json.real_name+'</a>');
					var delete_link = $('<a/>').text('удалить');
					delete_link.click ( function(){
						_label.append('<input type="hidden" name="'+json.name+'[delete]" value="'+json.old+'" />');
						old.remove();
						return false;
					});
					old.append(delete_link);
					_label.append(old);
				}
	
				return _label;
            */
            return $t.jQuery('form_row', json);
            
        },
        
        image: function ( json ) {
        	/*
				var cont = $('<div/>');
				if (json.label) cont.append( $('<label/>').text(json.label));
				
				var preview = $('<img style="max-width:100px; float: left;"/>').appendTo(cont);
				var hidden_input = $('<input name="'+json.name+'[id]" type="hidden" />').appendTo(cont);
				var real_name = $('<span>').appendTo(cont);
				var edit_link = $('<a>').text('изменить').appendTo(cont);
				var current_file = {};
				
				if ( json.old ) {
					real_name.text(json.real_name);
					preview.attr('src', json.path);
					
					current_file.file_id = json.old;
					current_file.path = json.path;
					current_file.filename = json.real_name;
				}
				else {
					edit_link.text('закачать')
				}
	
				cont.append('<br/>').append(edit_link).append('<br style="clear:both;"/>');
	
				edit_link.click(function () {
					var dialog_file = new fx_dialog_file(json.field_id, current_file, function ( new_file){
						preview.attr('src', new_file.path + '?rnd=' + ( Math.random() * (51174) ) );
						hidden_input.val(new_file.file_id);
						$('span', real_name).text(new_file.filename);
					});
					dialog_file.open();
				});
				
				return cont;
            */
            return $t.jQuery('form_row', json);
        },

        textarea: function(json) {
        	/*
				var _label = $('<label />');
				var _field = $('<textarea id="'+json.name+'" name="'+json.name+'" />').val(json.value);
				if ( json.wysiwyg ) _field.addClass("fx_wysiwyg");
				if ( json.code) {
					_field.addClass("fx_code");
					_field.data('syntax', json.code);
				}
				if ( json.label !== undefined ) _label.append(json.label);
				_label.append(_field.css('height', '190px').css('margin-bottom', '40px'));
				return _label;
            */
            json.field_type = 'textarea';
            return $t.jQuery('form_row', json);
        },

        select: function(json) {
        	/*
				if ( json.hidden_on_one_value && !json.extendable && json.values && fx_object_length(json.values) == 1 ) {
					var hidden_data = {};
					hidden_data.name = json.name;
					hidden_data.type = 'hidden';
					hidden_data.value = fx_object_get_first_key(json.values);
					return $fx_fields.input(hidden_data);
				}
				var container = $('<div class="nc_admin_group"/>');
				var id = $fx_form.rid(json.id ? json.id : json.name);
				var _field = $('<select id="'+id+'" name="'+json.name+'"  />');
				var selected_option = false;
				var fx_new_input;
				
				if ( json.inline ) {
					_field.css('display', 'inline').css( "margin-left", "+=10" );
				}
			 
				if ( json.extendable ) {
					json.values['fx_new'] = json.extendable === true ? 'Add...' : json.extendable;
					_field.change(function(){
						var name = 'fx_new_'+$(this).attr('name')
						if ($(this).val() === 'fx_new') {
							fx_new_input = $('<input>', {
								type:'text', 
								name:name
							});
							_field.after(fx_new_input);
						} else {
							if ( fx_new_input ) fx_new_input.remove();
						}
					});
				}
				if (json.multiple) {
					$(_field).prop('multiple', 'multiple');
					$(_field).prop('name', json.name+'[]');
				}
	
				if ( json.values ) $.each( json.values, function(key, val) {
					var _o = $('<option />').attr('value', key).text(val);
					if (json.value && (( ( json.value instanceof Array ) && $.inArray(key, json.value) > -1) ||  json.value == key)) {
						$(_o).prop('selected', 'selected');
						selected_option = $(_o);
					}
					$(_field).append(_o);   
					container.append('<div id="nc_form_field_'+id+'_'+$fx_form.rid(key)+'" />');
				});
				
			
				container.prepend( _field );
				if ( json.label ) {
					container.prepend( $('<label />').html(json.label) );
				}
				
				if ( selected_option ) {
					$fx_form.df_change.push(selected_option);
				}
				$fx_form.df_change.push(_field);
				
				if ( json.value != undefined ){
					$fx_form.to_change.push( _field );
				} 
					
	
				return container;
            */
            return $t.jQuery('form_row', json);
        },

        radio: function(json) {
        	/*
				if ( json.hidden_on_one_value && json.values && (fx_object_length(json.values) == 1 || json.values.length == 1)  ) {
					var hidden_data = {};
					hidden_data.name = json.name;
					hidden_data.type = 'hidden';
					hidden_data.value = fx_object_get_first_key(json.values);
					return $fx_fields.input(hidden_data);
				}
				
				var _label = $('<div class="nc_admin_group" />');
				var sel_first = 0;
				if ( json.selected_first && json.value == undefined ) {
					sel_first = 1;
				}
				
				$.each(json.values, function(key, val) {
					if ( sel_first == 1 ) {
						json.value = key;
						sel_first = 0;
					}
					$(_label).append('<label class="fx_admin_radio_label"><input type="radio" value="'+key+'" id="'+json.name+'_'+key+'" name="'+json.name+'"'+(key == json.value ? ' checked="checked"' : '')+'>'+val+'</label>');
					var id = $fx_form.rid(json.id ? json.id : json.name ) + '_' +key;
					$(_label).append('<div id="nc_form_field_'+id+'" class="fx_admin_child_fields"></div>');
				});
				$(_label).prepend('<label>' + json.label + '</label>');
				
				if ( json.change ) {
					$('input', _label).change( function() {
						val = $(this).val();
						
						options = {};
						$('input, textarea, select', $(this).closest('form')).each(function(){
							var val = $(this).attr('type') === 'radio' ?  $('input[name="'+$(this).attr('name')+'"]:checked').val() : $(this).val();
							options[ $(this).attr('name') ] = val;
						});
						
						fx_call_user_func( '$fx.' + json.change, options );
					});
					if ( json.value != undefined ){
						$fx_form.to_change.push( $('input[name="'+json.name+'"]:checked'), _label );
					} 
					
				}
				
				$fx_form.df_change.push( $("input", _label) );
				if ( json.value  ){ 
				   
					$fx_form.to_change.push( $('input[name="'+json.name+'"]:checked',  _label) );
						
				} 
				//if (json.post === undefined) $fx_form.df_change.push(json.name); //bp
				return _label;
            */
            return $t.jQuery('form_row', json);
        },
        
        radio_facet: function (json ) {
        	/*
				var container = $('<div class="fx_admin_radio_facet" />');
				var element, input;
				
				input = $('<input type="hidden" name="'+json.name+'" />');
				container.append(input);
				
				$.each(json.values, function(key, name) {
					element = $('<span>').text(name); 
					element.click ( function () {
						$('.fx_admin_radio_facet_current').removeClass('fx_admin_radio_facet_current');
						$(this).addClass('fx_admin_radio_facet_current');
					
						input.val(key).change(); 
					
					});
					if ( json.value !== undefined && json.value == key ) {
						$fx_form.to_click.push(element);
					}
					container.append(element);
				});
				
				return container;
            */
            return $t.jQuery('form_row', json);
        },

        checkbox: function(json) {
        	/*
				var _label = $('<label />');
				var _field;
				var checked;
				if ( json.values !== undefined && json.values ) {
					_label = $('<div />');
					_label.append('<label>'+json.label+'</label>');
					$.each(json.values, function (key,val) {
						checked = false;
						for ( i in json.value ) {
							if ( json.value[i] == key ) {
								checked = true;
								break;
							}
						}
	
						_label.append('<label><input type="checkbox" id="'+json.name+'_'+key+'" name="'+json.name+'[]" value="'+key+'"  '+( checked ? 'checked="checked"' : '')+' />'+val+'</label>');
					});
				   
				}
				else {
					_label = $('<label />');
					_field = $('<input type="checkbox" id="'+json.name+'" name="'+json.name+'" value="1"  />');
					if (json.value && json.value !== '0' ) $(_field).prop("checked", true);
					$(_label).append(_field, json.label);
				}
				
				return _label;
            */
            return $t.jQuery('form_row', json);
        },

        color: function(json) {
        	/*
				var _color = json.value ? json.value :  '#00ff00';
				var _label = $('<label id="'+json.name+'" class="fx_color"/>');
				var _field = $('<input type="hidden" name="'+json.name+'" value="'+_color+'" />');
				var _colordiv = $('<div style="position:relative;height:36px;"><div class="colorSelector2"><div style="background-color: #00ff00"></div></div><div class="colorpickerHolder2"></div></div>');
				$('head').append('<link rel="stylesheet" media="screen" type="text/css" href="/floxim/lib/colorpicker/css/colorpicker.css" /><script type="text/javascript" src="/floxim/lib/colorpicker/js/colorpicker.js"></script>');
				$(_label).append(_field, json.label);
				$(_label).append(_colordiv);
				$('.colorpickerHolder2', _colordiv).ColorPicker({
					flat: true,
					color: _color,
					onSubmit: function(hsb, hex, rgb, el) {
						$('.colorSelector2 div', _colordiv).css('backgroundColor', '#' + hex);
						$('.colorpickerHolder2', _colordiv).stop().animate({
							height: widt ? 0 : 173
						}, 500);
						$(_field).attr('value', '#' + hex);
						widt = !widt;
					} 
				});
				$('.colorpickerHolder2>div', _colordiv).css('position', 'absolute');
				$('.colorSelector2 div', _colordiv).css('backgroundColor', _color);
				var widt = false;
				$('.colorSelector2', _colordiv).bind('click', function() {
					$('.colorpickerHolder2', _colordiv).stop().animate({
						height: widt ? 0 : 173
					}, 500);
					widt = !widt;
				});
				return _label;
            */
            return $t.jQuery('form_row', json);
        },

        iconselect: function(json) {
        	return $t.jQuery('form_row', json);
        	/*
				var is = $('<div class="fx_iconselect"/>');
				var filter = $('<div>').addClass('fx_iconselect_filter');
				var groups = [];
				var group_select = $('<select/>');
				var items = $('<div class="items"></div>');
				var num = {}, num_top = 0;
				//group_select.append('<option value="">Все</option>');
				group_select.change( function() {
					var groupval = $("option:selected",group_select).val();
					$('.item', is).each( function() {
						if ( !groupval || $(this).data('group') == groupval ) {
							$(this).show();
						}
						else {
							$(this).hide();
						}
					})
				});
				filter.append(group_select);
				
				filter.append('<div style="clear:both;"></div>');
				is.append(filter);
				
				
				is.append(items);
				var inp = $('<input type="hidden" name="'+json.name+'" />');
				is.append(inp);
	
				$.each(json.values, function(i, v) {
					// group
					if ( v.group !== undefined && $.inArray(v.group, groups) == -1 ) {
						var seleceted_group = ( json.group && json.group == v.group );
						groups.push(v.group);
						group_select.append('<option value="'+v.group+'" '+(seleceted_group ? "selected='selected'" : "")+'>'+v.group+'</option>');
						num[v.group] = 0;
					}
	
					var div = $('<div class="item"><img src="'+v.icon+'" /><div class="name">'+v.name+'</div></div>');
					var data = '';
					data += v.name.toLowerCase();
					//data += ' ';
					//data += v.group.toLowerCase();
					div.data('filter_string', data);
					div.data('group', v.group);
					div.click( function(){
						$('.selected', items).removeClass('selected');
						$(this).addClass('selected');
						inp.val(v.id !== undefined ? v.id : i).change();
					});
					
					num[v.group]++;
					
					if ( json.value && json.value == v.id ) {
						div.addClass('selected');
						inp.val(v.id !== undefined ? v.id : i);
						// scroll
						num_top = num[v.group]-1;
						$fx.panel.bind('fx.fielddrawn', function() {
							items.scrollTop( 50*num_top );  
						});
						  
					}
	 
					items.append(div);
				});
	
				return is;
			*/
        },
        
        itemselect: function(json) {
        	return $t.jQuery('form_row', json);
        	/*
				var multiple = json.multiple;
				var div = $('<div />');
	
				$.each(json.values, function(i, content) {
					var element = $('<div>').addClass('fx_itemselect_item').data('id',i);
					element.html(content);
					element.click(function(e){
						if ( multiple && (e.metaKey || e.ctrlKey) ) {
							$(this).toggleClass('selected');
						} else {
							$('.selected').removeClass('selected');
							$(this).addClass('selected');
						}
					  
						$('input', div).remove();
						$('.selected', div).each( function() {
							var id = $(this).data('id');
							var name = json.name +( multiple ? '[]' : '' );
							$(div).append('<input type="hidden" name="'+name+'" value="'+id+'" />');
						});
					});
	
					if ( json.value && json.value.contains(i)  ) {
						element.click();
					}
					div.append(element);
				});
				
				div.append("<br style='clear:both'/>");
				return div;
			*/
        },
        
        
        set: function(json) {
        	return $t.jQuery('form_row', json);
        	
				var container = $('<div />', {
					id:'fx_fieldset_'+json.name
				});
				
				if ( json.label ) {
					container.append('<label>'+json.label+'</label>');
				}
				var labels = $('<div />', {
					'class':'fx_fieldset_label'
				});
				$.each(json.labels, function(i, label){
					labels.append($('<label />').text(label));
				});
				container.append(labels);
				if (json.values) {
					$.each(json.values, function(i, value){
						var k = 0;
						var row = $('<div />', {
							'class':'fx_fieldset_row'
						});
						row.data({
							row:i
						});
						$.each(value, function(name, value){
							var post = {};
							$.extend(true, post, json.tpl[k]);
							$.extend(true, post, {
								name:json.name+'['+i+']['+name+']', 
								value:value
							});
							row.append($fx_form.draw_field(post));
							++k;
						});
						var remove = $('<span />', {
							'class':'fx_fieldset_remove'
						}).text('Удалить');
						remove.click(function(){
							$(this).parent().remove(); 
						});
						row.append(remove)
						container.append(row);
					});
				} else {
					labels.css({
						display:'none'
					});
				}
				
				if ( json.without_add === undefined ) {
					var add = $('<span />', {
						'class':'fx_fieldset_add'
					}).text('Добавить');
					add.click(function(){
						if ( $('.fx_fieldset_label', container).is(":hidden") ) $('.fx_fieldset_label', container).show();
						var row_num = $(this).prev().data('row');
						row_num = (row_num !== undefined ? row_num+1 : 1);
						var row = $('<div />', {
							'class':'fx_fieldset_row'
						});
						row.data({
							row:row_num
						});
						$.each(json.tpl, function(i, tpl){
							var post = {};
							$.extend(true, post, tpl);
							$.extend(true, post, {
								name:json.name+'['+row_num+']['+tpl.name+']', 
								value:''
							});
							row.append($fx_form.draw_field(post));                 
						});
						var remove = $('<span />', {
							'class':'fx_fieldset_remove'
						}).text('Удалить');
						remove.click(function(){
							$(this).parent().remove();
						});
						row.append(remove)
						$(this).before(row);
					});
					container.append(add);
				}
				container.append('<br style="clear:both;"/>');
				return container;
			
        },

        tree: function(json) {
        	
        	return $t.jQuery('form_row', json);
        	/*
            var container = $('<div />');
            var map = $('<div id="fx_tree_map"/>');
            var v = {
                data: []
            };
            v.data.push(json.values);
           
			map.jstree({
                json_data: v,
                plugins : [ 'themes', 'json_data', 'ui', 'search', 'dnd', 'crrm'],
                themes: {
                    url:'/floxim/admin/skins/default/jstree/style.css', 
                    theme: 'default',
                    dots: false,
                    icons: false
                },
                crrm : {
                    move: {
                        'check_move' : function () {
                            return true;  
                        }
                    }
                }
            });
            
            if (json.filter) {
                var filter = $('<div class="fx_list_filter"><label>Найти:<input type="text" /></label></div>');
                $('input', filter).keyup(function(){
                    map.jstree("search",$(this).val());   
                });
                container.append(filter);
            }
            
            map.bind("move_node.jstree", function (event, data){
                var options = {
                    fx_admin:1, 
                    essence:'subdivision', 
                    posting:1, 
                    action:'move'
                };
                // что
                options['darged'] = data.rslt.o.data('id');
                // куда
                options['target'] = data.rslt.r.data('id') || 0;
                // тип
                options['type'] = data.rslt.p;
 
                $.post($fx.settings.action_link, options);
                
            });
            
            map.bind("select_node.jstree", function(event, data) {
                $('.fx_admin_selected', map).removeClass('fx_admin_selected');   
                $('.jstree-clicked', map).parent().addClass('fx_admin_selected');
                $fx_form.update_available_buttons();
               
            });
            map.bind("dehover_node.jstree", function(event, data) {
                $('.jstree-hovered_item').removeClass('jstree-hovered_item');
                $('.fx_admin_tree_link').remove();
            });
            map.bind("hover_node.jstree", function(event, data) {
                $('.fx_admin_tree_link').remove();
                var url = data.rslt.obj.data('url');
                var link = $('<span class="fx_admin_tree_link" href="'+url+'">перейти</span>');
                link.click( function(){
                    window.open(url);
                });
                $('>a', data.rslt.obj).append(link);
            });
            map.bind("deselect_node.jstree", function(event, data) {
                $('.fx_admin_selected', map).removeClass('fx_admin_selected');
                $('.jstree-clicked', map).parent().addClass('fx_admin_selected');
                $fx_form.update_available_buttons();
            });
            
            map.bind("after_open.jstree", function(event, data) {
                var id = data.rslt.obj.attr('id');
                fx_fields.tree_open_node[id] = id;
            });
            map.bind("after_close.jstree", function(event, data) {
                var id = data.rslt.obj.attr('id');
                if ( fx_fields.tree_open_node[id] ) {
                    delete fx_fields.tree_open_node[id];
                }
            });
            
            map.bind("loaded.jstree", function(event, data) {
                 $.each(fx_fields.tree_open_node, function(k,id){
                   map.jstree('open_node', '#'+id, false, true);
                });
            });

            container.append(map);
            
            $fx_form.update_available_buttons();
                
         
			return container;
			*/
        },

        table: function (json) {
        	return $t.jQuery('form_row', json);
        	/*
				var container = $('<table>');
				var tr;
				if ( json.labels ) {
					tr = $('<tr/>');
					$.each ( json.labels, function (k,v){
						tr.append('<th>'+v+'</th>');
					
					});
					container.append(tr);
				}
				if ( json.values ) {
					$.each ( json.values, function (k,row){
						tr = $('<tr/>');
						$.each(row, function(key, value){
							tr.append('<td>'+value+'</td>');  
						});
						container.append(tr);
					});
					
				}
				
				return container;
			*/
        },
        
        button: function (json) {
        	return $t.jQuery('form_row', json);
			/*
				  var button = $('<button>'); 
				  if ( json.label ) {
					  button.text(json.label);
				  }
				  if ( json.postdata ) {
					  button.click(function(){
						 $fx.post(json.postdata);
						 return false;
					  });
				  }
				  if (  json.func ) {
					  fx_call_user_func(json.func);
				  }
				  if ( json.dialog ) {
					  button.click(function(){
						 $fx.post(json.dialog, function(res){
							 $fx_dialog.open_dialog(res);
						 });
						 return false;
					  });
				  }
				  
				  return button;
			*/
        },
        
        list: function(json) {
        	
        	return $t.jQuery('form_row', json);
        	
        	/*
            var row, container = $('<div/>').addClass('fx_list');
            
            if (json.filter && json.values && json.values.length > 10 ) {
                var filter = $fx_fields._list_get_filter();
                container.append(filter);
            }
            
            if (json.labels !== undefined && json.values ) {
                row = $fx_fields._list_get_labels_row(json.labels);
                container.append(row);
            }

            if ( json.values ) {
                $.each(json.values, function(i, value) {
                    row = $fx_fields._list_fetch_row(i, json.labels, value, json.tpl);
                    container.append(row);
                    if ( row.data('tip_text') ) {
                        row.tipTip({content: row.data('tip_text')});
                    }
                    
                });
            }
            
            if (json.sortable) {
                container.sortable({
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
            }
                        
            $fx_form.update_available_buttons();
           
            return container;
            */
        },
        
        /*
        _list_get_filter: function () {
            var filter = $('<div class="fx_list_filter"><label>Найти:<input type="text" /></label></div>');
            $('input', filter).keyup(function(){
                var t = $(this).val().toLowerCase();
                $('.fx_list_row').each(function(){
                    var row = $(this);
                    var str = '';
                    $('.fx_list_row_item', $(this)).each(function(){
                        str += $(this).html(); 
                    });
                        
                    if (str.toLowerCase().indexOf(t) !== -1) {
                        row.show();
                    } else {
                        row.hide();
                    }
                });
            });
            return filter;
        },
        
        _list_get_labels_row: function ( labels ) {
            var row = $('<div/>').addClass('fx_list_labels');
            $.each(labels, function(name, value){
                row.append($('<label/>').text(value));
            });
            return row;
        },
        
        _list_fetch_row: function ( row_num, labels, value, template ) { 
            var base_url = $fx.mode;
            var row = $('<div/>', {
                'class':(row_num%2 !== 0 ? 'fx_list_row' : 'fx_list_row odd')
            });
            if ( value.unchecked ) row.addClass('fx_admin_unchecked');
            row.data(value);
            if ( value.id !== undefined ) {
                row.click(function(e){
                    if (e.metaKey || e.ctrlKey) {
                        if ($(this).hasClass('fx_admin_selected')) {
                            $(this).removeClass('fx_admin_selected');
                        } else {
                            $(this).addClass('fx_admin_selected');
                        }
                    } else {
                        if ($(this).hasClass('fx_admin_selected') && $('.fx_list_row.fx_admin_selected').length === 1) {
                            $(this).removeClass('fx_admin_selected');
                        } else {
                            $('.fx_list_row').removeClass('fx_admin_selected');
                            $(this).addClass('fx_admin_selected');
                        }
                    }
                   
                    $fx_form.update_available_buttons();
                });
            }
                
            if ( template === 'imgh' ) {
                var el = $('<div/>').addClass('fx_list_row_item');
                el.append('<img style="width: 130px;" src="'+value.img+'" />');
                row.append(el); 
                    
                var el2 = $('<div/>').addClass('fx_list_row_item').width(600);
                if ( value.header instanceof Object ) {
                    value.header = '<a href="#'+base_url+'.'+value.header.url+'">'+value.header.name+'</a>';
                }
                el2.append( $('<h2/>').html(value.header) );
                el2.append(value.text);
                row.append(el2); 
                    
                row.height(120);
    
            }
            else {
                $.each(labels, function(label_key, val){
                    
                    var var_value = value[label_key];
                    var el = $('<div/>').addClass('fx_list_row_item');
                    if ( var_value instanceof Object ) {
                        if ( var_value.url !== undefined ) {
                            el.html('<a href="#'+base_url+'.'+var_value.url+'">'+var_value.name+'</a>');
                        }
                        if ( var_value.button !== undefined ) {
                            var button = $('<button/>').text(var_value.name);
                            var post = var_value.button;
                            button.click(function(){ 
                                $fx.post(post, function(){
                                    window.location = '/';
                                });
                                return false;
                            });
                            el.html(button);
                        }
                            
                    }
                    else {
                        el.html(var_value);
                    }

                    row.append(el);  
                });
            }
                
            return row;
        },
        */
        
        ajaxlink: function(json) {
            
            var _i = fx_form.jsonlink_post.push(json.post_data) - 1;
            var _field = $('<a href="#">'+json.text+'</a>');
            _field.click(function () {
                fx_form.send_jsonlink(json.control_names, _i);
                return false;
            });
            return _field;
        },
        
        store: function (json) {
            var store = new fx_store(json);
            return store.get_main_container();
        },
        
        datetime: function ( json ) {
            var cont = $('<div />').addClass('fx_admin_form_datetime');
            if ( json.label ) {
                cont.append( $('<label>').text(json.label) );
            }
            var input_day = $('<input name="'+json.name+'[day]" size="2" maxlength="2" />').val( json.day  !== undefined ? json.day : '');
            var input_month = $('<input name="'+json.name+'[month]" size="2" maxlength="2" />').val( json.month  !== undefined ? json.month : '');
            var input_year = $('<input name="'+json.name+'[year]" size="4" maxlength="4" />').val( json.year  !== undefined ? json.year : '');
            
            var input_hours = $('<input name="'+json.name+'[hours]" size="2" maxlength="2" />').val( json.hours  !== undefined ? json.hours : '');
            var input_minutes = $('<input name="'+json.name+'[minutes]" size="2" maxlength="2" />').val( json.minutes  !== undefined ? json.minutes : '');
            var input_seconds = $('<input name="'+json.name+'[seconds]" size="2" maxlength="2" />').val( json.seconds  !== undefined ? json.seconds : '');
            
            var datepicker = $('<input />').hide();
            cont.append(input_day, input_month,input_year, input_hours, input_minutes, input_seconds, datepicker );
            var datapicker_cfg = {
                changeMonth: true,
                changeYear: true,
                dateFormat: 'dd.mm.yy',
                dayNamesMin : ['Вс', 'Пн', 'Вт', 'Ср','Чт','Пт','Сб'],
                monthNamesShort:['январь', 'февраль', 'март','апрель','май','июнь','июль','август','сентябрь','октябрь','ноябрь','декабрь'],
                nextText: 'Следующий',
                prevText: 'Предыдущий',
                yearRange: '1950:c+20',
                firstDay: 1,
                showOn: "button",
				buttonImage: "/floxim/admin/skins/default/images/calendar.gif",
				buttonImageOnly: true,
                onSelect: function(dateText, inst) {
                    input_day.val(inst.selectedDay);
                    input_month.val(inst.selectedMonth);
                    input_year.val(inst.selectedYear);
                }
            };
        
            $fx.panel.bind('fx.fielddrawn', function() {
				datepicker.datepicker(datapicker_cfg);
                if ( json.day !== undefined && json.day ) {
                    datepicker.datepicker( "setDate" , json.day+'.'+json.month+'.'+json.year );
                }
             });

            return cont;
        },
        
        floatfield: function (json ) {
            var label = $('<label />'); 
            var field = $('<input  name="'+json.name+'"  />').val( json.value !== undefined ? json.value : '' );
            
            if (json.label) {
                $(label).append(json.label);
            } 
            label.append(field);
            
            field.keypress(function(e) {
                if (!(e.which==8 || e.which==44 ||e.which==45 ||e.which==46 ||(e.which>47 && e.which<58))) {
                    return false;
                }
            });

            return label;
        },
        
        colorbasic: function (json) {
        	
        	return $t.jQuery('form_row', json);
        	/*
            var colors = {};
            colors.red = {code: '#ff0000', 'name' : 'Red'};
            colors.orange = {code: '#FB940B', 'name' : 'Orange'};
            colors.yellow = {code: '#FFFF00', 'name' : 'Yellow'};
            colors.green = {code: '#00FF00', 'name' : 'Green'};
            colors.lightblue = {code: '#00FFFF', 'name' : 'Light-blue'};
            colors.blue = {code: '#0000FF', 'name' : 'Blue'};
            colors.purple = {code: '#FF00FF', 'name' : 'Purple'};
            colors.grey = {code: '#C0C0C0', 'name' : 'Grey'};
            colors.black = {code: '#000000', 'name' : 'Black'};
          
            var cont = $('<div>').addClass('fx_admin_colorbasic');
            var input = $('<input type="hidden" name="'+json.name+'" />').appendTo(cont);
            if ( json.label ) {
                cont.append( $('<label>').text(json.label) );
            }
            $.each(colors, function(key, v){
               var el = $('<div>').html($('<span>').css('background-color', v.code));
               cont.append(el);
               el.click(function(){
                  $('.fx_admin_colorbasic_selected', cont).removeClass('fx_admin_colorbasic_selected');
                  el.addClass('fx_admin_colorbasic_selected');
                  input.val(key);
               });
               
               if ( json.value && json.value == key ) {
                   el.addClass('fx_admin_colorbasic_selected');
                   input.val(key);
               }
            });
            
            return cont;
            */
        }

    }
})(jQuery);

window.fx_fields = window.$fx_fields = fx_fields;