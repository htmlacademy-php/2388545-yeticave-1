<?php

date_default_timezone_set('Europe/Moscow');

require_once('./utils/db.php');
require_once('./config.php');
require_once('./utils/helpers.php');
require_once('./utils/functions.php');
require_once('./repository/sql-lots.php');
require_once('./repository/sql-categories.php');
require_once('./utils/init-session.php');

$per_page = LOTS_PER_PAGE;

function get_count_lots_by_category(mysqli $con, int $category_id)
{
    $sql_lots = <<<SQL
        SELECT COUNT(*) as total
        FROM lots l
        JOIN categories c ON l.category_id = c.id
        WHERE l.end_date > NOW() AND c.id = ?
    SQL;

    $stmt_lots = db_get_prepare_stmt($con, $sql_lots, [$category_id]);
    mysqli_stmt_execute($stmt_lots);
    $result_lots = mysqli_stmt_get_result($stmt_lots);

    if (!$result_lots) {
        echo "Произошла ошибка MySQL";
        die();
    }

    $row = mysqli_fetch_assoc($result_lots);
    return (int) $row['total'];
}

// привести к инту?
function get_count_pages($lots_count, $per_page)
{
    return $lots_count / $per_page;
}

function get_lots_by_category(mysqli $con, int $category_id, $per_page, $page_number)
{
    $offset = ($page_number - 1) * $per_page;

    $sql_lots = <<<SQL
        SELECT l.id, l.name as title, l.start_price as price, l.img, l.end_date as date, c.name as category, l.start_date
        FROM lots l
        JOIN categories c ON l.category_id = c.id
        WHERE l.end_date > NOW() AND c.id = ?
        ORDER BY l.start_date DESC
        LIMIT ?
        OFFSET ?
    SQL;

    $stmt_lots = db_get_prepare_stmt($con, $sql_lots, [$category_id, $per_page, $offset]);
    mysqli_stmt_execute($stmt_lots);

    $result_lots = mysqli_stmt_get_result($stmt_lots);

    if (!$result_lots) {
        echo "Произошла ошибка MySQL";
        die();
    }

    $lots = mysqli_fetch_all($result_lots, MYSQLI_ASSOC);

    return $lots;
}


$categories = get_categories($con);
$is_auth = isset($_SESSION['username']);
$user_name = $_SESSION['username'] ?? null;
$user_id = $_SESSION['user_id'];

// проверка существования параметра запроса с категорией лотов

if (!isset($_GET['category'])) {
    show_404($categories, $is_auth, $user_name);
    exit;
}

// получение лотов из выбранной категории

$category_id = filter_input(INPUT_GET, 'category');
$lots = get_lots_by_category($con, $category_id, LOTS_PER_PAGE, $page_number);

// отрисовка страницы

$page_content = include_template('all-lots-main.php', [
    'categories' => $categories,
    'lots' => $lots,
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Лоты',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'categories' => $categories,
]);

print($layout_content);
