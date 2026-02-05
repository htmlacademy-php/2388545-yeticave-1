<?php

/**
 * @var mysqli $con
 */

date_default_timezone_set('Europe/Moscow');

require_once('./utils/helpers.php');
require_once('./utils/functions.php');
require_once('./utils/db.php');
require_once('./repository/sql-lot.php');
require_once('./repository/sql-categories.php');
require_once('./repository/sql-rates.php');
require_once('./utils/validate-rules.php');
require_once('./utils/init-session.php');

$categories = get_categories($con);
$is_auth = isset($_SESSION['username']);
$user_name = $_SESSION['username'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

$restriction = null;
$errors = [];
$form_fields = [];
$is_form_send = $_SERVER['REQUEST_METHOD'] === 'POST';

// проверка существования параметра запроса с id лота

if (!isset($_GET['id'])) {
    show_404($categories, $is_auth, $user_name);
    exit;
}

// получение лота по id

$lot_id  = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$lot = get_lot($con, $lot_id);

// проверка существования лота с необходимым id

if ($lot === null) {
    show_404($categories, $is_auth, $user_name);
    exit;
}

// получение истории ставок

$rate_history = get_all_rates($con, $lot_id);

// получение id пользователя, сделавшего последнюю ставку

$last_rate_user_id = $rate_history[0]['user_id'] ?? null;

// проверка связей пользователя с лотом

$is_user_last_rate = $user_id === $last_rate_user_id;
$is_user_lot_author = $user_id === ($lot['user_id'] ?? 0);

// проверка активности лота

$time_left = isset($lot['date']) ? calculate_time_difference($lot['date']) : 0;
$is_lot_expired = (($time_left[0] ?? 0) < 0 || (($time_left[0] ?? 0) === 0 && ($time_left[1] ?? 0) <= 0));

// проверка ограничений ввода ставок

$can_user_make_rate = $is_auth && !$is_lot_expired && !$is_user_last_rate && !$is_user_lot_author;

// вычисление текущей цены

$current_price = $lot['price'] ?? 0;

if (count($rate_history)) {
    $index = array_key_first($rate_history);
    $current_price = $rate_history[$index]['cost'] ?? $lot['price'] ?? 0;
}

// вычисление минимальной ставки

$price_step = $lot['step'] ?? 0;
$min_rate = $current_price + $price_step;

// Проверка отправки формы

if ($is_form_send) {
    // извлечение и очистка значений из формы

    $form_fields = filter_input_array(
        INPUT_POST,
        [
            'cost' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        ],
        $add_empty = true
    );

    // создание правил валидации

    $rules = [
        'cost' => "required|positive_number|int|min_int:$min_rate",
    ];

    // запись ошибок в массив

    $errors = validate($form_fields, $rules, $con);

    // проверка наличия ограничений добавления ставки

    if (!$can_user_make_rate) {
        $restriction = 'Отсутствует возможность добавления ставки';
    }

    // запись в БД при отсутствии ошибок и ограничений и обновление данных

    if ($errors === null && $restriction === null) {
        add_rate($con, $form_fields, $user_id, $lot_id);
        $rate_history = get_all_rates($con, $lot_id);
        $index = array_key_first($rate_history);
        $current_price = $rate_history[$index]['cost'] ?? $lot['price'] ?? 0;
        $min_rate = $current_price + $price_step;
        $last_rate_user_id = $rate_history[$index]['user_id'] ?? null;
        $is_user_last_rate = $user_id === $last_rate_user_id;
        $can_user_make_rate = $is_auth && !$is_lot_expired && !$is_user_last_rate && !$is_user_lot_author;
    }
}

// отрисовка страницы с искомым лотом

$page_content = include_template('lot-main.php', [
    'categories' => $categories,
    'lot' => $lot,
    'is_auth' => $is_auth,
    'user_id' => $user_id,
    'last_rate_user_id' => $last_rate_user_id,
    'rate_history' => $rate_history,
    'current_price' => $current_price,
    'min_rate' => $min_rate,
    'errors' => $errors,
    'restriction' => $restriction,
    'can_user_make_rate' => $can_user_make_rate,
    'time_left' => $time_left,
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => "Просмотр лота " . ($lot['title'] ?? ''),
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'categories' => $categories,
]);

print($layout_content);
