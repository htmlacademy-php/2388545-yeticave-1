<?php

date_default_timezone_set('Europe/Moscow');

require_once('./utils/helpers.php');
require_once('./utils/functions.php');
require_once('./utils/data.php');
require_once('./utils/db.php');
require_once('./repository/sql-lot.php');
require_once('./repository/sql-categories.php');
require_once('./utils/init-session.php');

$categories = get_categories($con);
$is_auth = isset($_SESSION['username']);
$user_name = $_SESSION['username'] ?? null;

// проверка существования параметра запроса с id лота

if (!isset($_GET['id'])) {
    show_404($categories, $is_auth, $user_name);
    exit;
}

// получение лота по id

$lot_id  = filter_input(INPUT_GET, 'id');
$lot = get_lot($con, $lot_id);

// проверка существования лота с необходимым id

if ($lot === null) {
    show_404($categories, $is_auth, $user_name);
    exit;
}

// отрисовка страницы с искомым лотом

$page_content = include_template('lot-main.php', [
    'categories' => $categories,
    'lot' => $lot,
    'is_auth' => $is_auth,
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Лоты',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'categories' => $categories,
]);

print($layout_content);
