<?php

require_once('./utils/helpers.php');
require_once('./utils/functions.php');
require_once('./utils/data.php');
require_once('./utils/db.php');
require_once('./repository/sql-categories.php');
require_once('./utils/validate-rules.php');

$categories = get_categories($con);
$errors = [];
$form_fields = [];
$uploaded_file = [];

// проверка отправки формы

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // извлечение и очистка значений из формы

    $form_fields = filter_input_array(
        INPUT_POST,
        [
            'lot-name' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'category' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'message' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'lot-rate' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'lot-step' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'lot-date' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        ],
        $add_empty = true
    );

    $uploaded_file = $_FILES['lot-img'] ?? [];

    $form_fields['lot-img'] = $uploaded_file;

    // создание правил валидации

    $rules = [
        'lot-name' => 'required|string',
        'category' => 'required|string|select_not_default:default',
        'message' => 'required|string',
        'lot-rate' => 'required|positive_number',
        'lot-step' => 'required|positive_number|int',
        'lot-date' => 'required|date_format|date_after_tomorrow',
        'lot-img' => 'required_img|img_format:jpg,jpeg,png'
    ];

    // запись ошибок в массив

    $errors = validate($form_fields, $rules, $con);

    $errors = array_filter($errors);
}

// отрисовка страницы

$page_content = include_template('add-main.php', [
    'categories' => $categories,
    'errors' => $errors,
    'form_fields' => $form_fields,
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Добавление лота',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'categories' => $categories,
]);

print($layout_content);
