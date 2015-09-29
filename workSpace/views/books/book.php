<?php

if (isset(controllerManager::$variables['bookNotFound'])) {
    echo 'Error! Such book not found';
    return;
}
?>

<table class="bookView">
    <tr>
        <td>
            <img src="http://static1.ozone.ru/multimedia/books_covers/c300/1005767303.jpg" alt="">
        </td>
        <td>
            <div class="tdRight">
                <h3>{title}</h3>
                <p>Авторы: <?php
                    foreach ($authors as $key => $author) {
                        echo "<a href=\"/author/$author[0]\">$author[1]</a>".($key==count($authors)-1?'':', ');
                    }
                    ?>
                </p>
                <p>Цена: {price}</p>
                <p>Наличие: {presenceText}</p>
                <p>Состояние: {condition}</p>
                <p>Оценки: <span class="clGreen">+{likes}</span>, <span class="clRed">-{dislikes}</p>
                <p>Описание: {description}</p>
                <br/>
                <?php if ($isPresence == true) : ?>
                    <button type="button" class="btn btn-lg btn-success">В корзину</button>
                <?php else : ?>
                    <button type="button" class="btn btn-lg btn-info">Уведомить о поступлении</button>
                <?php endif; ?>
            </div>
        </td>
    </tr>
</table>
