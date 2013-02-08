<div class="side_test">
    {php}
    foreach ($this->get_var('input') as $i => $q){
        ?>
        <div class="test_q">
        {var id="q$i"}q#<?=$i?>{/var}:<br />
        <b><?=$q?></b>
        </div>
        <?
    }
    {/php}
</div>