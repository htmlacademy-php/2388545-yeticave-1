<?php

require_once('./utils/helpers.php');
require_once('./utils/functions.php');
require_once('./utils/data.php');
require_once('./utils/db.php');
require_once('./repository/sql-lots.php');
require_once('./repository/sql-categories.php');
require_once('./utils/init-session.php');

$is_form_send = $_SERVER['REQUEST_METHOD'] === 'GET';

// проверка отправки формы

if ($is_form_send) {

    // извлечение и очистка значений из формы

    $search_string = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
}

$found_lots = search_lots($con, $search_string);

$categories = get_categories($con);
$is_auth = isset($_SESSION['username']);
$user_name = $_SESSION['username'] ?? null;

$page_content = include_template('search-main.php', [
    'categories' => $categories,
    'lots' => $found_lots,
    'search_string' => $search_string,
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Главная',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'categories' => $categories,
]);

print($layout_content);
