<?php

require_once('init.php');
require_once('data.php');
require_once('helpers.php');
require_once('functions.php');

if ($is_auth) {

    header("Location: /index.php");
    exit();

}

$main_content = include_template('main-login.php', [
    "categories" => $categories
]);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $required = ["email", "password"];
    $errors = [];

    $rules = [
        "email" => function($value) {
            return is_email_valid($value);
        }
    ];

    $user_info = filter_input_array(INPUT_POST,
    [
        "email"=>FILTER_SANITIZE_SPECIAL_CHARS,
        "password"=>FILTER_SANITIZE_SPECIAL_CHARS
    ], true);

    foreach ($user_info as $field => $value) {
        if (isset($rules[$field])) {
            $rule = $rules[$field];
            $errors[$field] = $rule($value);
        }
        if (in_array($field, $required) && empty($value)) {
            $errors[$field] = "Данное поле нужно заполнить";
        }
    }

    $errors = array_filter($errors);


/*  Проверяет есть ли ошибки   */
    if (count($errors)) {
        $main_content = include_template("main-login.php", [
            "categories" => $categories,
            "user_info" => $user_info,
            "errors" => $errors
        ]);
    } else {
        /*  Если ошибок нет....  */

        /*  Проверяем есть ли соединение....  */
        if (!$link) {
            $error = mysqli_connect_error();
        }

        /*  Если соединение есть то...  */

            $res = is_login_data_correct($link, [$user_info["email"]]);

            $user_data = $res ? mysqli_fetch_array($res, MYSQLI_ASSOC) : null;

            if (!count($errors) and $user_data) {
                if (password_verify($user_info["password"], $user_data["password"])) {
                    $issession = session_start();
                    $_SESSION['user'] = $user_data['name'];
                    $_SESSION['id'] = $user_data['id'];
                }
                else {
                    $errors["password"] = 'Вы ввели неверный пароль';
                }
            } else {
                $errors["email"] = 'Такой пользователь не найден';
            }

            if (count($errors)) {
                $main_content = include_template('main-login.php', [
                    'categories' => $categories,
                    'user_info' => $user_info,
                    'errors' => $errors]);
            } else {
                header("Location: /index.php");
                exit();
            }

    }
}
/*  Проверка переданны ли данные... Если данные не переданы   */
else {
    $main_content = include_template('main-login.php', [
        'categories' => $categories
    ]);

    if (isset($_SESSION['user'])) {
        header("Location: /index.php");
        exit();
    }
};

$layout_content = include_template("layout.php", [
    'content' => $main_content,
    'categories' => $categories,
    'title' => "Вход",
    'is_auth' => $is_auth,
    'user_name' => $user_name
]);

print($layout_content);

?>
