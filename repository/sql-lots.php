<?php

/**
 * Возвращает 6 последних активных лотов для главной страницы
 *
 * @param mysqli $con Объект подключения к базе данных MySQLi
 *
 * @return array Массив лотов в формате ассоциативных массивов.
 *               Каждый лот содержит поля:
 *               - id (int) - идентификатор лота
 *               - title (string) - название лота
 *               - price (int|float) - начальная цена
 *               - img (string) - путь к изображению
 *               - date (string) - дата окончания торгов
 *               - category (string) - название категории
 *               - start_date (string) - дата создания лота
 */
function get_lots(mysqli $con)
{
    $sql_lots = <<<SQL
        SELECT l.id, l.name as title, l.start_price as price, l.img, l.end_date as date, c.name as category, l.start_date
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

function search_lots(mysqli $con, string $search_string)
{
    $sql_lots = <<<SQL
        SELECT l.id, l.name as title, l.start_price as price, l.img, l.end_date as date, c.name as category, l.start_date
        FROM lots l
        JOIN categories c ON l.category_id = c.id
        WHERE l.end_date > NOW()
        AND MATCH(l.name,l.description) AGAINST(?)
        ORDER BY l.start_date DESC
        LIMIT 6
    SQL;

    $stmt_lots = db_get_prepare_stmt($con, $sql_lots, [$search_string]);
    mysqli_stmt_execute($stmt_lots);

    $result_lots = mysqli_stmt_get_result($stmt_lots);

    if (!$result_lots) {
        echo "Произошла ошибка MySQL";
        die();
    }

    $found_lots = mysqli_fetch_all($result_lots, MYSQLI_ASSOC);

    return $found_lots;
}

function get_expired_lots(mysqli $con)
{
    $sql_lots = <<<SQL
        SELECT id, name
        FROM lots
        WHERE end_date <= NOW() AND winner_id is NULL
    SQL;

    $result_lots = mysqli_query($con, $sql_lots);

    if (!$result_lots) {
        echo "Произошла ошибка MySQL";
        die();
    }

    $lots = mysqli_fetch_all($result_lots, MYSQLI_ASSOC);

    return $lots;
}
