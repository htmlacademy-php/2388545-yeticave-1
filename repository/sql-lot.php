<?php

function get_lot(mysqli $con, int $lot_id)
{
    $sql_lot = <<<SQL
        SELECT l.id, l.name as title, l.start_price as price, l.img, l.description, l.end_date as date, c.name as category
        FROM lots l
        JOIN categories c ON l.category_id = c.id
        WHERE l.id = $lot_id
    SQL;

    $result_lot = mysqli_query($con, $sql_lot);

    if (!$result_lot) {
        echo "Произошла ошибка MySQL";
        die();
    }

    $lot = mysqli_fetch_assoc($result_lot);

    return $lot;
}
