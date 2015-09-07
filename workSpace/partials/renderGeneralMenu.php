<?php
    $data = dbCommon::getInstance()->select('select id, title from online_store.general_catalog where enabled = true');
    $activeMenu = '';
    $selectedCatalogId = isset(controllerManager::$variables['catalogId']) ? (integer)controllerManager::$variables['catalogId'] : false;

    foreach ($data as $value) {
        echo "<li><a" . (is_numeric($selectedCatalogId) && $selectedCatalogId === $value['id'] ? ' class="active activeMenu"' : '') .
        " href=\"/catalog/$value[id]\">$value[title]</a></li>\n";
    }
?>