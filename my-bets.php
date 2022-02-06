<?php

require_once('init.php');
require_once('data.php');
require_once('helpers.php');
require_once('functions.php');

if ($is_auth) {
    $bets_list = is_get_bets($link, $_SESSION["id"]);
    $bets = [];
    foreach($bets_list as $bet) {
        $id = intval($bet["id"]);
        $contacts = str_split(is_get_user_data ($link, $id));
        $res = array_merge($bet, $contacts);
        $bets[] = $res;
    };
    unset($bet);
};

$main_content = include_template("main-my-bets.php", [
    "categories" => $categories,
    "bets" => $bets,
    "is_auth" => $is_auth
]);

$layout_content = include_template("layout.php", [
    "content" => $main_content,
    "categories" => $categories,
    "title" => "Мои ставки",
    "is_auth" => $is_auth,
    "user_name" => $user_name
]);

print($layout_content);

?>