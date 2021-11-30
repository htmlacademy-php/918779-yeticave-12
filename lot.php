<?php

require_once('init.php');
require_once('data.php');
require_once('helpers.php');
require_once('functions.php');

//Запрос на показ лотов
$id_num = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);


$sql = "SELECT lots.id, lots.title, lots.description, lots.path, lots.price, lots.expiration, categories.title as category, MAX(bets.bet) as current_price
       FROM lots
       JOIN categories ON lots.category_id=categories.id
       JOIN bets ON bets.lot_id=lots.id
       WHERE lots.id = $id_num
       GROUP BY lots.id";

$res = mysqli_query($link, $sql);

if (!$res) {

    header('Location: /error.php',true, 500);
    exit;

};

if (!mysqli_num_rows($res)) {
    http_response_code(404);
    header('Location: /error.php',true, 404);
    exit;
}

$lot = mysqli_fetch_array($res, MYSQLI_ASSOC);

$main_content = include_template('main-lot.php', [
    'categories' => $categories,
    'lot' => $lot
]);

$layout_content = include_template('layout.php', [
    'is_auth' => $is_auth,
    'content' => $main_content,
    'categories' => $categories,
    'title' => $lot['title'],
    'user_name' => $user_name
]);

print($layout_content);

?>
