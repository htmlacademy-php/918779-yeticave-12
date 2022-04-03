<?php

require_once("data.php");
require_once("helpers.php");

/**
 * Форматирует сумму и добавляет к ней знак рубля
 * @param $input Сумма в виде числа
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
 * Возвращает массив из результата запроса
 * @param $result_query Результат запроса к базе данных
 * @return array Возвращает массив
 */
function get_arrow ($result_query) {
    $row = mysqli_num_rows($result_query);
    if ($row === 0 || $row === 1) {
        $arrow = mysqli_fetch_assoc($result_query);
    } else if ($row > 1) {
        $arrow = mysqli_fetch_all($result_query, MYSQLI_ASSOC);
    }
    return $arrow;
};

/**
 * Определяет время оставшееся до окончания лота
 * @param $input Дата в виде строки
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
 * Определяет время прошедшее с окончания лота
 * @param $input Время окочания лота
 * @return @output Время прошедшее с окончания лота или время окончания лота
 */
function get_time_after_end ($input) {
    $date01 = date_create($input);
    $date02 = date_create("now");
    $interval = date_diff($date01, $date02);

    $format_diff = date_interval_format($interval, "%d %H %I");
    $arr = explode(" ", $format_diff);

    $days = $arr[0];
    $hours = $arr[0] * 24 + $arr[1];
    $minutes = intval($arr[2]);

    $time = [
        'days' => $days,
        'hours' => $hours,
        'minutes' => $minutes
    ];

    switch ($time['days']) {
        case 0:
            switch ($time['hours']) {
                case 0:
                    return sprintf('%d %s назад', $time['minutes'], get_noun_plural_form($time['minutes'],'минута', 'минуты', 'минут'));
                    break;
                default:
                    return sprintf('%d %s %d %s назад', $time['hours'], get_noun_plural_form($time['hours'],'час', 'часа', 'часов'), $time['minutes'], get_noun_plural_form($time['minutes'],'минута', 'минуты', 'минут'));
            }
        case 1:
            return sprintf('Вчера, %s', date_format($date01, 'H:i'));
            break;
        default:
            return sprintf('%s в %s', date_format($date01, 'd.m.y'), date_format($date01, 'H:i'));
    }
};

/**
 * 'Форматирует время, оставшееся до конца лота'
 * @param $input Время в виде строки
 * @return @output Время в формате 'ЧЧ':'ММ'
 */
function decorate_time ($input) {
    $time = get_time_left ($input);
    return $time['hours'] . ':' . $time['minutes'];
}

/**
 * Проверяет существование категории
 * @param $id номер категории, $allowed_list  массив с категориями
 * @return 'Если категории нет, то возвращает сообщение, что Указанной катагории не существует'
 */
function is_category_valid ($id, $allowed_list) {
    if (!in_array($id, $allowed_list)) {
        return "Указанная категория не существует";
    }
}

/**
* Проверяет является ли содержимое поля формы целым числом больше нуля
* @param $num число
* @return 'Если содержимое не является целым числом, которое больше нуля возвращает сообщение, что Содержимое поля должно быть целым числом больше нуля'
*/
function is_number_valid ($num) {
    if (empty($num) || !ctype_digit($num) || $num < 0) {
        return 'Содержимое поля должно быть целым числом больше нуля';
    }
};

/**
* Проверяет дату завершения лота
* @param $date дата в формате ГГГГ-ММ-ДД
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
* Проверяет корректность e-mail
* @param $email электронная почта
* @return 'Возвращает сообщение, что e-mail должен быть корректным'
*/
function is_email_valid ($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "E-mail должен быть корректным";
    }
};

/**
* Проверяет длину строки
* @param $value строка
* @param $min минимальное количество символов в строке
* @param $max максимальное количество символов в строке*
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
* Проверяет правильность введенного логина и пароля
* @param $link cоединение
* @param $data логин и пароль
* @return 'Возвращает данные из БД о существовании в ней указанных логина и пароля'
*/
function is_login_data_correct ($link, $data) {
    $sql = "SELECT  id, email, name, password FROM users WHERE email = ?";
    $stmt = db_get_prepare_stmt($link, $sql, $data);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    return $res;
};

/**
* Добавляет в БД данные о сделанной ставке
* @param $link cоединение
* @param $cost Сумма ставки
* @param $user_id id пользователя
* @param $lot_id id лота
* @return 'Возвращает переданные в БД данные или ошибку, если нет соединения с БД'
*/
function add_bet_db($link, $cost, $user_id, $lot_id) {
    $sql = "INSERT INTO bets (cost, user_id, lot_id) VALUE (?, ?, ?)";
    $stmt = db_get_prepare_stmt($link, $sql, [$cost, $user_id, $lot_id]);
    $result = mysqli_stmt_execute($stmt);
    if ($result) {
        return $result;
    }
    $error = mysqli_error($link);
    return $error;
};

/**
* Возвращает количество сделанных ставок
* @param $link cоединение
* @param $data id лота
* @return 'Возвращает количество ставок по лоту'
*/
function get_bet_count($link, $data) {
    $result = mysqli_query($link, "SELECT COUNT(*) as cnt FROM bets
    JOIN lots ON bets.lot_id=lots.id
    WHERE lots.id = $data");
    $bets_count = mysqli_fetch_assoc($result)['cnt'];

    if ($result) {
        return $bets_count;
    }
    $error = mysqli_error($link);
    return $error;
};

/**
* Валидация формы
* * @param $array Массив с полученными из формы данными
* @return 'Возвращает ошибки в форме'
*/
function form_validate($array, $required) {
    $errors = [];

    foreach ($array as $field => $value) {
        if (isset($rules[$field])) {
            $rule = $rules[$field];
            $errors[$field] = $rule($value);
        }
        if (in_array($field, $required) && empty($value)) {
            $errors[$field] = "Данное поле необходимо заполнить";
        }
    }

    return $errors = array_filter($errors);
};

?>
