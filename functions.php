<?php

/**
 * Форматирует цену с делением на разряды и добавлением знака рубля.
 *
 * @param int $price Цена в виде числа
 * @return string Отформатированная цена
 */
function format_price(int $price)
{
    return number_format($price, 0, ',', ' ') . ' ₽';
}

/**
 * Рассчитывает временной промежуток между двумя датами
 *
 * @param string $date Дата в фиде строки
 * @return array Массив из двух строк, [0] - часы, [1] - минуты
 */
function get_dt_range(string $date)
{
    date_default_timezone_set('Europe/Moscow');

    $cur_date = date_create("now");
    $end_date = date_create($date);
    $diff = date_diff($cur_date, $end_date);

    $total_days = date_interval_format($diff, "%a");
    $hours = date_interval_format($diff, "%h");
    $minutes = date_interval_format($diff, "%i");

    $total_hours = ($total_days * 24) + $hours;

    $formatted_hours = str_pad($total_hours, 2, "0", STR_PAD_LEFT);
    $formatted_minutes = str_pad($minutes, 2, "0", STR_PAD_LEFT);

    return [$formatted_hours, $formatted_minutes];
}

/**
 * Рассчитывает временной промежуток между двумя датами
 *
 * @param string $date Дата в фиде строки
 * @return array Массив из двух строк, [0] - часы, [1] - минуты
 */
function get_date_range(string $date)
{
    date_default_timezone_set('Europe/Moscow');

    $cur_date = strtotime("now");
    $end_date = strtotime($date);
    $diff = $end_date - $cur_date;

    $hours = floor($diff / 3600);
    $minutes = floor(($diff - $hours * 3600) / 60);

    $formatted_hours = str_pad($hours, 2, "0", STR_PAD_LEFT);
    $formatted_minutes = str_pad($minutes, 2, "0", STR_PAD_LEFT);

    return [$formatted_hours, $formatted_minutes];
}
