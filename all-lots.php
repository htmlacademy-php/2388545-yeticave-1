<?php

/**
 * @var mysqli $con
 */

date_default_timezone_set('Europe/Moscow');

require_once('./utils/db.php');
require_once('./config.php');
require_once('./utils/helpers.php');
require_once('./utils/functions.php');
require_once('./repository/sql-lots.php');
require_once('./repository/sql-categories.php');
require_once('./utils/init-session.php');

$per_page = LOTS_BY_CATEGORY_PER_PAGE;

$categories = get_categories($con);
$is_auth = isset($_SESSION['username']);
$user_name = $_SESSION['username'] ?? null;
$user_id = $_SESSION['user_id']  ?? null;

$category_id = filter_input(INPUT_GET, 'category', FILTER_SANITIZE_NUMBER_INT);
$current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

// проверка существования параметра запроса с категорией лотов

if (!isset($_GET['category'])) {
    show_404($categories, $is_auth, $user_name);
    exit;
}

// получение названия категории для заголовка

$category_title = 'Разное';
foreach ($categories as $category) {
    if ($category['id'] === $category_id) {
        $category_title = $category['name'];
        break;
    }
}

// вычисление количества лотов и страниц

$lots_count = get_count_lots_by_category($con, $category_id);
$total_pages = get_count_pages($lots_count, $per_page);

// проверка существования запрошенной страницы

if ($current_page > $total_pages) {
    $current_page = $total_pages;
}

// получение лотов для текущей страницы

$lots = get_lots_by_category($con, $category_id, LOTS_BY_CATEGORY_PER_PAGE, $current_page);

// отрисовка страницы

$page_content = include_template('all-lots-main.php', [
    'categories' => $categories,
    'category_id' => $category_id,
    'category_title' => $category_title,
    'lots' => $lots,
    'current_page' => $current_page,
    'total_pages' => $total_pages,
    'lots_count' => $lots_count,
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => "Лоты - $category_title",
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'categories' => $categories,
]);

print($layout_content);
