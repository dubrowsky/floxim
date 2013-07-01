var $t = {
	add: function(name, tpl, test, priority) {
		
		if (typeof tpl._test == 'undefined' && typeof test == 'function') {
			tpl._test = test;
		}
		
		if (typeof tpl._priority != 'number' && typeof priority == 'number') {
			tpl._priority = priority;
		}
		
		// например, form.fields.text
		name = name.split('.');
		var name_prefix = name.slice(0, -1); // text
		var func_name = name.slice(-1); // form.fields
		var c = $t;
		// после цикла c попадает текущий $t.form.fields
		// если его нет, заполняется пустыми объектами
		for (var i = 0; i < name_prefix.length; i++) { 
			var chunk = name[i];
			if (typeof c[chunk] == 'undefined') {
				c[chunk] = {}
			}
			if (i < name.length - 1) {
				c = c[chunk];
			}
		}
		
		var c_type = typeof c[func_name];
		
		if (c_type == 'undefined') { // еще не задавали
			c[func_name] = tpl;
		} else {
			
			var old_func = c[func_name]; // то, что уже сидит в form.fields.text
			
			if ( c_type != 'function' ) { // объект-заглушка
				for (var i in old_func) { // переносим свойства в новую функцию
					tpl[i] = old_func[i];
					delete old_func[i];
				}
				// и устанавливаем новую ф-цию на место заглушки
				c[func_name] = tpl;
			} else if ( typeof old_func._variants != 'undefined') { // функция-с-вариантами, просто добавляем новую в качестве варианта
				old_func._variants.push( tpl );
				old_func._variants._is_sorted = false;
			} else { // функция-без-вариантов, ее надо заменить	
				var var_func = function(obj, options) {
					var res_func = $t.findVariant( arguments.callee._variants, obj, options );
					return res_func (obj, options);
				}
				for (var i in old_func) { // переносим свойства в новую функцию
					var_func[i] = old_func[i];
					delete old_func[i];
				}
				var_func._variants = [old_func, tpl];
				c[func_name] = var_func;
			}
		}
	},
	
	sortVariants: 	function(vars) {
		if (typeof vars._is_sorted != 'undefined' && vars._is_sorted) {
			return;
		}
		vars.sort( function(a, b) {
			if (typeof a._priority == 'undefined') {
				a._priority = (typeof a._test == 'undefined' ? 0 : 1);
			}
			if (typeof b._priority == 'undefined') {
				b._priority = (typeof b._test == 'undefined' ? 0 : 1);
			}
			return b._priority - a._priority;
		});
	},
	
	findVariant: function(vars, obj, options) {
		$t.sortVariants(vars);
		for (var i = 0; i < vars.length; i++) {
			if ( typeof vars[i]._test != 'function' || vars[i]._test(obj, options)) {
				return vars[i];
			}
		}
		return $t.noFunc;
	},
	
	noFunc: function(obj, options) {
		console.log('no tpl to render', obj);
		return '';
	},
	
	find: function(name) {
		var c = $t;
		name = name.split('.');
		for (var i = 0; i < name.length; i++) {
			var cp = name[i];
			if (typeof c[cp] == 'undefined') {
				console.log('not found in '+cp);
				return $t.noFunc;
			}
			c = c[cp];
		}
		return c;
	},
	jQuery: function(name, obj, options) {
		var tpl = $t.find(name);
        var res = tpl(obj,options).replace(/^\s+|\s+$/, '');
        var html = $(res);
		if (typeof tpl.jquery == 'function') {
			tpl.jquery(html, obj, options);
		}
		return html;
	},
	addSlashes: function(str) {
		return str.replace(/([\"\'])/g, "\\$1").replace(/\0/g, "\\0");
	},
	htmlEntities: function(s) {   // Convert all applicable characters to HTML entities
    	//
    	// +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    	
    	var div = document.createElement('div');
    	var text = document.createTextNode(s);
    	div.appendChild(text);
    	return div.innerHTML;
    },
    clear: function(s) {
    	var res = $t.htmlEntities(s);
    	res = res.replace(/\"/g, '&quot;');
    	return res;
    },
    inline_data: function(n) {
    	var c_data = n.data('inline_data');
    	if (c_data) {
    		return c_data;
    	}
    	var inp = n.find('>input.data_input');
    	
    	if (inp.length > 0) {
    		var json = inp.val();
    		inp.remove();
    	} else {
    		var json = n.attr('data-inline');
    		n.removeAttr('data-inline');
    	}
    	
    	if (json === undefined) {
    		return {};
    	}
		var data = $.evalJSON(json);
		n.data('inline_data', data);
		inp.remove();
		return data;
    },
    countLength:function(obj) {
    	if (obj instanceof Array) {
    		return obj.length;
    	}
    	var c = 0;
    	for (var i in obj) {
    		c++;
    	}
    	return c;
    }
}