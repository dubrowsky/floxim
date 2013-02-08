<?php
defined("FLOXIM") || die("Unable to load file.");

$fx_core = fx_core::get_object();
$shared_folder = fx::config()->INCLUDE_FOLDER.'phpimageeditor/lite/shared/';

include $shared_folder.'config.php';
include $shared_folder.'includes/functions.php';
include $shared_folder.'/classes/phpimageeditor.php';

global $objPHPImageEditor;
$objPHPImageEditor = new PHPImageEditor();

?>
<?php if (!$objPHPImageEditor->isAjaxPost) { ?>

            <script type="text/javascript" src="/floxim/lib/phpimageeditor/lite/shared/javascript/jquery-1.7.1.min.js"></script>
            <script type="text/javascript" src="/floxim/lib/phpimageeditor/lite/shared/javascript/jquery.jcrop.js"></script>
            <script type="text/javascript" src="/floxim/lib/phpimageeditor/lite/shared/javascript/jquery.numeric.js"></script>
            <script type="text/javascript" src="/floxim/lib/phpimageeditor/lite/shared/javascript/jquery-ui-1.8.16.custom.min.js"></script>

            <script type="text/javascript" src="/floxim/lib/phpimageeditor/lite/shared/javascript/phpimageeditor.js"></script>

            <link rel="stylesheet" type="text/css" href="/floxim/lib/phpimageeditor/lite/shared/css/style.css"/>
            <link rel="stylesheet" type="text/css" href="/floxim/lib/phpimageeditor/lite/shared/css/ui.resizable.css"/>
            <link rel="stylesheet" type="text/css" href="/floxim/lib/phpimageeditor/lite/shared/css/ui.slider.css"/>
            <link rel="stylesheet" type="text/css" href="/floxim/lib/phpimageeditor/lite/shared/css/jquery.jcrop.css?a=1"/>



            <script type="text/javascript">
                var ImageMaxWidth = <?php PIE_Echo(PIE_IMAGE_MAX_WIDTH); ?>;
                var ImageMaxHeight = <?php PIE_Echo(PIE_IMAGE_MAX_HEIGHT); ?>;
                var ImageWidth = <?php PIE_Echo($objPHPImageEditor->GetWidthFinal()); ?>;
                var ImageHeight = <?php PIE_Echo($objPHPImageEditor->GetHeightFinal()); ?>;
                var TextIsRequired = "<?php PIE_Echo($objPHPImageEditor->texts["IS REQUIRED"]); ?>";
                var TextMustBeNumeric = "<?php PIE_Echo($objPHPImageEditor->texts["MUST BE NUMERIC"]); ?>";
                var TextWidth = "<?php PIE_Echo($objPHPImageEditor->texts["WIDTH"]); ?>";
                var TextHeight = "<?php PIE_Echo($objPHPImageEditor->texts["HEIGHT"]); ?>";
                var TextNotNegative = "<?php PIE_Echo($objPHPImageEditor->texts["NOT NEGATIVE"]); ?>";
                var TextNotInRange = "<?php PIE_Echo($objPHPImageEditor->texts["NOT IN RANGE"]); ?>";
                var TextCantBeLargerThen = "<?php PIE_Echo($objPHPImageEditor->texts["CANT BE LARGER THEN"]); ?>";
                var TextAnUnexpectedError = "<?php PIE_Echo($objPHPImageEditor->texts["AN UNEXPECTED ERROR"]); ?>";
                var Brightness = <?php PIE_Echo($objPHPImageEditor->inputBrightness); ?>;
                var Contrast = <?php PIE_Echo($objPHPImageEditor->inputContrast); ?>;
                var BrightnessMax = <?php PIE_Echo($objPHPImageEditor->brightnessMax); ?>;
                var ContrastMax = <?php PIE_Echo($objPHPImageEditor->contrastMax); ?>;
                //var FormAction = "<?php PIE_Echo($objPHPImageEditor->GetFormAction()); ?>";
                var FormAction = "/floxim/?&essence=file&action=image_editor&imagesrc=<?=$objPHPImageEditor->srcEdit?>";
                var FormId = "<?php PIE_Echo($objPHPImageEditor->formName); ?>";
                var ActionUpdate = "<?php PIE_Echo($objPHPImageEditor->actionUpdate); ?>";
                var ActionUndo = "<?php PIE_Echo($objPHPImageEditor->actionUndo); ?>";
                var ActionSaveAndClose = "<?php PIE_Echo($objPHPImageEditor->actionSaveAndClose); ?>";
                var ActionRotateLeft = "<?php PIE_Echo($objPHPImageEditor->actionRotateLeft); ?>";
                var ActionRotateRight = "<?php PIE_Echo($objPHPImageEditor->actionRotateRight); ?>";
                var ActionSaveAndClose = "<?php PIE_Echo($objPHPImageEditor->actionSaveAndClose); ?>";
                var MenuResize = "<?php PIE_Echo(PIE_MENU_RESIZE); ?>";
                var MenuRotate = "<?php PIE_Echo(PIE_MENU_ROTATE); ?>";
                var MenuCrop = "<?php PIE_Echo(PIE_MENU_CROP); ?>";
                var MenuEffects = "<?php PIE_Echo(PIE_MENU_EFFECTS); ?>";
                var AjaxPostTimeoutMs = <?php PIE_Echo(PIE_AJAX_POST_TIMEOUT_MS); ?>;
            </script>



            <div id="phpImageEditor">
<?php } ?>

            <form id="<?php PIE_Echo($objPHPImageEditor->formName); ?>" name="<?php PIE_Echo($objPHPImageEditor->formName); ?>" method="post" action="/floxim/">
                <input type='hidden' name="essence" value="file" />
                <input type='hidden' name="action" value="image_editor" />
                <!-- <?=$objPHPImageEditor->srcEdit?> -->
<?php if (!$objPHPImageEditor->ErrorHasOccurred()) { ?>

                    <div id="nc_tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
                        <ul id="pie_menu" class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">

<?php
$tabs = array('RESIZE IMAGE', 'ROTATE IMAGE', 'CROP IMAGE', 'EFFECTS' );
$start_tab = 0;
foreach ( $tabs as $k => $tab ) {
    echo '
<li class="ui-state-default ui-corner-top '.( $k == $start_tab ? "ui-tabs-selected ui-state-active" : "").'">
    <div class="nc_tab">
        <div class="nc_tab_left"></div>
        <div class="nc_tab_main">
            <a id="menuitem_'.$k.'">'.$objPHPImageEditor->texts[$tab].'</a>
        </div>
        <div class="nc_tab_right"></div>

    </div>
</li>
';
}
?>

                        </ul>
<br style='clear:both;' />
                        <div>

                            <div id="panel_<?php PIE_Echo(PIE_MENU_RESIZE); ?>" class="panel">
                                <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                    <tr>
                                        <td>
                                            <div class="field widthAndHeight">
                                                <div class="pie_col1">
                                                    <label for="width"><?php PIE_Echo($objPHPImageEditor->texts["WIDTH"]); ?></label>
                                                    <input class="input-number" type="text" name="width" id="width" value="<?php PIE_Echo($objPHPImageEditor->GetWidthFinal()); ?>"/>
                                                    <input type="hidden" name="widthoriginal" id="widthoriginal" value="<?php PIE_Echo($objPHPImageEditor->GetWidth()); ?>"/>
                                                </div>
                                                <div class="pie_col2">
                                                    <label for="height"><?php PIE_Echo($objPHPImageEditor->texts["HEIGHT"]); ?></label>
                                                    <input class="input-number" type="text" name="height" id="height" value="<?php PIE_Echo($objPHPImageEditor->GetHeightFinal()); ?>"/>
                                                    <input type="hidden" name="heightoriginal" id="heightoriginal" value="<?php PIE_Echo($objPHPImageEditor->GetHeight()); ?>"/>
                                                </div>
                                            </div>
                                            <div>
                                                <input type="checkbox" name="<?php PIE_Echo($objPHPImageEditor->fieldNameKeepProportions); ?>" id="<?php PIE_Echo($objPHPImageEditor->fieldNameKeepProportions); ?>" <?php PIE_Echo($objPHPImageEditor->inputKeepProportions ? 'checked="checked"' : ''); ?>/>
                                                <input type="hidden" name="keepproportionsval" id="keepproportionsval" value="<?php PIE_Echo($objPHPImageEditor->inputKeepProportions ? '1' : '0'); ?>"/>
                                                <label for="<?php PIE_Echo($objPHPImageEditor->fieldNameKeepProportions); ?>" class="checkbox"><?php PIE_Echo($objPHPImageEditor->texts["KEEP PROPORTIONS"]); ?></label>
                                            </div>
                                        </td>

                                    </tr>
                                </table>
                            </div>

                            <div id="panel_<?php PIE_Echo(PIE_MENU_ROTATE); ?>" class="panel">
                                <div>
                                    <input id="btnRotateLeft" type="button" value="<?php PIE_Echo($objPHPImageEditor->texts["LEFT 90 DEGREES"]); ?>"/>
                                    <input id="btnRotateRight" type="button" value="<?php PIE_Echo($objPHPImageEditor->texts["RIGHT 90 DEGREES"]); ?>"/>
                                    <input type="button" class="pie_button_undo" <?php PIE_Echo($objPHPImageEditor->actions == "" ? 'disabled="disabled"' : ''); ?> name="btnundo" value="<?php PIE_Echo($objPHPImageEditor->texts["UNDO"]); ?>"/>
                                    <input type="hidden" name="rotate" id="rotate" value="-1"/>
                                </div>
                            </div>

                            <div id="panel_<?php PIE_Echo(PIE_MENU_CROP); ?>" class="panel">
                                <div>
                                    <input class="input-number" type="hidden" name="croptop" id="croptop" value="0"/>
                                    <input class="input-number" type="hidden" name="cropleft" id="cropleft" value="0"/>
                                    <input class="input-number" type="hidden" name="cropright" id="cropright" value="0"/>
                                    <input class="input-number" type="hidden" name="cropbottom" id="cropbottom" value="0"/>
                                </div>
                                <div>
                                    <div>
    <?php PIE_Echo($objPHPImageEditor->texts["CROP WIDTH"]); ?>: <span id="cropwidth">0</span>
                                        <?php PIE_Echo($objPHPImageEditor->texts["CROP HEIGHT"]); ?>: <span id="cropheight">0</span>
                                    </div>
                                    <input id="cropkeepproportions" class="checkbox" type="checkbox" name="cropkeepproportions" <?php PIE_Echo($objPHPImageEditor->inputCropKeepProportions ? 'checked="checked"' : ''); ?>/>
                                    <label class="checkbox" for="cropkeepproportions"><?php PIE_Echo($objPHPImageEditor->texts["CROP KEEP PROPORTIONS"]); ?></label>
                                    <input id="cropkeepproportionsval" type="hidden" name="cropkeepproportionsval" value="<?php PIE_Echo($objPHPImageEditor->inputCropKeepProportions ? '1' : '0'); ?>"/>
                                    <input id="cropkeepproportionsratio" type="hidden" name="cropkeepproportionsratio" value="<?php PIE_Echo($objPHPImageEditor->inputCropKeepProportionsRatio); ?>"/>
                                </div>
                                <div>
                                    <input type="button" class="pie_button_update" id="btnupdate" name="btnupdate" value="<?php PIE_Echo($objPHPImageEditor->texts["CROP"]); ?>"/>
                                    <input type="button" class="pie_button_undo" <?php PIE_Echo($objPHPImageEditor->actions == "" ? 'disabled="disabled"' : ''); ?> id="btnundo" name="btnundo" value="<?php PIE_Echo($objPHPImageEditor->texts["UNDO"]); ?>"/>
                                </div>
                            </div>

                            <div id="panel_<?php PIE_Echo(PIE_MENU_EFFECTS); ?>" class="panel">
                                <div>
                                    <label for="brightness"><?php PIE_Echo($objPHPImageEditor->texts["BRIGHTNESS"]); ?></label>
                                    <div id="brightness_slider_track"></div>
                                </div>
                                <br style="clear:both" />
                                <input type="hidden" name="brightness" id="brightness" value="<?php PIE_Echo($objPHPImageEditor->inputBrightness); ?>"/>
                                <div>
                                    <label for="contrast"><?php PIE_Echo($objPHPImageEditor->texts["CONTRAST"]); ?></label>
                                    <div id="contrast_slider_track"></div>
                                </div>
                                <br style="clear:both" />
                                <input type="hidden" name="contrast" id="contrast" value="<?php PIE_Echo($objPHPImageEditor->inputContrast); ?>"/>
                                <div>
                                    <input class="checkbox" type="checkbox" name="<?php PIE_Echo($objPHPImageEditor->actionGrayscale); ?>" id="<?php PIE_Echo($objPHPImageEditor->actionGrayscale); ?>" <?php PIE_Echo($objPHPImageEditor->inputGrayscale ? 'checked="checked"' : ''); ?>/>
                                    <label for="<?php PIE_Echo($objPHPImageEditor->actionGrayscale); ?>" class="checkbox"><?php PIE_Echo($objPHPImageEditor->texts["GRAYSCALE"]); ?></label>
                                    <input type="hidden" name="grayscaleval" id="grayscaleval" value="<?php PIE_Echo($objPHPImageEditor->inputGrayscale ? '1' : '0'); ?>"/>
                                </div>
                                <br style="clear:both" />
                            </div>

                            <div id="loading" style="display: none;"><?php PIE_Echo($objPHPImageEditor->texts["LOADING"]); ?>...<div id="loading_bar" style="width: 0px;"></div></div>

                        </div>

                        <div>
                            <input type="button" style="display:none;" id="btnsave" name="btnsave" value="<?php PIE_Echo($objPHPImageEditor->texts["SAVE AND CLOSE"]); ?>"/>
                        </div>

                    </div>
                    <input type="hidden" name="actiontype" id="actiontype" value="<?php PIE_Echo($objPHPImageEditor->actionUpdate); ?>"/>
                    <input type="hidden" name="panel" id="panel" value="<?php PIE_Echo($objPHPImageEditor->inputPanel); ?>"/>
                    <input type="hidden" name="language" id="language" value="<?php PIE_Echo($objPHPImageEditor->inputLanguage); ?>"/>
                    <textarea style="display:none;" name="actions" id="actions"><?php $objPHPImageEditor->GetActions(); ?></textarea>
                    <input type="hidden" name="widthlast" id="widthlast" value="<?php PIE_Echo($objPHPImageEditor->GetWidthFinal()); ?>"/>
                    <input type="hidden" name="heightlast" id="heightlast" value="<?php PIE_Echo($objPHPImageEditor->GetHeightFinal()); ?>"/>
                    <input type="hidden" name="widthlastbeforeresize" id="widthlastbeforeresize" value="<?php PIE_Echo($objPHPImageEditor->GetWidthKeepProportions()); ?>"/>
                    <input type="hidden" name="heightlastbeforeresize" id="heightlastbeforeresize" value="<?php PIE_Echo($objPHPImageEditor->GetHeightKeepProportions()); ?>"/>
                    <input type="hidden" name="userid" id="userid" value="<?php PIE_Echo($objPHPImageEditor->userId); ?>"/>
                    <input type="hidden" name="contrastlast" id="contrastlast" value="<?php PIE_Echo($objPHPImageEditor->inputContrast); ?>"/>
                    <input type="hidden" name="brightnesslast" id="brightnesslast" value="<?php PIE_Echo($objPHPImageEditor->inputBrightness); ?>"/>
                    <input type="hidden" name="isajaxpost" id="isajaxpost" value="false"/>
<?php } ?>
            </form>
                <?php $objPHPImageEditor->GetErrorMessages(); ?>
            <div id="divJsErrors" class="error" style="display: none;">
                <ul id="ulJsErrors" style="display: none;"><li></li></ul>
            </div>
            <div><img src="/floxim/lib/phpimageeditor/lite/shared/images/empty.gif" alt=""/></div>
<?php if (!$objPHPImageEditor->ErrorHasOccurred()) { ?>
                <div id="editimage">
                    <img id="image" style="position: absolute; left: 0px; top: 0px; width: <?php PIE_Echo($objPHPImageEditor->GetWidthFinal()); ?>px; height: <?php PIE_Echo($objPHPImageEditor->GetHeightFinal()); ?>px;" alt="" src="<?php PIE_Echo($objPHPImageEditor->srcWorkWith); ?>?timestamp=<?php PIE_Echo(time()); ?>"/>
                    <div id="imageResizerKeepProportions" style="diplay: <?php PIE_Echo(($objPHPImageEditor->inputKeepProportions && $objPHPImageEditor->inputPanel == PIE_MENU_RESIZE) ? 'block' : 'none'); ?>; width: <?php PIE_Echo($objPHPImageEditor->GetWidthFinal()); ?>px; height: <?php PIE_Echo($objPHPImageEditor->GetHeightFinal()); ?>px;"></div>
                    <div id="imageResizerNoProportions" style="diplay: <?php PIE_Echo((!$objPHPImageEditor->inputKeepProportions && $objPHPImageEditor->inputPanel == PIE_MENU_RESIZE) ? 'block' : 'none'); ?>; width: <?php PIE_Echo($objPHPImageEditor->GetWidthFinal()); ?>px; height: <?php PIE_Echo($objPHPImageEditor->GetHeightFinal()); ?>px;"></div>
                </div>
<?php } ?>

            <?php if (!$objPHPImageEditor->isAjaxPost) { ?>

<?php } ?>

<?php $objPHPImageEditor->CleanUp(); ?>