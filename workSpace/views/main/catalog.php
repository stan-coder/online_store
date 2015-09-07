<?php
    if (isset($catalogNotFound)) {
        echo 'Ошибка! Каталог не найден. Попрбуйте перейти по другому адресу.';
        return;
    }
    foreach ($subCatalog as $key => $value) {
        echo "<a href=\"/sub_catalog/$key\">$value[title]</a><br/>";
        if (!empty($value['goods_bunch'])) {
            foreach ($value['goods_bunch'] as $key2 => $value2) {

                echo "______<a href=\"/goods_bunch/$key2\">$value2</a><br/>";
            }
        }
    }

?>