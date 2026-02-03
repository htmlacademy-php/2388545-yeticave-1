<?php

/**
 * Функция для поиска определенного значения в таблице
 *
 * @param mysqli $con
 * @param mixed $value искомое значение
 * @param string $table_name название таблицы
 * @param string $column_name название столбца, содержащего искомое значение
 * @return array|null строка из таблицы либо null
 */
function get_existing_data(mysqli $con, mixed $value, string $table_name, string $column_name): ?array
{
    $safe_table = mysqli_real_escape_string($con, $table_name);
    $safe_column = mysqli_real_escape_string($con, $column_name);

    $sql_existing_data = <<<SQL
        SELECT *
        FROM $safe_table
        WHERE $safe_column = ?
        LIMIT 1
    SQL;

    return get_sql_result_with_params($con, $sql_existing_data, [$value], 'assoc');
}
