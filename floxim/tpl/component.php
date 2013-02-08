<?php

abstract class fx_tpl_component extends fx_tpl {

    private $is_error = false;
    private $error_text = array();
    private $error_fields = array();
    private $is_canceled = false;
    private $cancel_message = '';

    /**
     * Выставить ошибку в условии добавления/изменения
     * @param type $text текст ошибки (или массив текстов)
     * @param type $fields имя поля (или массив имен)
     */
    protected function set_error($text, $fields = array()) {
        if (!is_array($text)) $text = array($text);
        if (!is_array($fields)) $fields = array($fields);

        $this->is_error = true;
        $this->error_text = array_merge($this->error_text, $text);
        $this->error_fields = array_merge($this->error_fields, $fields);
    }

    public function get_error() {
        if (!$this->is_error) return null;

        return array('text' => $this->error_text, 'fields' => $this->error_fields);
    }

    /**
     * Отменить вывод компонента
     * @param type $stop_message выводимое сообщение
     */
    public function cancel($stop_message) {
        $this->is_canceled = true;
        $this->cancel_message = $stop_message;
    }

    /**
     * Проверить, отменен ли вывод
     * @return bool
     */
    public function is_canceled() {
        return $this->is_canceled;
    }

    /**
     * Получить сообщение об отмене вывода
     * @return string
     */
    public function get_cancel_message() {
        return $this->cancel_message;
    }

    protected function _show_fields() {
        extract($this->get_vars());

        foreach ($fx_fields as $field) {
            if ($field->check_rights()) {
                echo $field->get_html();
            }
        }
    }

    public function begin_add_form() {
        extract($this->get_vars());
        ?>
        <div id="fx_form_<?= $fx_infoblock['id'] ?>" class="fx_form">
            <div class="fx_form_error" <?=($result['text'] ? '' : 'style="display:none;"')?>><?= ( $result['text'] ? join('<br/>', $result['text']) : '') ?></div>
            <form action="<?= fx::config()->HTTP_ACTION_LINK ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value ="add" />
                <input type="hidden" name="posting" value ="1" />
                <input type="hidden" name="fx_infoblock" value ="<?= $fx_infoblock['id'] ?>" />
                <input type="hidden" name="essence" value ="message" />
                <input type="hidden" name="fx_ajax" value ="1" />

        <?php
        if ($parent_id) {
            echo '<input type="hidden" name="parent_id" value ="'.$parent_id.'" />';
        }
    }

    public function end_add_form() {
        extract($this->get_vars());
        ?>
                <div class="fx_form_wrap fx_form_wrap_submit">
                    <input class="fx_form_field fx_form_field_submit" type="submit" value="Добавить"/>
                </div>
                <?= $this->get_js_for_ajax_check($fx_infoblock['id']) ?>
            </form>
        </div>

        <?php
    }

    public function get_js_for_ajax_check($id) {
        return '
         <script type="text/javascript">
            var fx_cont = $("#fx_form_'.intval($id).'");
            var fx_form = $("form", fx_cont );

            fx_form.submit(function() {
                $(".fx_form_error", fx_cont).html("").hide();
                $(this).ajaxSubmit({
                    success:  function (data) {
                        if ( data.status == "ok" ) {
                            fx_cont.html(data.aftertext);
                        }
                        else {
                            if ( data.fields ) {
                                $.each ( data.fields, function(k,v) {
                                    $("[name=\'"+v+"\']", fx_form).addClass("fx_form_field_error");
                                });
                            }
                            if ( data.text ) {
                                $(".fx_form_error", fx_cont).html( data.text.join("<br/>")).show();
                            }
                        }
                    },
                    dataType: "json",
                    error: function ( e ) {
                        if ( e.error.message) {
                           $(".fx_form_error", fx_cont).html(e.error.message).show();
                        }
                    }
                });

                return false;
            });
        </script>';
    }

    public function add_form() {
        extract($this->get_vars());

        echo $this->_show_fields();
    }

    public function begin_edit_form() {
        extract($this->get_vars());
        ?>
        <div id="fx_form_<?= $fx_infoblock['id'] ?>" class="fx_form">
            <div class="fx_form_error" style="display:none;"><?= ( $result['text'] ? join('<br/>', $result['text']) : '') ?></div>
        <form action="<?= fx::config()->HTTP_ACTION_LINK ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value ="edit" />
            <input type="hidden" name="posting" value ="1" />
            <input type="hidden" name="fx_infoblock" value ="<?= $fx_infoblock['id'] ?>" />
            <input type="hidden" name="essence" value ="message" />
            <input type="hidden" name="message_id" value ="<?= $fx_message_id ?>" />
            <input type="hidden" name="essence" value ="message" />
            <input type="hidden" name="fx_ajax" value ="1" />
        <?php
    }

    public function end_edit_form() {
        extract($this->get_vars());
        ?>
            <div class="fx_form_wrap fx_form_wrap_submit">
                <input class="fx_form_field fx_form_field_submit" type="submit" value="Изменить"/>
            </div>
            <?= $this->get_js_for_ajax_check($fx_infoblock['id']) ?>
        </form>
        </div>

        <?php
    }

    public function edit_form() {
        extract($this->get_vars());

        echo $this->_show_fields();
    }

    public function settings_index() {

    }

    public function settings_full() {

    }

    public function prefix() {

    }

    public function record() {

    }

    public function suffix() {

    }

    public function full() {

    }

    public function h1() {

    }

    public function title() {

    }

    public function after_add() {
        extract($this->get_vars());
        $sub = $fx_core->subdivision->get_by_infoblock($fx_message->get('infoblock_id'));
        ?>
        Ваше сообщение #<?= $fx_message['id'] ?> добавлено.<br>
        <a href="<?= $sub['hidden_url'] ?>">Вернуться в раздел</a>
        <br/><br/>
        <?
    }

    public function after_edit() {
        extract($this->get_vars());
        $sub = $fx_core->subdivision->get_by_infoblock($fx_message->get('infoblock_id'));
        ?>
        Ваше сообщение #<?= $fx_message['id'] ?> изменено.<br>
        <a href="<?= $sub['hidden_url'] ?>">Вернуться в раздел</a>
        <br/><br/>
        <?
    }

    public function add_cond() {

    }

    public function edit_cond() {

    }

}
?>