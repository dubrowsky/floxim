<div class="text">  
    <?foreach ($this->get_var('input') as $item) {?>
        {call id="record"}{$record select="$item"}{/call}
        <hr />
        <?=$this->render('record', array('record' => $item));?>
    <?}?>
</div>

{template id="record"}
    Meta: <?=$this->get_var_meta('record')?>;<br />
    Record: {$record.text}
{/template}