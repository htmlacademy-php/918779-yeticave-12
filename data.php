<?php

$user_name = "";

$is_auth = !empty($_SESSION["user"]);

if ($is_auth) {
    $user_name = $_SESSION["user"];
};

date_default_timezone_set('Europe/Moscow');

$title = 'Главная';

if (!$link) {
    http_response_code(500);
    header('Location: /error.php', true, 500);
    exit;
};

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
    $content = header('Location: /error.php', true, 404);
    exit;
}
