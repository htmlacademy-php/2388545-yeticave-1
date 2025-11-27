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
 * Рассчитывает разницу между текущим временем и указанной датой
 *
 * @param string $target_date Дата/время в формате, понимаемом strtotime()
 * @return array Массив [часы, минуты] (могут быть отрицательными)
 */
function calculate_time_difference(string $target_date): array
{
    $current_timestamp = strtotime("now");
    $target_timestamp = strtotime($target_date);

    if ($target_timestamp === false) {
        throw new InvalidArgumentException('Некорректный формат даты');
    }

    $difference = $target_timestamp - $current_timestamp;
    $hours = (int)($difference / 3600);
    $minutes = (int)(($difference % 3600) / 60);

    return [$hours, $minutes];
}

/**
 * Форматирует временной промежуток для отображения
 *
 * @param array $time_diff Массив [часы, минуты] от calculateTimeDifference()
 * @return array Массив из двух строк с ведущими нулями
 */
function format_time_difference(array $time_diff): array
{
    [$hours, $minutes] = $time_diff;

    $formatted_hours = str_pad((string)abs($hours), 2, "0", STR_PAD_LEFT);
    $formatted_minutes = str_pad((string)abs($minutes), 2, "0", STR_PAD_LEFT);

    return [$formatted_hours, $formatted_minutes];
}
