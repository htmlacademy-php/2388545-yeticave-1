<?php

function get_all_rates(mysqli $con, int $lot_id)
{
    $sql_rates = <<<SQL
        SELECT r.id, r.date, r.cost, u.login
        FROM rates r
        JOIN users u ON r.user_id = u.id
        WHERE r.lot_id = ?
        ORDER BY r.date DESC
    SQL;

    $stmt_rates = db_get_prepare_stmt($con, $sql_rates, [$lot_id]);
    mysqli_stmt_execute($stmt_rates);

    $result_rates = mysqli_stmt_get_result($stmt_rates);

    if (!$result_rates) {
        echo "Произошла ошибка MySQL";
        die();
    }

    $all_rates = mysqli_fetch_all($result_rates, MYSQLI_ASSOC);

    return $all_rates;
}

function get_last_rate(mysqli $con, int $lot_id)
{
    $sql_rates = <<<SQL
        SELECT r.id, r.lot_id, r.cost, r.user_id, u.email, u.login
        FROM rates r
        JOIN users u ON r.user_id = u.id
        WHERE r.lot_id = ?
        ORDER BY r.date DESC
        LIMIT 1
    SQL;

    $stmt_rate = db_get_prepare_stmt($con, $sql_rates, [$lot_id]);
    mysqli_stmt_execute($stmt_rate);

    $result_rate = mysqli_stmt_get_result($stmt_rate);

    if (!$result_rate) {
        echo "Произошла ошибка MySQL";
        die();
    }

    $last_rate = mysqli_fetch_assoc($result_rate);

    return $last_rate;
}

function add_rate(mysqli $con, array $form_fields, $user_id, $lot_id)
{
    $cost = $form_fields['cost'];

    $sql_rates_insert = <<<SQL
        INSERT INTO rates (cost, user_id, lot_id)
        VALUES (?, ?, ?)
    SQL;

    $stmt_rate = db_get_prepare_stmt($con, $sql_rates_insert, [$cost, $user_id, $lot_id]);
    mysqli_stmt_execute($stmt_rate);
}
