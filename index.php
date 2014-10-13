<?php
/**
 * Тестовое задание для Невеста.инфо
 * @author Ожегов Иван
 **/

session_start();

header('Pragma: public');

// хранение в локальном кэше браузера
header("Cache-control: private");
header('Content-type: text/html; charset=utf-8');

require_once('core/defines.php');
require_once('core/db.php');

if(DEBUG_MODE) {
    ini_set('display_errors', 1);
} else {
    ini_set('display_errors', 0);
}
error_reporting(E_ALL | E_STRICT);

set_include_path(get_include_path().
                    PATH_SEPARATOR.CONTROLLERS.
                    PATH_SEPARATOR.VIEWS.
                    PATH_SEPARATOR.MODELS.
                    PATH_SEPARATOR.UTILS);

function __autoload($classname){
    require_once $classname.'.php';
}

$main = MainController::getInstance();
$main->route();

echo $main->getContent();
