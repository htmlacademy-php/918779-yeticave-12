<?php

require_once('init.php');
require_once('data.php');
require_once('helpers.php');
require_once('functions.php');

if ($is_auth) {
    $data = $_SESSION["id"];
    $bets_list;

    $sql = "SELECT bets.date_bet AS date_bet, bets.cost, lots.title as title, lots.description,
    lots.path, lots.expiration, lots.id, lots.winner_id, categories.title as category, users.message
    FROM bets
    JOIN lots ON bets.lot_id = lots.id
    JOIN users ON bets.user_id = users.id
    JOIN categories ON lots.category_id = categories.id
    WHERE bets.user_id = ?
    ORDER BY bets.date_bet DESC";

    $stmt = db_get_prepare_stmt($link, $sql, [$data]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    if ($result) {
        $bets_list = $result;
    } else {
        $error = mysqli_error($link);
    };

    $bets = [];

    foreach ($bets_list as $bet) {
        $id = intval($bet["id"]);

        $sql = "SELECT users.message FROM lots
        JOIN users ON users.id = lots.user_id
        WHERE lots.id = $id";

        $result = mysqli_query($link, $sql);

        if ($result) {
            $contacts = mysqli_fetch_all($result, MYSQLI_ASSOC);
        } else {
            $error = mysqli_error($link);
        }

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
