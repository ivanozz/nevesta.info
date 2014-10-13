<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="author" content="Ожегов Иван" />
	<title>Тестовое задание для Heвеста.инфо</title>
    <link href="<?php echo CSS_DIR;?>reset.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo CSS_DIR;?>bootstrap.min.css"  rel="stylesheet" type="text/css" />
    <link href="<?php echo CSS_DIR;?>main.css"  rel="stylesheet" type="text/css" />
</head>
<body>
    <div class="container well well-large main"
        <div class="tabbable">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab1" data-toggle="tab">Невеста.Фото</a></li>
            </ul>
            <div class="tab-content cont">
                <div class="tab-pane active" id="tab1">
                    <a href="/index/clear" class="pull-right" title="Сбросить все фильтры"><i class="icon-home"></i></a>
                    <?php if(isset($this->name)) { ?><h1>Hello, <?php echo $this->name; ?><? } ?></h1>

                    <?php // выводим статистику по результатам импорта
                          if($this->self == 'import') {
                    ?>

                        <?php if(isset($this->count_insert) && $this->count_insert > 0)
                        echo '<span class="label label-success">Добавлено фото: '.$this->count_insert.'</span>&nbsp;'; ?>
                        <?php if(isset($this->count_error) && $this->count_error > 0)
                        echo '<span class="label label-warning">Ошибочных фото: '.$this->count_error.'</span>&nbsp;'; ?>

                        <?php if(isset($this->count_add_tag) && $this->count_add_tag > 0)
                        echo '<span class="label label-success">Добавлено тегов: '.$this->count_add_tag.'</span>&nbsp;'; ?>
                        <?php if(isset($this->count_fail_tag) && $this->count_fail_tag > 0)
                        echo '<span class="label label-warning">Ошибочных тегов: '.$this->count_fail_tag.'</span>&nbsp;'; ?>

                        <?php if(isset($this->count_add_like) && $this->count_add_like > 0)
                        echo '<span class="label label-success">Добавлено лайков: '.$this->count_add_like.'</span>&nbsp;'; ?>
                        <?php if(isset($this->count_fail_like) && $this->count_fail_like > 0)
                        echo '<span class="label label-warning">Ошибочных лайков: '.$this->count_fail_like.'</span>&nbsp;'; ?>

                    <?php } else { // выводим основной шаблон ?>

					<?php include VIEWS.'paginator.php'?>

                    <?php include VIEWS.'sorter.php'?>

					<ul class="thumbnails">
					  <?php for($i = 0; $i < COUNT_ITEM_ON_PAGE; $i++) {?>
					  <li class="span3">
						<a href="#" class="thumbnail">

                        <img class="img-rounded" src="<?php if( isset($this->images[$i]['src']) && !empty($this->images[$i]['src']) ) echo $this->images[$i]['src']; ?>" alt="" width="200px" height="200px" />

                        </a>
                        <div class="well-large">
                          <?php if(isset($this->images[$i]['created_at'])) { ?>
                                <span class="created_at label"><?php echo $this->images[$i]['created_at']; ?></span>
                          <? } ?>
                          <?php if(isset($this->images[$i]['count_like'])) { ?>
                              <span class="count_like badge badge-important" title="like"
                                    data-id="<?php if(isset($this->images[$i]['id'])) echo $this->images[$i]['id']; ?>">
                                  <?php echo $this->images[$i]['count_like']; ?></span>
                          <? } ?>
                            <br />
                            <br />
						<? if (isset($this->images[$i]['tags']) && sizeof($this->images[$i]['tags'] > 0)) { ?>
                          <div class="tags label label-info"><?php echo $this->images[$i]['tags']; ?></div>
						<? } ?>
						</div>
					  </li>
					  <?php } ?>
					</ul>

                    <!-- Панель фильтрации -->
                    <h3>Фильтрация по тегам (логическое ИЛИ)</h3>
                    <div class="filter-tag">
                        <?php
                        for($i = 0, $c = sizeof($this->tags); $i < $c; $i++) {
                            if($this->tags[$i]['name']) {?>
                                <span class="label <? if (isset($this->tags[$i]['active'])) { ?>label-success<? } else {?>label-inverse<? } ?>" data-tag="/index/pushTag/nametag/<? echo $this->tags[$i]['name'];?>">#<? echo $this->tags[$i]['name']?></span>
                                <?php
                            }
                        } ?>
                    </div>
                    <h3>Теги-исключения</h3>
                    <div class="filter-tag-fail">
                        <?php for($i = 0, $c = sizeof($this->tags); $i < $c; $i++) {
                        if($this->tags[$i]['name']) {?>
                            <span class="label <? if (isset($this->tags[$i]['fail'])) { ?>label-important<? } else {?>label-inverse<? } ?>" data-tag="/index/pushTagFail/nametag/<? echo $this->tags[$i]['name'];?>">#<? echo $this->tags[$i]['name']?></span>
                            <?php }
                    } ?>
                    </div>
                    <!-- конец панели фильтрации -->

                    <? } ?>
				</div>
            </div>
            <?php include VIEWS.'debug.php'?>
        </div>

    </div>
    <script src="<?php echo JS_DIR;?>jquery.min.js" type="text/javascript"></script>
    <script src="<?php echo JS_DIR;?>bootstrap.min.js" type="text/javascript"></script>
    <script src="<?php echo JS_DIR;?>main.js" type="text/javascript"></script>
</body>
</html>