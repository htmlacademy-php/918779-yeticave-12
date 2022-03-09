
<?php

require_once('init.php');
require_once('data.php');
require_once('helpers.php');
require_once('functions.php');

//Запрос на показ лотов
$id_num = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);


$sql = "SELECT lots.id, lots.title, lots.description, lots.path, lots.price, lots.expiration, lots.step, categories.title as category
       FROM lots
       JOIN categories ON lots.category_id=categories.id
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

};

$lot = mysqli_fetch_array($res, MYSQLI_ASSOC);

$sql = "SELECT users.name, bets.cost, date_bet AS date_bet
        FROM bets
        JOIN lots ON bets.lot_id=lots.id
        JOIN users ON bets.user_id=users.id
        WHERE lots.id = $id_num
        ORDER BY bets.date_bet DESC LIMIT 10";
        $result = mysqli_query($link, $sql);

        if ($result) {
            $history = mysqli_fetch_all($result, MYSQLI_ASSOC);
        }

        else {
        $error = mysqli_error($link);
        }

$current_price = max($lot["price"], $history[0]["cost"]);
$min_bet = $current_price + $lot["step"];
$bet_counter = get_bet_count($link, $id_num);


$main_content = include_template('main-lot.php', [

    'categories' => $categories,
    'lot' => $lot,
    'is_auth' => $is_auth,
    "current_price" => $current_price,
    "min_bet" => $min_bet,
    "id_num" => $id_num,
    "history" => $history,
    "bet_counter" => "$bet_counter"

]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $error = [];

    $bet = filter_input(INPUT_POST, "cost", FILTER_VALIDATE_INT);

    if ($bet < $min_bet) {
        $error = "Ставка не может быть меньше $min_bet";
    }
    if (empty($bet)) {
        $error = "Ставка должна быть целым числом, болше нуля";
    }

    if ($error) {
        $main_content = include_template("main-lot.php", [
            "categories" => $categories,
            "lot" => $lot,
            "is_auth" => $is_auth,
            "current_price" => $current_price,
            "min_bet" => $min_bet,
            "error" => $error,
            "id_num" => $id_num,
            "history" => $history
        ]);
    } else {
        $res = add_bet_db($link, $bet, $_SESSION["id"], $id_num);
        $bet_counter = get_bet_count($link, $id_num);
        header("Location: /lot.php?id=" .$id_num);
    }
}

$layout_content = include_template('layout.php', [

    'is_auth' => $is_auth,
    'content' => $main_content,
    'categories' => $categories,
    'title' => $lot['title'],
    'user_name' => $user_name

]);

print($layout_content);

?>
