<?php

function get_winners(mysqli $con)
{
    $sql_winners = <<<SQL
        SELECT l.id, l.name as title, l.start_price as price, l.img, l.end_date as date, c.name as category, l.start_date
        FROM rates r
        JOIN lots l ON l.id = r.lot_id
        WHERE l.end_date > NOW()
        ORDER BY l.start_date DESC
        LIMIT 6
    SQL;

    $result_winners = mysqli_query($con, $sql_winners);

    if (!$result_winners) {
        echo "Произошла ошибка MySQL";
        die();
    }

    $winners = mysqli_fetch_all($result_winners, MYSQLI_ASSOC);

    return $winners;
}
