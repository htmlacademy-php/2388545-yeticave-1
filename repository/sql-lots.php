<?php

/**
 * Возвращает 6 последних активных лотов для главной страницы
 *
 * @param mysqli $con Объект подключения к базе данных MySQLi
 *
 * @return array<int, array{
 *     id: int,
 *     title: string,
 *     price: int|float,
 *     img: string,
 *     date: string,
 *     category: string,
 *     start_date: string
 * }>
 */
function get_lots(mysqli $con): array
{
    $sql_lots = <<<SQL
        SELECT l.id, l.name as title, l.start_price as price, l.img, l.end_date as date, c.name as category, l.start_date
        FROM lots l
        JOIN categories c ON l.category_id = c.id
        WHERE l.end_date > NOW()
        ORDER BY l.start_date DESC
        LIMIT 6
    SQL;

    return get_sql_result_without_params($con, $sql_lots);
}

/**
 * Получает список лотов с истекшим сроком, победитель которых ещё не определён
 *
 * @param mysqli $con Объект подключения к базе данных MySQLi
 *
 * @return array<int, array{
 *     id: int,
 *     name: string
 * }> Массив лотов
 */
function get_expired_lots(mysqli $con): array
{
    $sql_lots = <<<SQL
        SELECT id, name
        FROM lots
        WHERE end_date <= NOW() AND winner_id is NULL
    SQL;

    return get_sql_result_without_params($con, $sql_lots);
}

/**
 * Получает количество активных лотов в указанной категории
 *
 * @param mysqli $con Объект подключения к базе данных MySQLi
 * @param int $category_id id категории
 *
 * @return int Количество активных лотов в категории
 */
function get_count_lots_by_category(mysqli $con, int $category_id): int
{
    $sql_lots = <<<SQL
        SELECT COUNT(*) as total
        FROM lots l
        JOIN categories c ON l.category_id = c.id
        WHERE l.end_date > NOW() AND c.id = ?
    SQL;

    $row = get_sql_result_with_params($con, $sql_lots, [$category_id], 'assoc');

    return $row ? (int) $row['total'] : 0;
}

/**
 * Получает часть активных лотов указанной категории (для пагинации)
 *
 * @param mysqli $con Объект подключения к базе данных MySQLi
 * @param int $category_id id категории
 * @param int $per_page Количество лотов на странице
 * @param int $current_page Номер текущей страницы
 *
 * @return array<int, array{
 *     id: int,
 *     title: string,
 *     price: int,
 *     img: string,
 *     date: string,
 *     category: string,
 *     start_date: string
 * }> Массив лотов категории
 */
function get_lots_by_category(mysqli $con, int $category_id, int $per_page, int $current_page): array
{
    $offset = ($current_page - 1) * $per_page;

    $sql_lots = <<<SQL
        SELECT l.id, l.name as title, l.start_price as price, l.img, l.end_date as date, c.name as category, l.start_date
        FROM lots l
        JOIN categories c ON l.category_id = c.id
        WHERE l.end_date > NOW() AND c.id = ?
        ORDER BY l.start_date DESC
        LIMIT ?
        OFFSET ?
    SQL;

    return get_sql_result_with_params($con, $sql_lots, [$category_id, $per_page, $offset], 'all');
}

/**
 * Получает количество активных лотов по результатам полнотекстового поиска
 *
 * @param mysqli $con Объект подключения к базе данных MySQLi
 * @param string $search_string Поисковый запрос
 *
 * @return int Количество найденных активных лотов
 */
function get_count_search_lots(mysqli $con, string $search_string): int
{
    $sql_lots = <<<SQL
        SELECT COUNT(*) as total
        FROM lots l
        JOIN categories c ON l.category_id = c.id
        WHERE l.end_date > NOW()
        AND MATCH(l.name,l.description) AGAINST(?)
    SQL;

    $row = get_sql_result_with_params($con, $sql_lots, [$search_string], 'assoc');

    return $row ? (int) $row['total'] : 0;
}

/**
 * Находит часть активных лотов по результатам полнотекстового поиска (для пагинации)
 *
 * @param mysqli $con Объект подключения к базе данных MySQLi
 * @param string $search_string Поисковый запрос
 * @param int $per_page Количество лотов на странице
 * @param int $current_page Номер текущей страницы
 *
 * @return array<int, array{
 *     id: int,
 *     title: string,
 *     price: int,
 *     img: string,
 *     date: string,
 *     category: string,
 *     start_date: string
 * }> Массив найденных лотов
 */
function search_lots(mysqli $con, string $search_string, int $per_page, int $current_page): array
{
    $offset = ($current_page - 1) * $per_page;

    $sql_lots = <<<SQL
        SELECT l.id, l.name as title, l.start_price as price, l.img, l.end_date as date, c.name as category, l.start_date
        FROM lots l
        JOIN categories c ON l.category_id = c.id
        WHERE l.end_date > NOW()
        AND MATCH(l.name,l.description) AGAINST(?)
        ORDER BY l.start_date DESC
        LIMIT ?
        OFFSET ?
    SQL;

    return get_sql_result_with_params($con, $sql_lots, [$search_string, $per_page, $offset], 'all');
}
