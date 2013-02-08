<?
class fx_template_layout_supernova extends fx_template {
protected $_source_dir = "Z:/floxim/controllers/layout/supernova";
protected $_template_code = "layout_supernova";
    public function tpl_index() {
        ?><?
$tpl_to_call = fx::template("layout_supernova");
ob_start();
?>
        <div class="index_data">
            <i>на морде нет сайдбара</i>
            <?=$this->render_area("content")?>  
        </div>
    <?
$tpl_to_call->set_var(
                'content', 
                ob_get_clean());
$tpl_to_call->set_var_meta(
                'content', 
                array('var_type' => 'param'));
echo $tpl_to_call->render("wrap", $this->data);?><?
    }
    public function tpl_inner() {
        ?><?
$tpl_to_call = fx::template("layout_supernova");
ob_start();
?>
    <div class="sidebar">
        <?=$this->render_area("sidebar")?>
        
        
        
    </div>
    <div class="content content_with_side">
        <?=$this->render_area("content")?>
    </div>
<?
$tpl_to_call->set_var(
                'content', 
                ob_get_clean());
$tpl_to_call->set_var_meta(
                'content', 
                array('var_type' => 'param'));
echo $tpl_to_call->render("wrap", $this->data);?><?
    }
    public function tpl_wrap_simple() {
        ?>
            <div class="block">
                <?
if (isset($content)) {
echo $content;
}
?><?=$this->show_var("content")?>
            </div>
        <?
    }
    public function tpl_wrap_titled() {
        ?>
            <div class="block titled_block" style="border-color:<?
if (isset($color)) {
echo $color;
}
?><?ob_start();?><?$this->set_var_default(
                        "color", 
                        ob_get_clean());?><?=$this->show_var("color")?>;">
                 <div class="title" style="background:<?
if (isset($color)) {
echo $color;
}
?><?=$this->show_var("color")?>;">
                    <?
if (isset($title)) {
echo $title;
}
?><?ob_start();?><?$this->set_var_default(
                        "title", 
                        ob_get_clean());?><?=$this->show_var("title")?>
                 </div>
                 <div class="data"><?
if (isset($content)) {
echo $content;
}
?><?=$this->show_var("content")?></div>
            </div>
        <?
    }
    public function tpl_wrap() {
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
                   <img src="<?
if (isset($logo)) {
echo $logo;
}
?><?ob_start();?><?$this->set_var_default(
                        "logo", 
                        ob_get_clean());?><?=$this->show_var("logo")?>" />
                </a>
                <?=$this->render_area("header")?>
            </div>
            <div class="content">
                <?
if (isset($content)) {
echo $content;
}
?><?=$this->show_var("content")?>
            </div>
            <div class="footer">
                <div class="footer_left">
                    <?
if (isset($copy)) {
echo $copy;
}
?><?ob_start();?><?$this->set_var_default(
                        "copy", 
                        ob_get_clean());?><?=$this->show_var("copy")?><br />
                    <div itemtype="fx_var" itemprop="company">My Company Name</div>
                </div>
                <div class="footer_right">
                    <?=$this->render_area("footer")?>
                </div>
                
                
            </div>
        </div>
    </body>
</html><?
    }
    public function tpl_supermenu() {
        ?>
                    <div class="supermenu">
                        <span class="title"><?
if (isset($title)) {
echo $title;
}
?><?ob_start();?><?$this->set_var_default(
                        "title", 
                        ob_get_clean());?><?=$this->show_var("title")?>&nbsp;</span>
                        
                    </div>
                <?
    }
protected $_templates = array (
  0 => 
  array (
    'id' => 'index',
    'calls' => 
    array (
      0 => 'wrap',
    ),
    'name' => 'index',
    'for' => 'layout.supernova.index',
  ),
  1 => 
  array (
    'id' => 'inner',
    'calls' => 
    array (
      0 => 'wrap',
    ),
    'name' => 'inner',
    'for' => 'layout.supernova.inner',
  ),
  2 => 
  array (
    'id' => 'wrap_simple',
    'vars' => 
    array (
      0 => 'content',
    ),
    'name' => 'Простой блок',
    'for' => 'wrap',
  ),
  3 => 
  array (
    'id' => 'wrap_titled',
    'vars' => 
    array (
      0 => 'color',
      1 => 'color',
      2 => 'title',
      3 => 'content',
    ),
    'name' => 'Блок с заголовком',
    'for' => 'wrap',
  ),
  4 => 
  array (
    'id' => 'wrap',
    'vars' => 
    array (
      0 => 'logo',
      1 => 'content',
      2 => 'copy',
    ),
    'name' => 'wrap',
    'for' => 'layout.supernova.wrap',
  ),
  5 => 
  array (
    'id' => 'supermenu',
    'vars' => 
    array (
      0 => 'title',
    ),
    'name' => 'supermenu',
    'for' => 'component.section.listing',
  ),
);
}
?>