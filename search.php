<?php

require_once('./utils/helpers.php');
require_once('./utils/functions.php');
require_once('./utils/db.php');
require_once('./config.php');
require_once('./repository/sql-lots.php');
require_once('./repository/sql-categories.php');
require_once('./utils/init-session.php');

$is_form_send = $_SERVER['REQUEST_METHOD'] === 'GET';

// проверка отправки формы

if ($is_form_send) {

    // извлечение и очистка значений из формы

    $search_string = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
}

$per_page = FOUND_LOTS_PER_PAGE;

$categories = get_categories($con);
$is_auth = isset($_SESSION['username']);
$user_name = $_SESSION['username'] ?? null;

$current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$search_param = isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '';

// получение лотов, соответствующих поисковому запросу

$found_lots = search_lots($con, $search_string, $per_page, $current_page);

// вычисление количества лотов и страниц

$lots_count = get_count_search_lots($con, $search_string);
$total_pages = get_count_pages($lots_count, $per_page);

// проверка существования запрошенной страницы

if ($current_page > $total_pages) {
    $current_page = $total_pages;
}

$page_content = include_template('search-main.php', [
    'categories' => $categories,
    'lots' => $found_lots,
    'search_string' => $search_string,
    'current_page' => $current_page,
    'total_pages' => $total_pages,
    'search_param' => $search_param,
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Поиск',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'categories' => $categories,
]);

print($layout_content);
