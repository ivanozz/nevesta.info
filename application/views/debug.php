<? if ( DEBUG_MODE && $_SESSION['sql']) {?>
<br />
<!-- Панель отладки -->
<? foreach($_SESSION['sql'] as $sql) { ?>
    <pre><? echo $sql; ?></pre>
    <? } ?>
<? } ?>
<!-- Конец панели отладки -->