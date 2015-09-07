<?php
if (isset(controllerManager::$variables['quickExplore'])) {

    $qe = controllerManager::$variables['quickExplore'];
    echo '<div class="quickExplore">';
    $qeCount = count($qe) - 1;
    foreach ($qe as $key => $element) {
        echo "<span><a href=\"$element[url]\">$element[title]</a></span>".
            ($key !== $qeCount ? " <span>&gt;&gt;</span>" : '');
    }
    echo '</div>';
}