<?php

require_once('./utils/helpers.php');
require_once('./utils/functions.php');
require_once('./utils/db.php');
require_once('./repository/sql-categories.php');
require_once('./repository/sql-user.php');
require_once('./utils/validate-rules.php');
require_once('./utils/init-session.php');

$categories = get_categories($con);
$is_auth = isset($_SESSION['username']);
$user_name = $_SESSION['username'] ?? null;

$errors = [];
$form_fields = [];
$is_form_send = $_SERVER['REQUEST_METHOD'] === 'POST';
$intended_param = isset($_GET['intended']) ? '?intended=' . urlencode($_GET['intended']) : '';

// проверка отправки формы

if ($is_form_send) {

    // извлечение и очистка значений из формы

    $form_fields = filter_input_array(
        INPUT_POST,
        [
            'email' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'password' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        ],
        $add_empty = true
    );

    // создание правил валидации

    $rules = [
        'email' => 'required|string|email|exist:users,email',
        'password' => 'required|string|password',
    ];

    // запись ошибок в массив

    $errors = validate($form_fields, $rules, $con);
}

// отрисовка страницы

if (!$is_form_send || $errors !== null) {
    $page_content = include_template('login-main.php', [
        'categories' => $categories,
        'errors' => $errors,
        'form_fields' => $form_fields,
        'intended_param' => $intended_param,
    ]);

    $layout_content = include_template('layout.php', [
        'content' => $page_content,
        'title' => 'Вход',
        'is_auth' => $is_auth,
        'user_name' => $user_name,
        'categories' => $categories,
    ]);

    print($layout_content);

    exit();
};

$user_data = get_existing_data($con, $form_fields['email'], 'users', 'email');

$_SESSION['username'] = $user_data['login'];
$_SESSION['user_id'] = $user_data['id'];

if (isset($_GET['intended'])) {
    $intended_url = $_GET['intended'];
    header("Location: " . $intended_url);
    exit;
}

header("Location: /");
exit();
