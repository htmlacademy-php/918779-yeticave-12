<?php

require_once('init.php');
require_once('data.php');
require_once('helpers.php');
require_once('functions.php');

if (!$is_auth) {
    header('Location: error.php?error=403');
    exit;
}

$categories_id = [];
$user_id = $_SESSION['id'];

if ($categories) {
    $categories_id = array_column($categories, "id");
}

$main_content = include_template("main-add.php", ["categories" => $categories]);

if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    $required = ["title", "category_id", "description", "price", "step", "expiration"];
    $rules = [

        "category_id" => function ($value) use ($categories_id) {
            return is_category_valid($value, $categories_id, 1000000);
        },

        "price" => function ($value) {
            return is_number_valid($value, 1000000);
        },

        "step" => function ($value) {
            return is_number_valid($value, 1000000);
        },

        "expiration" => function ($value) {
            return date_valid($value, 18);
        }
    ];

    $lot = filter_input_array(
        INPUT_POST,
        [
        "title" => FILTER_DEFAULT,
        "category_id" => FILTER_DEFAULT,
        "description" => FILTER_DEFAULT,
        "price" => FILTER_DEFAULT,
        "step" => FILTER_DEFAULT,
        "expiration" => FILTER_DEFAULT
        ],
        true
    );

    $errors = form_validate($lot, $rules, $required);

    if (!empty($_FILES["photo"]["name"])) {
        $tmp_name = $_FILES["photo"]["tmp_name"];
        $path = $_FILES["photo"]["name"];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file_type = finfo_file($finfo, $tmp_name);
        if ($file_type === "image/jpeg") {
            $ext = ".jpg";
        } elseif ($file_type === "image/png") {
            $ext = ".png";
        };
        if ($ext) {
            $filename = uniqid() . $ext;
            $lot["path"] = "uploads/" . $filename;
            move_uploaded_file($_FILES["photo"]["tmp_name"], "uploads/" . $filename);
        } else {
            $errors["photo"] = "Допустимые форматы файлов: jpg, jpeg, png";
        }
    } else {
        $errors["photo"] = "Вы не загрузили изображение";
    }

    if (count($errors)) {
        $main_content = include_template("main-add.php", [
            "categories" => $categories,
            "lot" => $lot,
            "errors" => $errors
         ]);
    } else {
        $sql = "INSERT INTO lots (title, category_id, description, price, step, expiration, path, user_id)
        VALUES (?, ?, ?, ?, ?, ?, ?, $user_id)";
        $stmt = db_get_prepare_stmt($link, $sql, $lot);
        $res = mysqli_stmt_execute($stmt);

        if (!$res) {
            header("Location: /error.php", true, 500);
            exit;
        }

        $lot_id = mysqli_insert_id($link);
        header("Location: /lot.php?id=" . $lot_id);
    }
};

$layout_content = include_template("layout.php", [
    "is_auth" => $is_auth,
    "content" => $main_content,
    "categories" => $categories,
    "title" => "Добавляет лот",
    "user_name" => $user_name
    ]);

print($layout_content);
