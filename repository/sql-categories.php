<?php

function get_categories(mysqli $con)
{
    $sql_categories = <<<SQL
        SELECT name, slug
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
