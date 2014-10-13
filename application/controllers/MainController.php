<?php
/**
 * Базовый контроллер приложения
 */

class MainController{

    private $_controller;
    private $_action;
    private $_content;
    
	public $filter;
    public $db;
    public $_params;
    public $tools;
    public $csv;
	public $sorter = array();
	
    static $_instance;

    private function __construct(){

        $this->tools = new Tools();

        $this->filter = new Filter();

        $this->db = DB::getInstance();

        $this->csv = new CSV(FILE_CSV);

        $name_action = '';
        $name_controller = '';


        // получаем составляющие запроса
        $uri = $_SERVER['REQUEST_URI'];
        $uri_splits = explode('/', $uri);

        // фильтруем входные данные
        if(sizeof($uri_splits) > 1 && $uri_splits['1'])
            $name_controller = $this->filter->filterStringUri($uri_splits['1']);
        if(sizeof($uri_splits) > 2 && $uri_splits['2'])
            $name_action = $this->filter->filterStringUri($uri_splits['2']);

        // устанавливаем значения контроллера и экшена
        $this->_controller = $name_controller ? ucfirst($name_controller.'Controller') : 'IndexController';
        $this->_action = $name_action ? $name_action.'Action' : 'indexAction';

		
        // получаем массив параметров
        if(sizeof($uri_splits) > 3 && !empty($uri_splits['3'])) {

            $keys = array();
            $values = array();

            for($i = 3, $c = sizeof($uri_splits); $i < $c; $i++) {

                $current = $uri_splits[$i];

                if($i % 2 == 0)
                    $values[] = $current;
                else
                    $keys[] = $current;
            }
            if(sizeof($keys) == sizeof($values)) {
                $this->_params = array_combine($keys, $values);
            }
			if(isset($this->_params['field'])
                && ($this->_params['field'] == 'count_like' || $this->_params['field'] == 'created_at')) {
				$this->sorter['field'] = $this->_params['field'];
			}
			
			if(isset($this->_params['direct']) && ($this->_params['direct'] == 'asc'
                || $this->_params['direct'] == 'desc')) {
				$this->sorter['direct'] = $this->_params['direct'];	
			}
			// сохраняем параметры сортировки
			if($this->sorter) {
				$_SESSION['sorter'] = $this->sorter;
            }
		}
		
		// вспоминаем сортировку пользователя на других страницах
		if(isset($_SESSION['sorter']) && !empty($_SESSION['sorter']) && !$this->sorter) {
            $this->sorter = $_SESSION['sorter'];
		}

	}

    public static function getInstance(){

        if(!(self::$_instance instanceOf self))
            self::$_instance = new self();

        return self::$_instance;
		
    }

    /**
     * Метод-маршрутизатор приложения
     * /<controller>/<action>/key1/value1/key2/value2
     */
    public function route(){

        $class_name = $this->getController();

        $class = new $class_name();
        if(class_exists($class_name)) {
            $action_name = $this->getAction();
            if(is_callable($class_name, $action_name)) {
                $controller = $class;
                $action = $action_name;
                $controller->$action();
            } else {
                throw new Exception('Неизвестный метод класса '.$class_name) ;
            }
        } else {
            throw new Exception('Неизвестный контроллер');
        }
    }

    private function getParams(){
        return $this->_params;
    }

    private function getAction(){
        return $this->_action;
    }

    private function getController(){
        return $this->_controller;
    }

    public function getContent(){
        return $this->_content;
    }

    public function setContent($content){
        $this->_content = $content;
    }
}