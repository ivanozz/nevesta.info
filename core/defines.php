<?php
/**
 * Базовые константы
 */

// режим разработки
define('DEBUG_MODE', false);

// абсолютные пути
$pathUp2Lv = pathinfo(__FILE__);
$pathUp2Lv = $pathUp2Lv['dirname']."/../"; // абсолютный путь в системе
$pathUp2Lv = realpath($pathUp2Lv)."/";

define("ROOT", $pathUp2Lv);

define("CONTROLLERS", ROOT.'application/controllers/');
define("MODELS", ROOT.'application/models/');
define("VIEWS", ROOT.'application/views/');
define("UTILS", ROOT.'core/utils/');

// путь до папки с фотографиями
// на случай если нужно будет закачать файлы директорию на текущем сервере
define("PHOTOS", ROOT.'application/views/images/photos/');

// относительные пути
define("BASE", '/');
define("BASE_VIEWS", BASE.'application/views/');
define("CSS_DIR", BASE_VIEWS.'css/');
define("JS_DIR", BASE_VIEWS.'js/');
define("IMAGES_DIR", BASE_VIEWS.'images/');

// расположение CSV файла
define("FILE_CSV", ROOT.'data/test-photo.csv');

// параметры пагинации
define("PAGE_MANY", 2);
define("PAGE_START", 2);
define("PAGE_END", 2);

// количество изображений на странице
define("COUNT_ITEM_ON_PAGE", 20);