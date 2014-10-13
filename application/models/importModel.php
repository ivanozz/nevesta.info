<?php
class importModel extends mainModel{

    public $db;
    public $controller;
    public $view;

    public $images;
    public $tags;
    public $paginator;
    public $tools;
    public $sort_type;
    public $sort_direct;

    public $count_insert_photo = 0;
    public $count_error_photo = 0;
    public $count_add_tag = 0;
    public $count_fail_tag = 0;
    public $count_add_like = 0;
    public $count_fail_like = 0;

    public $self = 'import';

    // атрибуты на основе которых создаются дополнительные поля для быстрой сортировки
    private $_allow_attrs = array('count_like', 'created_at');


    public function __construct($controller){
        $this->db = $controller->db;
        $this->controller = $controller;
    }

    /**
     * Вставляем запись о фотографии
     * @param $data
     */
    public function insertOnePhoto($data){

        $temp = array(
            'user_id' => (int) $data[0],
            'src' => $data[1],
            'created_at' => $data[2]
        );

        $this->controller->filter->filterForSQL($temp);
        $res_item = $this->db->insert('photos', $temp);

        if(!$res_item) {
            $this->count_insert_photo++;
        }
    }

    /**
     * Обновляем индекс для сортировки фотографий
     * по атрибуту $attr (count_like|created_at)
     * @param attr
     */
    public function updateIndex($attr){

        if(!in_array($attr, $this->_allow_attrs)) {
            die('Ошибка: Недопустимое значение '.$attr.' атрибута сортировки. Доступны '.implode(',', $this->_allow_attrs));
        }

        $tbl_t = TABLE_PREFIX.'_temporary';
        $tbl_f = TABLE_PREFIX.'_photos';

        # Установка переменных
        $this->db->query('SET @n_like := 0;');

        # Удаление временной таблицы
        $this->db->query('DROP TABLE IF EXISTS `'.$tbl_t.'`;');

        # Создание таблицы, содержащей отображение нового порядкового номера на основе $attr.
        $this->db->query('CREATE TABLE `'.$tbl_t.'`
            SELECT `'.$attr.'_index`,`id` FROM
            (SELECT @n_like:=@n_like+1 as `id`, `id` as `'.$attr.'_index` FROM `'.$tbl_f.'`
                ORDER BY `'.$attr.'` ASC) AS `tbl1`
                NATURAL JOIN (SELECT `id` FROM `'.$tbl_f.'` ORDER BY `id` ASC) as `tbl2`;');

        # Обновление исходной таблицы с использованием данных из временной
        $this->db->query('UPDATE `'.$tbl_f.'` as l
            INNER JOIN `'.$tbl_t.'` as m ON (l.id = m.`'.$attr.'_index`)
            SET l.`'.$attr.'_index`= m.`id`
            WHERE l.`id` = m.`'.$attr.'_index`;');

        # Удаление временной таблицы
        $this->db->query('DROP TABLE IF EXISTS `'.$tbl_t.'`;');
    }


    /**
     * Генерируем теги для фотографий
     */
    public function createAllTags(){
        $this->db->truncate('tags');

        $tbl = TABLE_PREFIX.'_photos';
        $list = $this->db->select('SELECT `id` FROM `'.$tbl.'`');

        for($i = 0, $c = count($list); $i < $c; $i++) {
            $this->generateTagsPhoto($list[$i]['id']);
        }
        //$this->count_add_tag = (int) $this->count_add_tag;
        //$this->iew->count_fail_tag = (int) $this->count_fail_tag;
    }


    /**
     * Создаем случайное количество тегов для фото с id
     * @param $id
     */
    public function generateTagsPhoto($id) {

        // генерируем от 1 до 9 тегов
        $count_tags = rand(1, 9);

        for($i = 0; $i < $count_tags; $i++) {
            $data = array(
                'name' => 'tag'.rand(1, 99),
                'item_id' => $id
            );
            $res = $this->db->insert('tags', $data);

            if(!$res) $this->count_add_tag++;
            else $this->count_fail_tag++;
        }

    }

    /**
     * Генерируем случайное количество лайков для фотографий
     */
    public function createAllLikes() {

        $this->db->truncate('likes');

        $tbl = TABLE_PREFIX.'_photos';

        $list = $this->db->select('SELECT `id` FROM `'.$tbl.'`');

        // получаем список id юзеров
        $this->user_list = $this->db->select('SELECT DISTINCT `user_id` as `user` FROM `'.$tbl.'`');
        $this->size_user_list = sizeof($this->user_list);

        for($i = 0, $c = count($list); $i < $c; $i++) {
            $new_count_like_for_photo = $this->generateLikePhoto($list[$i]['id']);
            $this->db->update('photos', array('count_like' => $new_count_like_for_photo), '`id` = '.$list[$i]['id']);
        }

        //$this->view->count_add_like = (int) $this->count_add_like;
        //$this->view->count_fail_like = (int) $this->count_fail_like;

    }

    /**
     * Заполняем таблицу лайков для фото с $id $data['user_id'] - случайный пользователь у которого есть своя фотография
     * @param $id
     * @return int - число успешно добавленных лайков
     */
    public function generateLikePhoto($id) {

        // генерируем от 0 до 10 лайков
        $count_likes = rand(0, 10);

        $success_like = 0;
        if($count_likes > 0)
            for($i = 0; $i < $count_likes; $i++) {
                $rand_int = rand(0, $this->size_user_list);
                $one_user = isset($this->user_list[$rand_int]) ? $this->user_list[$rand_int] : 0;
                $data = array(
                    'photo_id' => $id,
                    'user_id' => $one_user['user']
                );
                $res = $this->db->insert('likes', $data);

                if(!$res) {
                    $this->count_add_like++;
                    $success_like++;
                }
                else $this->count_fail_like++;
            }

        return $success_like;
    }

}