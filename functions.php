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

function get_arrow ($result_query) {
    $row = mysqli_num_rows($result_query);
    if ($row === 1) {
        $arrow = mysqli_fetch_assoc($result_query);
    } else if ($row > 1) {
        $arrow = mysqli_fetch_all($result_query, MYSQLI_ASSOC);
    }

    return $arrow;
};

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

/**
* 'Проверяет правильность введенного логина и пароля'
*
* @param $link cоединение
* @param $data данные
*
* @return 'Возвращает данные из базы данных'
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
* 'Проверяет ставку'
*
* @param $link cоединение
* @param $data данные
*
* @return 'Возвращает данные из базы данных'
*/

function is_get_bets ($link, $data) {

    if (!$link) {

    $error = mysqli_connect_error();
    return $error;

    } else {

        $sql = "SELECT DATE_FORMAT(bets.date_bet, '%d.%m.%y %H:%i') AS date_bet, bets.cost, lots.title, lots.description, lots.path, lots.expiration, lots.id, lots.winner_id, categories.title, users.message
        FROM bets
        JOIN lots ON bets.lot_id = lots.id
        JOIN users ON bets.user_id = users.id
        JOIN categories ON lots.category_id = categories.id
        WHERE bets.user_id = $data
        ORDER BY bets.date_bet DESC;";
        $result = mysqli_query($link, $sql);

        if ($result) {

            $bets_list = mysqli_fetch_all($result, MYSQLI_ASSOC);
            return $bets_list;
        }

        $error = mysqli_error($link);
        return $error;

    }
};

/**
* 'Проверяет данные'
*
* @param $link cоединение
* @param $data данные
*
* @return 'Возвращает данные из базы данных'
*/


function is_get_user_data ($link, $data) {

    if (!$link) {
    $error = mysqli_connect_error();
    return $error;

    } else {

        $sql = "SELECT  users.message AS user_data FROM lots
        JOIN users ON users.id = lots.user_id
        WHERE lots.id = $data";

        $result = mysqli_query($link, $sql);

        if ($result) {
            $contacts = mysqli_fetch_all($result, MYSQLI_ASSOC);
        }

        $error = mysqli_error($link);
        return $error;
    }
};

function is_add_bet_db($link, $cost, $user_id, $lot_id) {
    $sql = "INSERT INTO bets (cost, user_id, lot_id) VALUE (?, ?, ?)";
    $stmt = db_get_prepare_stmt($link, $sql, [$cost, $user_id, $lot_id]);
    $result = mysqli_stmt_execute($stmt);
    if ($result) {
        return $result;
    }
    $error = mysqli_error($link);
    return $error;
};

function is_get_bets_history ($link, $data) {
    if (!$link) {
    $error = mysqli_connect_error();
    return $error;
    } else {
        $sql = "SELECT users.name, bets.cost, DATE_FORMAT(date_bet, '%d.%m.%y %H:%i') AS date_bet
        FROM bets
        JOIN lots ON bets.lot_id=lots.id
        JOIN users ON bets.user_id=users.id
        WHERE lots.id = $data
        ORDER BY bets.date_bet DESC LIMIT 10;";
        $result = mysqli_query($link, $sql);
        if ($result) {
            $list_bets = mysqli_fetch_all($result, MYSQLI_ASSOC);
            return $list_bets;
        }
        $error = mysqli_error($link);
        return $error;
    }
};

function is_bet_counter($link, $data) {

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

function get_lot_date_finish ($link) {
    if (!$link) {
        $error = mysqli_connect_error();
        return $error;
    } else {
        $sql = "SELECT * FROM lots WHERE winner_id IS NULL && expiration <= NOW()";
        $result = mysqli_query($link, $sql);
        if ($result) {
            $lots = mysqli_fetch_all($result, MYSQLI_ASSOC);
            return $lots;
        }
        $error = mysqli_error($link);
        return $error;
    }
};

function get_last_bet ($link, $id) {
    if (!$link) {
        $error = mysqli_connect_error();
        return $error;
    } else {
        $sql = "SELECT * FROM bets
        WHERE lot_id = $id
        ORDER BY date DESC LIMIT 1;";
        $result = mysqli_query($link, $sql);
        if ($result) {
            $bet = get_arrow($result);
            return $bet;
        }
        $error = mysqli_error($con);
        return $error;
    }
};

function add_winner ($link, $winer_id, $lot_id) {
    if (!$link) {
        $error = mysqli_connect_error();
        return $error;
    } else {
        $sql = "UPDATE lots SET winner_id = $winer_id WHERE id = $lot_id";
        $result = mysqli_query($link, $sql);
        if ($result) {
            return $result;
        }
            $error = mysqli_error($link);
            return $error;
    }
};

function get_user_win ($link, $id) {
    if (!$link) {
    $error = mysqli_connect_error();
    return $error;
    } else {
        $sql = "SELECT lots.id, lots.title, users.name, users.message
        FROM bets
        JOIN lots ON bets.lot_id=lots.id
        JOIN users ON bets.user_id=users.id
        WHERE lots.id = $id";
        $result = mysqli_query($link, $sql);
        if ($result) {
            $data = get_arrow($result);
            return $data;
        }
        $error = mysqli_error($con);
        return $error;
    }
};

function get_user_contacts ($link, $id) {
    if (!$link) {
    $error = mysqli_connect_error();
    return $error;
    } else {
        $sql = "SELECT users.name, users.email, users.message FROM users
        WHERE id=$id";
        $result = mysqli_query($link, $sql);
        if ($result) {
            $user_date = get_arrow($result);
            return $user_date;
        }
        $error = mysqli_error($link);
        return $error;
    }
};

?>
