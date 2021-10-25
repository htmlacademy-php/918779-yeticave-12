<?php 

require_once("helpers.php");

/**
 * 'Форматирует сумму и добавляет к ней знак рубля'
 *
 * @param $input Сумма в виде числа
 *
 * @return @output Сумма в виде числа с добавленным к нему знаком рубля
 */

function is_format_price ($input) {
    $output = "";
    $input = ceil($input);

    if ($input >= 1000) {

        $input = number_format($input, 0, '',' ');

    }

    $output = $input . " " . "&#8381;";

    return $output;
}

/**
 * 'Форматирует время оставшееся до окончания лота'
 *
 * @param $input Дата в виде строки
 *
 * @return @output Время в виде массива
 */

function is_format_date ($input) {

    $hour = 3600;
    $minute = 60;

    $date01 = strtotime($input);
    $date02 = strtotime('now');

    $diff = $date01 - $date02;

    $hours = floor($diff / $hour);

    $expiration = $diff - ($hours * $hour);
    $minutes = ceil($expiration / $minute);

    if ($minutes > $minute || $minutes < $minute )  {

        $hours = str_pad($hours, 2, '0', STR_PAD_LEFT);
        $minutes = str_pad($minutes, 2, '0', STR_PAD_LEFT);

    }

    else {

        $hours = str_pad(1, 2, '0', STR_PAD_LEFT);
        $minutes = str_pad(0, 2, '0', STR_PAD_LEFT);

    }
    
    if ($date02 >= $date01) {

        $hours =  str_pad(0, 2, '0', STR_PAD_LEFT);
        $minutes =  str_pad(0, 2, '0', STR_PAD_LEFT);
        
    }

    $output = [

        'hours' => $hours,
        'minutes' => $minutes

    ];

    return $output['hours'] . ':' . $output['minutes'];
}

?>