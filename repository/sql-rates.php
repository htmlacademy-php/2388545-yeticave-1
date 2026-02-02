<?php

/**
 * Получает все ставки для указанного лота
 *
 * @param mysqli $con Объект подключения к базе данных MySQLi
 * @param int $lot_id id лота
 *
 * @return array<int, array{
 *     id: int,
 *     date: string,
 *     cost: int,
 *     login: string
 * }> Массив ставок
 */
function get_all_rates(mysqli $con, int $lot_id): array
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

/**
 * Получает последнюю ставку для указанного лота
 *
 * @param mysqli $con Объект подключения к базе данных MySQLi
 * @param int $lot_id id лота
 *
 * @return array{
 *     id: int,
 *     lot_id: int,
 *     cost: int,
 *     user_id: int,
 *     email: string,
 *     login: string
 * }|null Последняя ставка или null, если ставок нет
 */
function get_last_rate(mysqli $con, int $lot_id): ?array
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

/**
 * Записывает новую ставку на лот в БД
 *
 * @param mysqli $con Объект подключения к базе данных MySQLi
 * @param $form_fields Данные формы ставки
 * @param int $user_id id пользователя, делающего ставку
 * @param int $lot_id id лота
 *
 * @return void
 */
function add_rate(mysqli $con, array $form_fields, $user_id, $lot_id): void
{
    $cost = $form_fields['cost'];

    $sql_rates_insert = <<<SQL
        INSERT INTO rates (cost, user_id, lot_id)
        VALUES (?, ?, ?)
    SQL;

    $stmt_rate = db_get_prepare_stmt($con, $sql_rates_insert, [$cost, $user_id, $lot_id]);
    mysqli_stmt_execute($stmt_rate);
}

/**
 * Получает последние ставки пользователя по каждому лоту
 *
 * @param mysqli $con Объект подключения к базе данных MySQLi
 * @param int $user_id id пользователя
 *
 * @return array<int, array{
 *     lot_id: int,
 *     lot_name: string,
 *     lot_end_date: string,
 *     winner_id: int|null,
 *     img: string,
 *     contact: string,
 *     category: string,
 *     cost: int,
 *     rate_date: string
 * }> Массив последних ставок пользователя, сгруппированных по лотам
 */
function get_all_rates_by_user(mysqli $con, int $user_id): array
{
    $sql_rates = <<<SQL
        SELECT
            l.id as lot_id,
            l.name as lot_name,
            l.end_date as lot_end_date,
            l.winner_id,
            l.img,
            u.contact,
            c.name as category,
            r.cost,
            r.date as rate_date
        FROM (
            SELECT
                lot_id,
                MAX(date) as max_date
            FROM rates
            WHERE user_id = ?
            GROUP BY lot_id
        ) last_r
        JOIN rates r ON r.lot_id = last_r.lot_id
            AND r.date = last_r.max_date
            AND r.user_id = ?
        JOIN lots l ON r.lot_id = l.id
        JOIN users u ON l.user_id = u.id
        JOIN categories c ON l.category_id = c.id
        ORDER BY r.date DESC
    SQL;

    $stmt_rates = db_get_prepare_stmt($con, $sql_rates, [$user_id, $user_id]);
    mysqli_stmt_execute($stmt_rates);

    $result_rates = mysqli_stmt_get_result($stmt_rates);

    if (!$result_rates) {
        echo "Произошла ошибка MySQL";
        die();
    }

    $all_rates = mysqli_fetch_all($result_rates, MYSQLI_ASSOC);

    return $all_rates;
}
