<?php

define('HOUR', '3600');
define('MINUTES_PER_HOUR', '60');
define('SECONDS_PER_MINUTE', '60');

$is_auth = rand(0, 1);

$user_name = 'Антон';

$title = 'Главная';

if(!$link) {

    http_response_code(500);
    header('Location: /error.php',true, 500);
    exit;
}

else {

    // Запрос на получение списка категорий
    $categories_list = "SELECT categories.id, categories.code, categories.title FROM categories ORDER BY id ASC";

    //Выполняем запрос и получаем результат
    $result = mysqli_query($link, $categories_list);

    // Запрос выполнен успешно
    if ($result) {

        // Получаем все категории в виде двухмерного массива
        $categories = mysqli_fetch_all($result, MYSQLI_ASSOC);

    } else {

        // Получить текст последней ошибки

        http_response_code(404);
        $content = header('Location: /error.php',true, 404);
        exit;

    }

}

?>
