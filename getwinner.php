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
            ORDER BY date_bet DESC LIMIT 1";

    $result = mysqli_query($link, $sql);

    if ($result) {
        $bet = get_arrow($result);
    }

    if (!empty($bet)) {
        $winner_id = $bet["user_id"];
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
        $recipient_id = intval($bet["user_id"]);

        $sql = "SELECT lots.id, lots.title, users.name, users.message, users.email FROM bets
        JOIN lots ON bets.lot_id = lots.id
        JOIN users ON bets.user_id = users.id
        WHERE lots.id = $id && users.id = $recipient_id";

        $result = mysqli_query($link, $sql);
        if ($result) {
            $win_users = get_arrow($result);
        }

        $msg_content = include_template('email.php', ['win_users' => $win_users]);

        // Конфигурация траспорта
        $dsn = 'smtp://d753198b29c4db:59e88ca28b5e22@smtp.mailtrap.io:2525?encryption=tls&auth_mode=login';
        $transport = Transport::fromDsn($dsn);
        // Формирование сообщения
        $message = new Email();
        $message->to($win_users['email']);
        $message->from("keks@phpdemo.ru");
        $message->subject("Ваша ставка победила");
        $message->html($msg_content);
        // Отправка сообщения
        $mailer = new Mailer($transport);
        $mailer->send($message);
    }
}
?>
