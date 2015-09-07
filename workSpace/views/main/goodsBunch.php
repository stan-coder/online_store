<?php
    if (isset(controllerManager::$variables['goodsBunchNotFound'])) {
        echo 'Ошибка! Данный субкаталог не найден';
        return;
    }
    echo $goodsBunch;
?>