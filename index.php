<?php

require('helpers.php');
require('functions.php');
require('data.php');
require('db.php');

if (!$con) {
    echo "Произошла ошибка подключения к БД";
    die();
}

$sql_lots = <<<SQL
SELECT l.name as title, l.start_price as price, l.img, l.end_date as date, c.name as category, l.start_date
FROM lots l
JOIN categories c ON l.category_id = c.id
WHERE l.end_date > NOW()
ORDER BY l.start_date DESC
LIMIT 6
SQL;

$result_lots = mysqli_query($con, $sql_lots);

if (!$result_lots) {
    echo "Произошла ошибка MySQL";
    die();
}

$lots = mysqli_fetch_all($result_lots, MYSQLI_ASSOC);

$sql_categories = <<<SQL
SELECT name, slug
FROM categories
SQL;

$result_categories = mysqli_query($con, $sql_categories);

if (!$result_categories) {
    echo "Произошла ошибка MySQL";
    die();
}

$categories = mysqli_fetch_all($result_categories, MYSQLI_ASSOC);

date_default_timezone_set('Europe/Moscow');

$page_content = include_template('main.php', [
    'categories' => $categories,
    'lots' => $lots,
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Главная',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'categories' => $categories,
]);

print($layout_content);
