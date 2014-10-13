<?php
/**
 * Контроллер по умолчанию
 */

class IndexController {

    public $indexModel;
    public $controller;
    public $mc;

    public function __construct(){
        $this->controller = MainController::getInstance();

        $page = isset($this->controller->_params['page']) ? $this->controller->_params['page'] : 0;

        $this->indexModel = new indexModel(
            $this->controller->db,
            $this->controller->filter->getInteger($page),
            $this->controller->tools,
            $this->controller->sorter
        );
    }

    public function indexAction(){

		$this->indexModel->name = isset($this->controller->_params['name']) ? $this->controller->_params['name'] : '%Username%';
		
		$this->indexModel->sort_type = isset($this->controller->sorter['field']) ? $this->controller->sorter['field'] : '';
		$this->indexModel->sort_direct = isset($this->controller->sorter['direct']) ? $this->controller->sorter['direct'] : '';

		$result = $this->indexModel->render(VIEWS.'index.php');
        $this->controller->setContent($result);
		
	}

    /**
     * Выставляем like для фото $_REQUEST['photo_id']
     */
    public function addLikeAction() {

        $count = $this->indexModel->addLikeForPhoto($_REQUEST['photo_id']);
        echo $count;

    }
	
	/**
	 * Добавляем или удаляем тег из фильтра
	 */
	public function pushTagAction(){
		
		$tag = $this->controller->_params['nametag'];
		$tmp = isset($_SESSION['tags']) ? $_SESSION['tags'] : array();

		if($tmp && in_array($tag, $tmp)) {
			unset($_SESSION['tags'][array_search($tag, $tmp)]);
		} 
		elseif($tag) {
            $_SESSION['tags'][] = $tag;
		}
        header('location: /');
        exit;
	}
	
	/**
	 * Добавляем или удаляем запретный тег из фильтра
	 */
	public function pushTagFailAction(){
		
		$tag = $this->controller->_params['nametag'];
		$tmp = isset($_SESSION['tags_fail']) ? $_SESSION['tags_fail'] : array() ;
		if($tmp && in_array($tag, $tmp)) {
			unset($_SESSION['tags_fail'][array_search($tag, $tmp)]);
		} 
		elseif($tag){
			    $_SESSION['tags_fail'][] = $tag;
		}
        header('location: /');
        exit;
	}

    /**
     * Убираем все фильтры
     */
    public function clearAction(){
        unset($_SESSION['tags'], $_SESSION['tags-fail'], $_SESSION['sorter']);
        header('location: /');
        exit;
    }

}