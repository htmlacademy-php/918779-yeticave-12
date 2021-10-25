<?php 

require_once("data.php");
require_once("helpers.php");

/**
 * 'Форматирует сумму и добавляет к ней знак рубля'
 *
 * @param $input Сумма в виде числа
 *
 * @return @output Сумма в виде числа с добавленным к нему знаком рубля
 */

function format_price ($input) {
    $output = "";
    $input = ceil($input);

    if ($input >= 1000) {

        $input = number_format($input, 0, '',' ');

    }

    $output = $input . " " . "&#8381;";

    return $output;
}

/**
 * 'Определяет время оставшееся до окончания лота'
 *
 * @param $input Дата в виде строки
 *
 * @return @output Время в виде массива
 */

function get_time_left ($input) {

    $date01 = strtotime($input);
    $date02 = strtotime('now');

    $diff = $date01 - $date02;

    $hours = floor($diff / HOUR);

    $expiration = $diff - ($hours * HOUR);
    $minutes = ceil($expiration / MINUTE);

    $hours = str_pad($hours, 2, '0', STR_PAD_LEFT);
    $minutes = str_pad($minutes, 2, '0', STR_PAD_LEFT);
    
    if ($minutes > MINUTE || $minutes < MINUTE )  {

        $output = [

            'hours' => $hours,
            'minutes' => $minutes
    
        ];

    }

    else {

        $output = [

            'hours' => '01',
            'minutes' => '00'
    
        ];

    }

   return $output;
}

/**
 * 'Форматирует время, оставшееся до конца лота'
 *
 * @param $input Время в виде строки
 *
 * @return @output Время в формате 'ЧЧ':'ММ'
 */

function decorate_time ($input) {

    $time = get_time_left ($input);

    $hours = $time['hours'];
    $minutes = $time['minutes'];

   

    return $output = $hours . ':' . $minutes;
}

?>