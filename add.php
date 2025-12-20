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

    $uploaded_file = $_FILES['lot-img'];

    // создание правил валидации

    $rules = [
        'lot-name' => 'required',
        'category' => 'required|select_not_default:default',
        'message' => 'required',
        'lot-rate' => 'required|positive_number',
        'lot-step' => 'required|positive_number|int',
        'lot-date' => 'required|date_format|date_after_tomorrow',
    ];

    // запись ошибок в массив

    $errors = validate($form_fields, $rules, $con);

    // проверка загруженного файла

    if (!empty($uploaded_file['name'])) {
        $tmp_name = $_FILES['lot-img']['tmp_name'];
        $original_name = $_FILES['lot-img']['name'];

        $ext = pathinfo($original_name, PATHINFO_EXTENSION);
        $allowed = ['jpg', 'jpeg', 'png'];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file_type = finfo_file($finfo, $tmp_name);

        if (!in_array(strtolower($ext), $allowed)) {
            $errors['lot-img'] = "Загрузите картинку в формате .png, .jpg или .jpeg";
        } else {
            $filename = uniqid() . '.' . $ext;
            move_uploaded_file($tmp_name, 'uploads/' . $filename);
            $uploaded_file = $filename;
        }
    } else {
        $errors['lot-img'] = "Загрузите изображение";
    }

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
