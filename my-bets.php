<?php

date_default_timezone_set('Europe/Moscow');

require_once('./utils/db.php');
require_once('./config.php');
require_once('./utils/helpers.php');
require_once('./utils/functions.php');
require_once('./repository/sql-rates.php');
require_once('./repository/sql-categories.php');
require_once('./utils/init-session.php');

if (!isset($_SESSION['username'])) {
    header('Location: login.php?intended=' . urlencode('/my-bets.php'));
    exit();
}

$categories = get_categories($con);
$is_auth = isset($_SESSION['username']);
$user_name = $_SESSION['username'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

$all_rates = get_all_rates_by_user($con, $user_id);

$page_content = include_template('my-bets-main.php', [
    'categories' => $categories,
    'rates' => $all_rates,
    'user_id' => $user_id,
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Главная',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'categories' => $categories,
]);

print($layout_content);
