<?php

require('helpers.php');
require('functions.php');
require('data.php');
require('db.php');

if (!$con) {
    print("Ошибка подключения: " . mysqli_connect_error());
} else {
    // $lot_name = '2014 Snowboard';
    // $lot_description = 'Сноуборд модель 2014 года';
    // $lot_img = 'img/lot-5.jpg';
    // $lot_start_price = 19000;
    // $lot_end_date = '2026-08-01';
    // $lot_step = 500;
    // $user_id = 1;
    // $category_id = 1;
    // $sql_lots_insert = 'INSERT INTO lots (name, description, img, start_price, end_date, step, user_id, category_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
    // $stmt_lots = mysqli_prepare($con, $sql_lots_insert);
    // mysqli_stmt_bind_param($stmt_lots, 'sssisiii', $lot_name, $lot_description, $lot_img, $lot_start_price, $lot_end_date, $lot_step, $user_id, $category_id);
    // mysqli_stmt_execute($stmt_lots);

    // $category_name = 'Маски';
    // $category_slug = 'masks';
    // $sql_category_insert = 'INSERT INTO categories (name, slug) VALUES (?, ?)';
    // $stmt_category = mysqli_prepare($con, $sql_category_insert);
    // mysqli_stmt_bind_param($stmt_category, 'ss', $category_name, $category_slug);
    // mysqli_stmt_execute($stmt_category);

    $sql_lots = "SELECT l.name as title, l.start_price as price, l.img, l.end_date as date, c.name as category, l.start_date "
        . "FROM lots l "
        . "JOIN categories c ON l.category_id = c.id "
        . "WHERE l.end_date > NOW() "
        . "ORDER BY l.start_date DESC "
        . "LIMIT 6";

    $result_lots = mysqli_query($con, $sql_lots);

    if ($result_lots) {
        $lots = mysqli_fetch_all($result_lots, MYSQLI_ASSOC);
    } else {
        print("Ошибка MySQL: " . mysqli_error($con));
    }

    $sql_categories = "SELECT name, slug FROM categories";
    $result_categories = mysqli_query($con, $sql_categories);

    if ($result_categories) {
        $categories = mysqli_fetch_all($result_categories, MYSQLI_ASSOC);
    } else {
        print("Ошибка MySQL: " . mysqli_error($con));
    }
}

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
