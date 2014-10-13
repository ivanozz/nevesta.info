<?php
class mainModel{

    /**
     * Получаем файл шаблона
     * @param $file
     * @return string
     */
    public function render($file) {
        ob_start();
        include($file);
        return ob_get_clean();
    }
}