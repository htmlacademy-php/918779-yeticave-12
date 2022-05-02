<?php

require_once('init.php');
require_once('data.php');
require_once('helpers.php');
require_once('functions.php');
require_once("getwinner.php");

//Запрос на показ лотов
$sql = "SELECT lots.id, lots.title, lots.price, lots.path, lots.expiration, categories.title as category FROM lots
JOIN categories ON lots.category_id = categories.id
WHERE lots.expiration > NOW()
ORDER BY creation DESC";

$res = mysqli_query($link, $sql);

if (!$res) {
    http_response_code(404);
    header('Location: /error.php', true, 404);
    exit;
}

$lots = mysqli_fetch_all($res, MYSQLI_ASSOC);

$main_content = include_template('main.php', [
    'categories' => $categories,
    'lots' => $lots
]);

$layout_content = include_template('layout.php', [
    'content' => $main_content,
    'categories' => $categories,
    'title' => 'Главная',
    'is_auth' => $is_auth,
    'user_name' => $user_name
]);

print($layout_content);
