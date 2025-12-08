<?php

require('helpers.php');
require('functions.php');
require('data.php');

$con = mysqli_connect("localhost", "root", "","yeticave");

// $lot_name = '2014 Snowboard';
// $lot_description = 'Сноуборд модель 2014 года';
// $lot_img = 'img/lot-5.jpg';
// $lot_start_price = 19000;
// $lot_end_date = '2026-08-01';
// $lot_step = 500;
// $user_id = 1;
// $category_id = 1;
// $sqlLotsInsert = 'INSERT INTO lots (name, description, img, start_price, end_date, step, user_id, category_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
// $stmtLots = mysqli_prepare($con, $sqlLotsInsert);
// mysqli_stmt_bind_param($stmtLots, 'sssisiii', $lot_name, $lot_description, $lot_img, $lot_start_price, $lot_end_date, $lot_step, $user_id, $category_id);
// mysqli_stmt_execute($stmtLots);

// $category_name = 'Маски';
// $category_slug = 'masks';
// $sqlCategoryInsert = 'INSERT INTO categories (name, slug) VALUES (?, ?)';
// $stmtCategory = mysqli_prepare($con, $sqlCategoryInsert);
// mysqli_stmt_bind_param($stmtCategory, 'ss', $category_name, $category_slug);
// mysqli_stmt_execute($stmtCategory);

$sqlLots = "SELECT l.name as title, l.start_price as price, l.img, l.end_date as date, c.name as category, l.start_date FROM lots l JOIN categories c ON l.category_id = c.id ORDER BY l.start_date DESC";
$resultLots = mysqli_query($con, $sqlLots);
$lots = mysqli_fetch_all($resultLots, MYSQLI_ASSOC);

$sqlCategories = "SELECT name, slug FROM categories";
$resultCategories = mysqli_query($con, $sqlCategories);
$categories = mysqli_fetch_all($resultCategories, MYSQLI_ASSOC);

var_dump($lots);

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
