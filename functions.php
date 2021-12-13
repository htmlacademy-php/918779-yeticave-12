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
    $minutes = ceil($expiration / SECONDS_PER_MINUTE);

    if ($minutes == MINUTES_PER_HOUR)  {

        $hours = $hours + 1;
        $minutes = 0;

    }

    $hours = str_pad($hours, 2, '0', STR_PAD_LEFT);
    $minutes = str_pad($minutes, 2, '0', STR_PAD_LEFT);

    $output = [

        'hours' => $hours,
        'minutes' => $minutes

    ];

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


    return $time['hours'] . ':' . $time['minutes'];
}

/**
 * 'Проверяет существование категории'
 *
 * @param $id номер категории, $allowed_list  массив с категориями
 *
 * @return 'Если категории нет, то возвращает сообщение, что Указанной катагории не существует'
 */

function is_category_valid ($id, $allowed_list) {
    if (!in_array($id, $allowed_list)) {
        return "Указанная категория не существует";
    }
}

/**
* 'Проверяет является ли содержимое поля формы целым числом больше нуля'
*
* @param $num число
*
* @return 'Если содержимое не является целым числом, которое больше нуля возвращает сообщение, что Содержимое поля должно быть целым числом больше нуля'
*/

function is_number_valid ($num) {

    if (empty($num) || !ctype_digit($num) || $num < 0) {

        return 'Содержимое поля должно быть целым числом больше нуля';
    }

};

/**
* 'Проверяет дату завершения лота'
*
* @param $date дата в формате ГГГГ-ММ-ДД
*
* @return 'Если дата больше текущей менее чем на один день или содержимое поля «дата завершения» не является датой в формате «ГГГГ-ММ-ДД» возвращает соответствующее сообщение'
*/

function valid_date ($date) {
    if (is_date_valid($date)) {

        $date01 = strtotime($date);
        $date02 = strtotime("now");

        if ($date01 < $date02 + SECONDS_IN_DAY) {

            return 'Дата должна быть больше текущей не менее чем на один день';

        }

    } else {

        return "Содержимое поля «дата завершения» должно быть датой в формате «ГГГГ-ММ-ДД»";
    }
};


/**
* 'Проверяет корректность e-mail'
*
* @param $email электронная почта
*
* @return 'Возвращает сообщение, что e-mail должен быть корректным'
*/

function is_email_valid ($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "E-mail должен быть корректным";
    }
};

/**
* 'Проверяет длину строки'
*
* @param $value строка
* @param $min минимальное количество символов в строке
* @param $max максимальное количество символов в строке
*
* @return 'Возвращает сообщение, о необходимом количестве символов в строке'
*/

function is_length_valid ($value, $min, $max) {
    if ($value) {
        $len = strlen($value);
        if ($len < $min or $len > $max) {
            return "Значение должно быть от $min до $max символов";
        }
    }
};

/**
* 'Проверяет наличие введенного e-mail в базе данных'
*
* @param $link cоединение
* @param $sql запрос
* @param $data данные
*
* @return 'Возвращает данные из базы данных'
*/

function is_email_used ($link, $sql, $data) {

    $stmt = db_get_prepare_stmt($link, $sql, $data);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_bind_result($stmt, $res);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    return $res;
};

?>
