<?php

function get_lots(mysqli $con)
{
    $sql_lots = <<<SQL
        SELECT l.name as title, l.start_price as price, l.img, l.end_date as date, c.name as category, l.start_date
        FROM lots l
        JOIN categories c ON l.category_id = c.id
        WHERE l.end_date > NOW()
        ORDER BY l.start_date DESC
        LIMIT 6
    SQL;

    $result_lots = mysqli_query($con, $sql_lots);

    if (!$result_lots) {
        echo "Произошла ошибка MySQL";
        die();
    }

    $lots = mysqli_fetch_all($result_lots, MYSQLI_ASSOC);

    return $lots;
}
