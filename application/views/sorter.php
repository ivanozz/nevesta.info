<!-- Панель сортировки -->
<div class="btn-toolbar">
    <div class="btn-group">
        <a class="btn<? if($this->sort_type == 'created_at' || !$this->sort_type) { ?> active<? } ?>" href="/index/index/field/created_at/direct/<? if($this->sort_direct == 'asc') { ?>desc<? } else { ?>asc<? } ?>">
            По дате создания
            <? if($this->sort_type == 'created_at' || !$this->sort_type) {
            if($this->sort_direct == 'asc') { ?>
                &uarr;
                <? } else { ?>
                &darr;
                <? } } ?></a>
        <a class="btn<? if($this->sort_type == 'count_like') { ?> active<? } ?>" href="/index/index/field/count_like/direct/<? if($this->sort_direct == 'asc') { ?>desc<? } else { ?>asc<? } ?>">
            По популярности
            <? if($this->sort_type == 'count_like') {
            if($this->sort_direct == 'asc') { ?>
                &uarr;
                <? } else { ?>
                &darr;
                <? } } ?></a>
    </div>
</div>
<!-- Конец панели сортировки -->