<div fx:template="main">
    {$form  | .form}
</div>


<form fx:template="form" action="{$action}" method="{$method}" class="fx_form {$class}">
    {apply errors /}
    {$fields || .row /}
</form>

<div fx:template="errors" fx:with-each="$fields->find('has_error')" class="fx_form_errors">
    <p>We found some errors:</p>
    <div fx:item class="fx_form_error">
        <span>{$name}</span>: {$error /}
    </div>
    <p>Please correct them</p>
</div>
    
<div 
    fx:template="row" 
    class="
        fx_row fx_row_type_{$type} fx_row_name_{$name} 
        {if $has_error} fx_row_error{/if}
        {if $required} fx_row_required{/if}">
    {apply label /}
    {apply input_block /}
</div>

<label fx:template="label" class="fx_label" for="{$name}">
    {%label_$name}{$label /}{/%}
    <span fx:if="$required" class="required">*</span>
</label>

<div fx:template="input_block" class="fx_input_block">
    {apply input /}
</div>

{template id="input_atts"}
    class="fx_input fx_input_type_{$type}"
    id="{$name}"
    name="{$name}"
    {if $is_disabled}disabled="disabled"{/if}
    {if $value && in_array($type, array('text', 'number', 'password'))}
        value="{$value}"
    {/if}
{/template}

<input 
    fx:template="input[in_array($type, array('text', 'password'))]"
    type="text"
    {apply input_atts /} />

<input 
    fx:template="input[$type == 'checkbox']"
    {apply input_atts /}
    {if $value}checked="checked"{/if} />

<textarea
    fx:template="input[$type == 'textarea']"
    {apply input_atts /}>
    {$value}
</textarea>