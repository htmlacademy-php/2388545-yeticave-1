<?php

/**
 * @var mysqli $con
 */

date_default_timezone_set('Europe/Moscow');

require_once('./utils/helpers.php');
require_once('./utils/functions.php');
require_once('./utils/db.php');
require_once('./repository/sql-lots.php');
require_once('./repository/sql-categories.php');
require_once('./utils/init-session.php');
require_once('./getwinner.php');

$lots = get_lots($con);
$categories = get_categories($con);
$is_auth = isset($_SESSION['username']);
$user_name = $_SESSION['username'] ?? null;

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
