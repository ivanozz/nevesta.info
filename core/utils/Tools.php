<?php
class Tools {

    /**
     * Получаем расширение файла по его имени
     * @param $filename
     * @return mixed
     */
    public function getExtFileFromName($filename) {
        $rs = explode('.',$filename);
        $rs = $rs[count($rs)-1];
        return $rs;
    }
	
	/**
	 * Получаем массив для постраничного вывода
	 * @param $pages
	 * @param @page
	 * @return array
	 */
	public function getPaginator($pages, $page) {
		
		$this->paginator_many = PAGE_MANY;
		$this->paginator_start = PAGE_START;
		$this->paginator_end = PAGE_END;

        if ($pages < 2)
            return array();
        if (!$page || $page < 1)
            $page = 1;
        if ($page > $pages)
            $page = $pages;

        $paginator = array();

        for ($i = $page - $this->paginator_many - $this->paginator_start; $i < $page; $i++) {
            if ($i > 0){
                if($page <= $this->paginator_many + $this->paginator_start + 1 || $i >= $page - $this->paginator_many){
                    $paginator['pages'][$i] = false;
                }
            }
        }
        $paginator['pages'][$page] = true;
        for ($i = $page + 1; $i < $page + $this->paginator_many + $this->paginator_end + 1; $i++) {
            if ($i <= $pages){
                if($page >= $pages - $this->paginator_many - $this->paginator_end || $i <= $page + $this->paginator_many){
					$paginator['pages'][$i] = false;
                }
            }
        }

        if ($page > $this->paginator_many + 1 + $this->paginator_start)
            for($i=1; $i<$this->paginator_start + 1; $i++){
                $paginator['firstpage'][] = $i;
            }
        if ($page < $pages - $this->paginator_end - $this->paginator_many)
            for($i = $pages - $this->paginator_end + 1; $i<=$pages; $i++){
                $paginator['lastpage'][] = $i;
            }

        if ($page < $pages)
            $paginator['nextpage'] = $page + 1;
        if ($page > 1)
            $paginator['prevpage'] = $page - 1;

        return $paginator;
    }
}
