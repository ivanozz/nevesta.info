<?php
class indexModel extends mainModel {

    public $db;
    public $images;
    public $tags;
    public $paginator;
    public $tools;
    public $sort_type;
    public $sort_direct;

    public $self = 'index';

    public function __construct($db = '', $page = '', $tools = '', $sort){
        $this->db = $db;
        $this->tools = $tools;

        $field = (isset($sort['field'])) ? $sort['field'] : 'created_at';
        $direct = (isset($sort['direct'])) ? $sort['direct'] : 'desc';

        // выбранные теги для фильтрации не могут быть исключенными
        // исключенные теги не могут быть выбраны
        $this->checkTags();

        $this->images = $this->_getListPhotoAction($page, $field, $direct);

        $this->tags = $this->getTags();
    }

    /**
     * Получаем список фотографий
     * @param $page
     * @param $field_sort
     * @param $direct_sort
     * @return mixed
     */

    protected function _getListPhotoAction($page, $field_sort, $direct_sort){

        $tbl = TABLE_PREFIX.'_photos';
        $tbl_tag = TABLE_PREFIX.'_tags';

        // получаем данные для пагинатора
        if(!$page)
            $page = 1;

        $start = $page * COUNT_ITEM_ON_PAGE - COUNT_ITEM_ON_PAGE;
        $stop = $page * COUNT_ITEM_ON_PAGE;

        //узнаем максимальное значение столбца
        $max_q = $this->db->select('SELECT MAX(`id`) as `max` FROM `'.$tbl.'`');
        $max = $max_q[0]['max'];

        $this->sort_type = $field_sort;
        $this->sort_direct = $direct_sort;

        $cond = ' ';

        // параметры для формирования фильтров по тегам
        $base1 = '';
        $base2 = '';
        $filter1 = '';
        $filter2 = '';

        // устанавливаем параметры сортировки
        $order = '`'.$this->sort_type.'` '.$this->sort_direct;

        // формируем фильтр по тегам
        if(isset($_SESSION['tags']) && sizeof($_SESSION['tags'])) {

            $base1 = '(SELECT COUNT(*)
								FROM `'.$tbl_tag.'`
								WHERE
									`name` IN ("'.implode('","', $_SESSION['tags']).'")
									AND `item_id` = l.`id` LIMIT 1)';
            $filter1 = ' WHERE '.$base1.'>0 ';

            $base1 = ', '.$base1;
            $cond = ' AND ';
        }

        // формируем фильтр по исключениям
        if(isset($_SESSION['tags_fail']) && sizeof($_SESSION['tags_fail'])) {

            if(!isset($_SESSION['tags']) || !sizeof($_SESSION['tags']))
                $cond = ' WHERE ';

            $base2 = '(SELECT COUNT(*)
							FROM `'.$tbl_tag.'`
							WHERE
								`name` IN ("'.implode('","', $_SESSION['tags_fail']).'")
								 AND `item_id` = l.`id` LIMIT 1)';

            $filter2 = $cond.$base2.' = 0 ';

            $base2 = ', '.$base2;
        }

        // если есть теги - используем LIMIT в запросе
        if( (isset($_SESSION['tags']) && sizeof($_SESSION['tags'])) ||
            (isset($_SESSION['tags_fail']) && sizeof($_SESSION['tags_fail'])) ) {

            $condition_default1 = ' ';
            $condition_default2 = ' LIMIT '.$start.', '.COUNT_ITEM_ON_PAGE.' ';

            $total = $this->db->select('
				SELECT COUNT(*) as `c`
					FROM `'.$tbl.'` as l
						LEFT JOIN `'.$tbl_tag.'` as m
						ON (l.`id` = m.`item_id`)
					 '.$filter1.$filter2.'
					GROUP BY l.`id` LIMIT 1',1
            );
            $total = (int) $total[0]['c'];

        } else {
            // иначе используем индексное поле

            $total = (int)(($max - 1) / COUNT_ITEM_ON_PAGE) + 1;

            // если сортировка идет убыванию - переворачиваем параметры фильтрации

            if($this->sort_direct == 'desc') {
                $start = $max - COUNT_ITEM_ON_PAGE * $page;
                $stop = $max - COUNT_ITEM_ON_PAGE * $page + COUNT_ITEM_ON_PAGE;
            } else {
                $start = $page * COUNT_ITEM_ON_PAGE - COUNT_ITEM_ON_PAGE;
                $stop = $page * COUNT_ITEM_ON_PAGE;
            }
            $order .= ', `id` '.$this->sort_direct;
            $condition_default1 = ' WHERE l.`'.$this->sort_type.'_index` > '.$start.'
                                      AND l.`'.$this->sort_type.'_index` <= '.$stop.' ';
            $condition_default2 = ' ';
        }

        $this->paginator = $this->tools->getPaginator($total, $page);

        // главный запрос на выборку списка фото
        $images = $this->db->select('
			    SELECT	l.`id`, l.`src`, l.`created_at`, l.`count_like`, l.`'.$this->sort_type.'_index`,
					GROUP_CONCAT(DISTINCT m.name ORDER BY m.name ASC SEPARATOR ", ") AS `tags`
				FROM `'.$tbl.'` as l
					LEFT JOIN `'.$tbl_tag.'` as m
					ON (l.`id` = m.`item_id`)
				'.$condition_default1.$filter1.$filter2.'
				GROUP BY l.`id`
				ORDER BY '.$order.' '.$condition_default2, 1
        );

        return $images;
    }

    /**
     * Проставляем лайк у фотографии от некоторого пользователя с id = 111
     * @param $id
     */
    public function addLikeForPhoto($id) {

        $id = (int) $id;
        $tbl = TABLE_PREFIX.'_photos';
        $tbl_l = TABLE_PREFIX.'_likes';

        $this->db->insert('likes', array('photo_id' => $id, 'user_id' => 111));
        $count = $this->db->select('SELECT count(*) as `c` FROM '.$tbl_l.' WHERE `photo_id` = '.$id);
        $count = $count['0']['c'];

        $this->db->update('photos', array('count_like' => $count), '`id` = '.$id);

        echo $count;
    }

    /**
     * Получаем массив тегов, отмечаем активные и исключенные теги
     * @return mixed
     */
    public function getTags(){
        $tbl = TABLE_PREFIX.'_tags';

        $tags = $this->db->select('SELECT DISTINCT `name` FROM '.$tbl.' ORDER BY `name` ASC');

        if($tags && sizeof($tags))
            for($i = 0, $c = sizeof($tags); $i < $c; $i++){

                if(isset($_SESSION['tags']) && in_array($tags[$i]['name'], $_SESSION['tags'])) {
                    $tags[$i]['active'] = 1;
                }

                if(isset($_SESSION['tags_fail']) && in_array($tags[$i]['name'], $_SESSION['tags_fail'])) {
                    $tags[$i]['fail'] = 1;
                }

            }

        return $tags;
    }

    /**
     * Проверка тегов фильтрации и исключенных тегов на пересечение
     */
    public function checkTags() {

        $tags = $tags_fail = array();

        if(isset($_SESSION['tags']))
            $tags = $_SESSION['tags'];

        if(isset($_SESSION['tags_fail']))
            $tags_fail = $_SESSION['tags_fail'];

        if(isset($tags_fail) && sizeof($tags_fail))
            for($i = 0, $c = sizeof($tags_fail); $i < $c; $i++) {

                // если среди исключенных тегов есть теги фильтрации
                // то исключенный тег снимается
                if(isset($tags_fail) && isset($tags_fail[$i]) && in_array($tags_fail[$i], $tags)) {
                    unset($_SESSION['tags_fail'][$i]);
                }

            }

    }
}