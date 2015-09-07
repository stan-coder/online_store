<?php
    if (isset(controllerManager::$variables['subCatalogNotFound'])) {
        echo 'Ошибка! Данный субкаталог не найден';
        return;
    }
    echo $subCatalog;
    echo '<br/>';
    foreach ($goodsBunch as $element) {
        echo "<a href=\"/goods_bunch/$element[id]\">$element[title]</a><br/>";
    }

?>