<?php

require_once('./repository/sql-categories.php');

function get_lot(mysqli $con, int $lot_id)
{
    $sql_lot = <<<SQL
        SELECT l.id, l.name as title, l.start_price as price, l.img, l.description, l.end_date as date, c.name as category
        FROM lots l
        JOIN categories c ON l.category_id = c.id
        WHERE l.id = ?
    SQL;

    $stmt = db_get_prepare_stmt($con, $sql_lot, [$lot_id]);
    mysqli_stmt_execute($stmt);

    $result_lot = mysqli_stmt_get_result($stmt);

    if (!$result_lot) {
        echo "Произошла ошибка MySQL";
        die();
    }

    $lot = mysqli_fetch_assoc($result_lot);

    return $lot;
}

function add_lot(mysqli $con, array $form_fields)
{
    var_dump($form_fields['lot-img']);

    $lot_name = $form_fields['lot-name'];
    $lot_description = $form_fields['message'];
    $lot_img = $form_fields['lot-img']['img_path'];
    $lot_start_price = $form_fields['lot-rate'];
    $lot_end_date = $form_fields['lot-date'];
    $lot_step = $form_fields['lot-step'];
    $user_id = 1;
    $category_id = find_category_by_slug($con, $form_fields['category']);

    $sql_lots_insert = <<<SQL
        INSERT INTO lots (name, description, img, start_price, end_date, step, user_id, category_id)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    SQL;

    $stmt_lot = db_get_prepare_stmt($con, $sql_lots_insert, [$lot_name, $lot_description, $lot_img, $lot_start_price, $lot_end_date, $lot_step, $user_id, $category_id]);
    mysqli_stmt_execute($stmt_lot);

    $new_lot_id = mysqli_insert_id($con);

    return $new_lot_id;
}
