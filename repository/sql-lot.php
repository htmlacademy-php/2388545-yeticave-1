<?php

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
