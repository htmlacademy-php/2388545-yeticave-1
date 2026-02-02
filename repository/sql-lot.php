<?php

require_once('./repository/sql-categories.php');
require_once('./utils/init-session.php');

/**
 * Получает информацию о конкретном лоте по его id
 *
 * @param mysqli $con Объект подключения к базе данных MySQLi
 * @param int $lot_id id лота
 *
 * @return array{
 *     id: int,
 *     title: string,
 *     price: int,
 *     step: int,
 *     img: string,
 *     description: string,
 *     date: string,
 *     category: string
 * }|null Ассоциативный массив с данными лота или null, если лот не найден
 */
function get_lot(mysqli $con, int $lot_id): ?array
{
    $sql_lot = <<<SQL
        SELECT l.id, l.name as title, l.start_price as price, l.step, l.img, l.description, l.end_date as date, c.name as category
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

/**
 * Добавляет новый лот в базу данных
 *
 * @param mysqli $con Объект подключения к базе данных MySQLi
 * @param array $form_fields Данные формы добавления лота
 * @param int $user_id id пользователя, добавляющего лот
 *
 * @return int id созданного лота
 */
function add_lot(mysqli $con, array $form_fields, $user_id): int
{
    $lot_name = $form_fields['lot-name'];
    $lot_description = $form_fields['message'];
    $lot_img = $form_fields['lot-img']['img_path'];
    $lot_start_price = $form_fields['lot-rate'];
    $lot_end_date = $form_fields['lot-date'];
    $lot_step = $form_fields['lot-step'];
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

/**
 * Записывает победителя в лот
 *
 * @param mysqli $con Объект подключения к базе данных MySQLi
 * @param int $lot_id id лота
 * @param int $user_id id победителя
 *
 * @return void
 */
function add_winner_into_lot(mysqli $con, int $lot_id, int $user_id): void
{
    $sql_lots_update = <<<SQL
        UPDATE lots
        SET winner_id = ?
        WHERE id = ?
    SQL;

    $stmt_lot = db_get_prepare_stmt($con, $sql_lots_update, [$user_id, $lot_id]);
    mysqli_stmt_execute($stmt_lot);
}
