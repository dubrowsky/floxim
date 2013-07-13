(function($){
    fx_fields = {
        tree_open_node: {},
        
        html: function (json) {
          return json.html;  
        },
        
        label: function(json) {
            return $t.jQuery('field_label', json);
        },
        
        input: function(json) {
            return $t.jQuery('form_row', json);
        },
    
        file: function (json) {
            return $t.jQuery('form_row', json);
            
        },
        
        image: function ( json ) {
            return $t.jQuery('form_row', json);
        },

        textarea: function(json) {
            json.field_type = 'textarea';
            return $t.jQuery('form_row', json);
        },

        select: function(json) {
            return $t.jQuery('form_row', json);
        },

        radio: function(json) {
            return $t.jQuery('form_row', json);
        },
        
        radio_facet: function (json ) {
            return $t.jQuery('form_row', json);
        },

        checkbox: function(json) {
            return $t.jQuery('form_row', json);
        },

        color: function(json) {
            return $t.jQuery('form_row', json);
        },

        iconselect: function(json) {
        	return $t.jQuery('form_row', json);
        },
        
        livesearch: function(json) {
        	var ls = $t.jQuery('form_row', json);
            return ls;
        },
        
        
        set: function(json) {
        	return $t.jQuery('form_row', json);
        },

        tree: function(json) {
        	
        	return $t.jQuery('form_row', json);
        },

        table: function (json) {
        	return $t.jQuery('form_row', json);
        },
        
        button: function (json) {
        	return $t.jQuery('form_row', json);
        },
        
        link: function(json) {
            return $t.jQuery('form_row', json);
        },
        
        list: function(json) {
        	
        	return $t.jQuery('form_row', json);
        	
        },
        
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
        	return $t.jQuery('form_row', json);
        	
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
        }

    }
})(jQuery);

window.fx_fields = window.$fx_fields = fx_fields;