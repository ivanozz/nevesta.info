<?php
/**
 * Класс импортирующий CSV в БД
 */

class ImportController {

    public $controller;

    public function __construct(){

        $this->controller = MainController::getInstance();
		
		$this->importModel = new importModel(
            $this->controller
        );

    }

	public function getContent(){
        $result = $this->importModel->render(VIEWS.'index.php');
        $this->controller->setContent($result);
	}

    public function indexAction(){

        // готовим базу к обновлению
        $this->importModel->db->truncate('photos');

        // считываем исходные данные
        $get_csv = $this->controller->csv->getCSV();

		set_time_limit(0);
		
        // заносим информацию о фотографиях
		for($i = 1, $c = sizeof($get_csv); $i < $c; $i++) {
            $this->importModel->insertOnePhoto($get_csv[$i]);
        }

        // удаляем информацию о старых настройках
        unset($_SESSION['tags'], $_SESSION['tags_fail'], $_SESSION['sorter']);

        // создаем теги
        $this->importModel->createAllTags();

        // создаем лайки
        $this->importModel->createAllLikes();

        // заполняем дополнительные индексы для быстрой пагинации
        $this->importModel->updateIndex('created_at');

        $this->importModel->updateIndex('count_like');

        $this->importModel->name = isset($this->controller->_params['name']) ? $this->controller->_params['name'] : '%Username%';

		$this->getContent();
    }

}
