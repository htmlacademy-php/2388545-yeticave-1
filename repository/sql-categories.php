<?php

/**
 * Получает список всех категорий лотов
 *
 * @param mysqli $con Объект подключения к базе данных MySQLi
 * @return array<int, array{
 *     id: int,
 *     name: string,
 *     slug: string
 * }>
 */
function get_categories(mysqli $con): array
{
    $sql_categories = <<<SQL
        SELECT id, name, slug
        FROM categories
    SQL;

    return get_sql_result_without_params($con, $sql_categories);
}

/**
 * Находит id категории по её slug
 *
 * @param mysqli $con Объект подключения к базе данных MySQLi
 * @param string $slug идентификатор категории
 *
 * @return int|null id категории или null, если категория не найдена
 */
function find_category_by_slug(mysqli $con, string $slug): ?int
{
    $sql_category_id = <<<SQL
        SELECT id
        FROM categories
        WHERE slug = ?
    SQL;

    $row = get_sql_result_with_params($con, $sql_category_id, [$slug], 'assoc');

    return $row ? (int)$row['id'] : null;
}
