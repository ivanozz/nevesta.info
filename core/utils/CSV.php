<?php
/**
 * Класс для работы с CSV-файлами
 */

class CSV {

    private $_csv_file = null;

    /**
     * @param $csv_file
     */
    public function __construct($csv_file) {
        if (file_exists($csv_file)) {
            $this->_csv_file = $csv_file;
        }
        else {
            throw new Exception("Файл \"$csv_file\" не доступен");
        }
    }

    /**
     * Читаем CSV в массив
     * @return array
     */
    public function getCSV() {
        $handle = fopen($this->_csv_file, "r");

        $array_line_full = array();

        while (($line = fgetcsv($handle, 0, ";")) !== FALSE) {
            $array_line_full[] = $line; // Формируем массив из строк CSV-файла
        }

        fclose($handle);
        return $array_line_full;
    }

}