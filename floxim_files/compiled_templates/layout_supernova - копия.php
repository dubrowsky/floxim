{template id="index"}
	<div class="index">
		<?
		$extras = array('1','2');
		?>
		<b style="background:{$bg}#CCC{/$bg};">{$title}I am title{/$title}</b><br />
		<b style="background:{$bg};">{$t2}I am <?=strtoupper('php title')?></b>
		<b>{$trec}I have something inside: {$title}{/$trec}</b>
		
		{call id="wrap"}
			{$data}<b><?=str_repeat('olo-',3)?>lo!</b>{/$data}
			{$extras}<?return $extras?>{/$extras}
		{/call}
	</div>
{/template}
<?

class fx_template_layout_supernova extends fx_template {
	public function tpl_wrap($data) {
		?><div class="index">
			<?
			$extras = array('1','2');
			?>
			<b style="background:<?=$data->show('bg', '#CCC', 'attribute')?>">
				<?$data->show('title', 'I am title');?>
			</b><br />
			<b style="background:<?=$data->show('bg', null, 'attribute')?>">
				<?=$data->show('t2', 'I am <?=strtoupper(\'php title\')?>')?>
			</b>
			<b>
				<?=$data->show('trec', 'I have something inside: <?=$data->show(\'title\')?>')?>
			</b>
			<?=$this->render(
				array(
					'data' => eval("ob_start();?><b><?=str_repeat('olo-',3)?></b><?return ob_get_clean();"),
					'extras' => $extras
				), 'wrap'
			);?>
		</div><?
	}
}

class fx_template_layout_supernova extends fx_template {
protected $_source_dir = "Z:/home/floxim/www/controllers/layout/supernova";
    public function tpl_index($data = array()) {
        $tpl = $this;
        ob_start();
		?><?=$tpl->render(
				array_merge(
					$data, 
					array(
						"data" =>  call_user_func( 
							function($data, $tpl) {
								ob_start();
									?><div class="index_data">
										<i>на морде нет сайдбара</i><?=$tpl->render_area("content", $data)?>
									</div><?
								return ob_get_clean();
							}, 
							$data, 
							$tpl
						)
					)
				), 
				"wrap"
			)?>
		<?
		return ob_get_clean();
    }
    public function tpl_wrap_simple($data = array()) {
        $tpl = $this;
ob_start();
?><div class="block"><?=$tpl->show_var("data", $data, "", "text")?></div><?
return ob_get_clean();
    }
    public function tpl_wrap_titled($data = array()) {
        $tpl = $this;
ob_start();
?><div class="block titled_block" style="border-color:<?=$tpl->show_var("color", $data,  call_user_func( function($data, $tpl) {ob_start();
?>#900<?
return ob_get_clean();}, $data, $tpl), "attribute")?>;">
                 <div class="title" style="background:<?=$tpl->show_var("color", $data, "", "attribute")?>;"><?=$tpl->show_var("title", $data,  call_user_func( function($data, $tpl) {ob_start();
?>Заголовок<?
return ob_get_clean();}, $data, $tpl), "text")?></div>
                 <div class="data"><?=$tpl->show_var("data", $data, "", "text")?></div>
            </div><?
return ob_get_clean();
    }
    public function tpl_inner($data = array()) {
        $tpl = $this;
ob_start();
?><?=$tpl->render(array_merge($data, array("data" =>  call_user_func( function($data, $tpl) {ob_start();
?><div class="sidebar">
        <i>Я - сайдбар!</i><?=$tpl->render_area("sidebar", $data)?></div>
    <div class="content content_with_side"><?=$tpl->render_area("content", $data)?></div><?
return ob_get_clean();}, $data, $tpl))), "wrap")?><?
return ob_get_clean();
    }
    public function tpl_supermenu($data = array()) {
        $tpl = $this;
ob_start();
?><div class="supermenu">
                        <span class="title"><?=$tpl->show_var("title", $data,  call_user_func( function($data, $tpl) {ob_start();
?>Менюшечка:<?
return ob_get_clean();}, $data, $tpl), "text")?>&nbsp;</span>
                        <?
                            foreach ($data['input'] as $i => $item) {
                                ?><span class="menu_item"><a href="<?=$item->get_url()?>"><?=$item['name']?></a></span><?
                                if ($i+1 != count($data['input']) ) {
                                    ?><span class="sep">&nbsp;&bull;&nbsp;</span><?
                                }
                            }
                        ?>
                    </div><?
return ob_get_clean();
    }
    public function tpl_wrap($data = array()) {
        $tpl = $this;
ob_start();
?><!DOCTYPE html>
<html>
    <head>
        <title>My Super Template</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head>
    <body>
        <div class="main_wrap">
            <div class="header">
                <a class="home" href="/">
                   <img src="<?=$tpl->show_var("logo", $data,  call_user_func( function($data, $tpl) {ob_start();
?>/floxim_templates/demo1/css/images/logo.gif<?
return ob_get_clean();}, $data, $tpl), "attribute")?>" />
                </a><?=$tpl->render_area("header", $data)?></div>
            <div class="data">
                here comes data:<?=$tpl->show_var("data", $data, "", "text")?></div>
            <div class="footer">
                
                <div class="footer_left"><?=$tpl->show_var("copy", $data,  call_user_func( function($data, $tpl) {ob_start();
?>&copy; 2010-<?=date('Y')?><?
return ob_get_clean();}, $data, $tpl), "text")?><br />
                    <div itemtype="fx_var" itemprop="company">My Company Name</div>
                </div>
                <div class="footer_right"><?=$tpl->render_area("footer", $data)?></div></div>
        </div>
    </body>
</html><?
return ob_get_clean();
    }
}
?>