<?php

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

// вычисление текущей цены

$current_price = $lot['price'];

if (count($rate_history)) {
    $index = array_key_first($rate_history);
    $current_price = $rate_history[$index]['cost'] ?? $lot['price'];
}

// вычисление минимальной ставки

$price_step = $lot['step'];
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

    // запись в БД при отсутствии значений и обновление цены

    if ($errors === NULL) {
        add_rate($con, $form_fields, $user_id, $lot_id);
        $rate_history = get_all_rates($con, $lot_id);
        $index = array_key_first($rate_history);
        $current_price = $rate_history[$index]['cost'] ?? $lot['price'];
        $min_rate = $current_price + $price_step;
        $last_rate_user_id = $rate_history[$index]['user_id'] ?? null;
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
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => "Просмотр лота {$lot['title']}",
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'categories' => $categories,
]);

print($layout_content);
