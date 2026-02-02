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

    $result_categories = mysqli_query($con, $sql_categories);

    if (!$result_categories) {
        echo "Произошла ошибка MySQL";
        die();
    }

    $categories = mysqli_fetch_all($result_categories, MYSQLI_ASSOC);

    return $categories;
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

    $stmt = db_get_prepare_stmt($con, $sql_category_id, [$slug]);

    mysqli_stmt_execute($stmt);

    $result_category_id = mysqli_stmt_get_result($stmt);

    if (!$result_category_id) {
        echo "Произошла ошибка MySQL";
        die();
    }

    $row = mysqli_fetch_assoc($result_category_id);

    if (!$row) {
        return null;
    }

    return $row['id'];
}
