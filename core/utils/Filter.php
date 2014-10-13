<?php
/**
 * Класс для фильтрации входных данных
 */

class Filter {

    /**
     * Фильтрация параметров URL
     * @param $data
     * @return string
     */
    public function filterStringUri($data) {

        $data = htmlspecialchars(strip_tags(trim($data)));

        return $data;
    }


    /**
     * Фильтрация массива для вставки в БД
     * @param $data
     * @return mixed
     */
    public function filterForSQL($data) {

        foreach($data as $k => $v) {
            $data[$k] = mysql_real_escape_string($this->filterStringUri($v));
        }

        return $data;
    }

    /**
     * Получение целочисленного значения
     * @param $val
     * @return int
     */
    public function getInteger($val) {

		$val = preg_replace( '/[^\d]/', '', trim($val) );

        return (int) $val;
	}

}