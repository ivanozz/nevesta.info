<?php
/**
 * Класс для работы с БД
 */

class DB{

    static $_instance;

    private function __construct() {
	
        if(mysql_connect(SQL_HOST, SQL_LOGIN, SQL_PASSWD)) {
            mysql_select_db(SQL_DBASE);

            if(defined(INITIAL_QUERY)){
                $this->query(INITIAL_QUERY);
            }

            // в режиме отладки здесь сохраняются избранные SQL-запросы (см. функцию select)
            $_SESSION['sql'] = array();
        }
    }

    public static function getInstance(){

        if(!(self::$_instance instanceOf self))
            self::$_instance = new self();

        return self::$_instance;
    }

    /**
     * Получение id последней вставленной записи
     * @return int
     */
    public function getLastId() {
        $res = (int) $this->select('SELECT LAST_INSERT_ID() as `last_id`');
        return $res;
    }

    /**
     * Выполнение запроса к БД
     * @param $sql
     * @return array|string
     */
    public function query($sql){
        $res = array();

        mysql_query($sql) or $res = mysql_error();

        return $res;
    }

    /**
     * Выполнение запроса
     * @param $sql
     * @param bool $show_select - вывод текста запроса в массив запросов
     * @return array
     */
    public function select($sql, $show_select = false){
        if($show_select)
            $_SESSION['sql'][] = $sql;

		$res = mysql_query($sql);
		
		if(mysql_error()) die('Query: '.$sql. '. Error:' .mysql_error());
		
		$array_final = array();
		
        while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
			$array_final[] = $row;
		}
        return $array_final;
    }

    /**
     * Вставка записей в БД
     * @param $tbl
     * @param $data
     * @return array|string
     */
    public function insert($tbl,$data) {
        $tbl = TABLE_PREFIX.'_'.$tbl;
        $sql='INSERT INTO `'.$tbl.'`
            (`'.implode('`,`', array_keys($data)).'`)
            VALUES ("'.implode('","', array_values($data)).'")';
        $res = $this->query($sql);

        return $res;
    }
	
    /**
     * Обновление записей БД
     * @param $tbl
     * @param $data
     * @param $where
     * @return array|string
     */
    public function update($tbl,$data,$where) {
        $tbl = TABLE_PREFIX.'_'.$tbl;
        $set_data = '';

        $i = 1;
        $c = sizeof($data);

        $res = array();

        foreach($data as $k => $v) {

            $set_data .= ' `'.$k.'` = "'.$v.'"';

            if($i != $c) $set_data .= ', ';

            $i++;
        }

        if($set_data) {
            $sql = 'UPDATE `'.$tbl.'` SET '.$set_data.' WHERE '.$where;
            $res = $this->query($sql);
        }
        return $res;
    }

    /**
     * Удаление записей БД
     * @param $tbl
     * @param $where
     * @return array|string
     */
    public function delete($tbl,$where) {
        $tbl = TABLE_PREFIX.'_'.$tbl;
        if (!empty($where)) $where = ' WHERE '.$where;
        $res = $this->query('DELETE FROM `'.$tbl.'` '.$where);
        return $res;
    }

    /**
     * Очистка таблицы $tbl
     * @param $tbl
     */
    public function truncate($tbl) {
        $tbl = TABLE_PREFIX.'_'.$tbl;
        $this->query('TRUNCATE `'.$tbl.'`');
    }
}