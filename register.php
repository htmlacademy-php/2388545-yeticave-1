<?php

require_once('./utils/helpers.php');
require_once('./utils/functions.php');
require_once('./utils/data.php');
require_once('./utils/db.php');
require_once('./repository/sql-categories.php');
require_once('./repository/sql-user.php');
require_once('./utils/validate-rules.php');

$categories = get_categories($con);
$errors = [];
$form_fields = [];
$is_form_send = $_SERVER['REQUEST_METHOD'] === 'POST';

// проверка отправки формы

if ($is_form_send) {

    // извлечение и очистка значений из формы

    $form_fields = filter_input_array(
        INPUT_POST,
        [
            'email' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'password' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'name' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'message' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        ],
        $add_empty = true
    );

    // создание правил валидации

    $rules = [
        'email' => 'required|string|email|unique:users,email',
        'password' => 'required|string|min:5',
        'name' => 'required|string',
        'message' => 'required|string',
    ];

    // запись ошибок в массив

    $errors = validate($form_fields, $rules, $con);
}

// отрисовка страницы

if (!$is_form_send || $errors !== null) {
    $page_content = include_template('register-main.php', [
        'categories' => $categories,
        'errors' => $errors,
        'form_fields' => $form_fields,
    ]);

    $layout_content = include_template('layout.php', [
        'content' => $page_content,
        'title' => 'Регистрация',
        'is_auth' => $is_auth,
        'user_name' => $user_name,
        'categories' => $categories,
    ]);

    print($layout_content);

    exit();
}

add_user($con, $form_fields);

header("Location: /");
exit();
