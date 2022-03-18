<?php

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

require_once('init.php');
require_once('data.php');
require_once('helpers.php');
require_once('functions.php');
require_once('vendor/autoload.php');

$sql = "SELECT * FROM lots WHERE winner_id IS NULL && expiration <= NOW()";
$result = mysqli_query($link, $sql);

if ($result) {
    $lots = mysqli_fetch_all($result, MYSQLI_ASSOC);
}

$bets_win = [];

foreach($lots as $lot) {
    $id = (int)$lot['id'];

    $sql = "SELECT * FROM bets WHERE lot_id = $id
        ORDER BY date_bet DESC LIMIT 1;";

    $result = mysqli_query($link, $sql);

    if ($result) {
            $bet = get_arrow($result);
    }

    $winner_id = $bet["user_id"];

    if (!empty($bet)) {
        $id_lot = $lot["id"];
        $bets_win[] = $bet;
        $sql = "UPDATE lots SET winner_id = $winner_id WHERE id = $id";
        $result = mysqli_query($link, $sql);
    }
}

if (!empty($bets_win)) {

    $win_users = [];

    foreach($bets_win as $bet) {
        $id = intval($bet["lot_id"]);

        $sql = "SELECT lots.id, lots.title, users.name, users.message FROM bets
        JOIN lots ON bets.lot_id=lots.id
        JOIN users ON bets.user_id=users.id
        WHERE lots.id = $id";

        $result = mysqli_query($link, $sql);
        if ($result) {
            $data = get_arrow($result);
        }
        $win_users[] = $data;
    }

    $recipients = [];

    foreach($bets_win as $bet) {
        $id = intval($bet["user_id"]);

        $sql = "SELECT users.name, users.email, users.message FROM users WHERE id = $id";
        $result = mysqli_query($link, $sql);

        if ($result) {
            $user_data = get_arrow($result);
        }

        $recipients[$user_data["email"]] = $user_data["name"];
    }

    // Конфигурация траспорта
    $dsn = 'smtp://4234:32434@smtp.mailtrap.io:2525?encryption=tls&auth_mode=login';
    $transport = Transport::fromDsn($dsn);
    // Формирование сообщения
    $message = new Email();
    $message->to($recipients);
    $message->from("mail@yeticave.shop");
    $message->subject("Ваша ставка победила");
    $message->text($msg_content);
    // Отправка сообщения
    $mailer = new Mailer($transport);
    $mailer->send($message);

}

?>
