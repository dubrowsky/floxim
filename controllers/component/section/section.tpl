{template id="listing"}
    <div class="menu">
        <?
            $arr = array('nums' => '123');
            $test = 'woo';
        ?>
        [{$arr.nums}, {$test}]
        {php}
            foreach ($this->get_var('input') as $item) {
                extract($item->get_fields_to_show());
                dev_log('vars in tpl', get_defined_vars(), $item->get_page());
                ?>
                <div class="menu_item">
                    <a title="<?=$f_name?>" href="<?=$item->get_page()->get_field_to_show('url')?>"><?=$f_name?></a>
                </div>
                <?
            }
        {/php}
        {render select="$items"}
            <div class="menu_item">
                <a href="{$f_url}">{$f_name}</a>
                {%separator}&bull;{/%separator}
            </div>
        {/render}
    </div>
{/template}