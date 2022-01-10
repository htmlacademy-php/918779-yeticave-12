<?php

require_once('init.php');
require_once('data.php');
require_once('helpers.php');
require_once('functions.php');

$lots = [];

$search = trim($_GET['search']) ?? '';

if ($search) {

    $current_page = (int) ($_GET["page"] ?? 1);

    $offset = ($current_page - 1) * PAGE_ITEMS;

    $sql = "SELECT COUNT(*) as count FROM lots WHERE MATCH(lots.title, lots.description) AGAINST(?)";

    $stmt = db_get_prepare_stmt($link, $sql, [$search]);
    mysqli_stmt_execute($stmt);
    $result= mysqli_stmt_get_result($stmt);

    $items_count = mysqli_fetch_assoc($result)['count'];
    $pages_count = ceil($items_count / PAGE_ITEMS);

    if($current_page === 0 || $current_page > $pages_count) {

        header("Location: /index.php");
        exit;
    }

    $pages = range(1, $pages_count);

    $sql = "SELECT lots.id, lots.title, lots.price, lots.path, lots.expiration, categories.title as category FROM lots
    JOIN categories ON lots.category_id=categories.id
    WHERE MATCH(lots.title, lots.description) AGAINST(?) ORDER BY expiration DESC LIMIT " . PAGE_ITEMS . " OFFSET " . $offset;

    $stmt = db_get_prepare_stmt($link, $sql, [$search]);
    mysqli_stmt_execute($stmt);
    $result= mysqli_stmt_get_result($stmt);

    if ($result) {

            $lots = mysqli_fetch_all($result, MYSQLI_ASSOC);

    }

}

$main_content = include_template("main-search.php", [
    "categories" => $categories,
    "search" => $search,
    "lots" => $lots,
    "pages_count" => $pages_count,
    "pages" => $pages,
    "current_page" => $current_page
]);

$layout_content = include_template("layout.php", [
    "content" => $main_content,
    "categories" => $categories,
    "title" => "Результат поиска",
    "search" => $search,
    "is_auth" => $is_auth,
    "user_name" => $user_name
]);

print($layout_content);

?>
