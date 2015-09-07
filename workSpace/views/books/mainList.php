<?php
    if (isset(controllerManager::$variables['goodsBunchNotFound'])) {
        echo 'Ошибка! Данный субкаталог не найден';
        return;
    } elseif($pagination['isError']) {
        echo 'Вы перешли на несуществующую страницу';
        return;
    }
    echo $goodsBunch; ?>

<br/><br/><div id="listBook">

    <?php foreach ($typeProduct as $element) { ?>

        <div class="panel panel-default w300">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <a href="/book/<?php echo $element['b_id'];?>"><?php echo $element['b_title'];?></a>
                </h3>
            </div>
            <div class="panel-body">
                <div class="preview">
                    <a href="/book/<?php echo $element['b_id'];?>">
                        <img style="visibility: hidden;" src="http://static2.ozone.ru/multimedia/c200/1010617388.jpg">
                    </a>
                </div>
                <div class="inner">
                    <p><?php echo ($element['p_presence']?'на складе':'нет в наличие');?></p>
                    <p class="authors">Авторы:
                        <?php
                        foreach ($element['authors'] as $author) {
                            echo "<a href=\"/authors/$author[id]\">$author[initials]</a>, ";
                         }
                        ?>
                    </p>
                    <span class="price">
                        <?php /*echo $element['p_price'];*/?>id: <?php echo $element['p_id'];?>
                    </span> <!--руб-->
                    <span></span>
                </div>
            </div>
        </div>
<?php } ?>
</div>
<?php if($pagination['isPagination']) :?>
<div>
    <nav>
        <ul class="pagination">
            <li<?php echo $currentPage==1?' class="disabled"':'';?>>
                <a<?php echo $currentPage==1?'':' href="'.$quickExplore[2]['url'].'/page/'.((int)$currentPage-1).'"';?> aria-label="Previous">
                    <span aria-hidden="true">«</span></a></li>
            <?php
            $indicator = false;
            for ($a = 1; $a <= $pageCount; $a++) {
                if (!in_array($a, $pagination['interval']) && !$indicator) {
                    $indicator = true;
                    echo '<li class="disabled"><a>...</a></li>';

                } elseif(in_array($a, $pagination['interval'])) {
                    if ($indicator) {
                        $indicator = false;
                    }
                    echo '<li'.($currentPage==$a?' class="active"':'').'><a href="'.$quickExplore[2]['url'].'/page/'.$a.'">'.$a.'</a></li>';
                }
            }
            ?>
            <li<?php echo $currentPage==$pageCount?' class="disabled"':'';?>>
                <a<?php echo $currentPage<$pageCount?' href="'.$quickExplore[2]['url'].'/page/'.((int)$currentPage+1).'"':'';?> aria-label="Next">
                    <span aria-hidden="true">»</span></a></li>
        </ul>
    </nav>
</div>
<?php endif;?>