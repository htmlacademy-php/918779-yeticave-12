<?php

require_once('init.php');
require_once('data.php');
require_once('helpers.php');
require_once('functions.php');

$main_content = include_template('main-error.php', [
    'categories' => $categories
]);

$layout_content = include_template('layout.php', [
    'is_auth' => $is_auth,
    'content' => $main_content,
    'categories' => $categories,
    'title' => 'Главная',
    'user_name' => $user_name
]);

print($layout_content);

?>
