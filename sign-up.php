<?php

require_once('init.php');
require_once('data.php');
require_once('helpers.php');
require_once('functions.php');

$categories_id = [];

if ($categories) {
    $categories_id = array_column($categories, "id");
}

$main_content = include_template("main-sign-up.php", ["categories" => $categories]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $required = ["email", "password", "name", "message"];
    $errors = [];

    $rules = [
        "email" => function($value) {
            return is_email_valid($value);
        },
        "password" => function($value) {
            return is_length_valid ($value, 6, 8);
        },
        "message" => function($value) {
            return is_length_valid ($value, 12, 1000);
        }
    ];

    $user = filter_input_array(INPUT_POST,
    [
        "email"=>FILTER_SANITIZE_SPECIAL_CHARS,
        "password"=>FILTER_SANITIZE_SPECIAL_CHARS,
        "name"=>FILTER_SANITIZE_SPECIAL_CHARS,
        "message"=>FILTER_SANITIZE_SPECIAL_CHARS
    ], true);

    foreach ($user as $field => $value) {
        if (isset($rules[$field])) {
            $rule = $rules[$field];
            $errors[$field] = $rule($value);
        }
        if (in_array($field, $required) && empty($value)) {
            $errors[$field] = "Это поле нужно заполнить";
        }
    }

    $errors = array_filter($errors);


    if (count($errors)) {
        $main_content = include_template("main-sign-up.php", [
            "categories" => $categories,
            "user" => $user,
            "errors" => $errors
        ]);
    } else {

            if (!$link) {
                $error = mysqli_connect_error();
            }

            $sql = "SELECT id FROM users WHERE email = ?";
            $stmt = db_get_prepare_stmt($link, $sql, [$user['email']]);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            mysqli_stmt_close($stmt);

            if (mysqli_num_rows($res) > 0)  {

                $errors["email"] = 'Пользователь с этим email уже зарегистрирован';
            }

        if (count($errors)) {
            $main_content = include_template("main-sign-up.php", [
                "categories" => $categories,
                "user" => $user,
                "errors" => $errors
            ]);

        } else {

            $password = password_hash($user["password"], PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (email, name, password, message) VALUES (?, ?, ?, ?)";

            $stmt = db_get_prepare_stmt($link, $sql, [$user['email'], $user['name'], $password, $user['message']]);
            $res = mysqli_stmt_execute($stmt);

            if ($res) {
                header("Location: /login.php");
            } else {
                $error = mysqli_error($link);
            }
        }
    }

}

$layout_content = include_template("layout.php", [
    "content" => $main_content,
    "categories" => $categories,
    "title" => "Регистрация",
    "is_auth" => $is_auth,
    "user_name" => $user_name
]);

print($layout_content);

?>
