<?php

/**
 * Форматирует цену с делением на разряды и добавлением знака рубля.
 *
 * @param int $price Цена в виде числа
 * @return string Отформатированная цена
 */
function format_price(int $price): string
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

/**
 * Отображает страницу 404 Not Found
 *
 * Устанавливает HTTP-статус 404 и выводит HTML-страницу с сообщением об ошибке.
 *
 * @param array $categories Массив категорий для меню навигации
 * @param bool $is_auth Флаг авторизации пользователя
 * @param string $user_name Имя авторизованного пользователя (если есть)
 * @return void Функция не возвращает значение
 */
function show_404(array $categories, bool $is_auth, string $user_name = ''): void
{
    http_response_code(404);

    $page_content = include_template('404-main.php', [
        'categories' => $categories,
    ]);

    $layout_content = include_template('layout.php', [
        'content' => $page_content,
        'title' => '404 - Страница не найдена',
        'is_auth' => $is_auth,
        'user_name' => $user_name,
        'categories' => $categories,
    ]);

    print($layout_content);
}

/**
 * Возвращает отформатированный промежуток от переданной прошедшей даты до текущего времени
 *
 * @param string $date прошедшая дата
 * @return string отформатированный промужеток времени
 */
function calculate_past_date(string $date): string
{
    $time_left = calculate_time_difference($date);
    $hours = abs($time_left[0]);
    $minutes = abs($time_left[1]);

    if ($hours < 1) {
        if ($minutes < 1) {
            return "менее минуты назад";
        }
        return "{$minutes} " . get_noun_plural_form($minutes, 'минута', 'минуты', 'минут') . " назад";
    }

    if ($hours < 24) {
        return "{$hours} " . get_noun_plural_form($hours, 'час', 'часа', 'часов') . " назад";
    }

    $timestamp_date = strtotime($date);
    return date('d.m.y \в H:i', $timestamp_date);
}

/**
 * Вычисляет количество страниц для пагинации
 *
 * @param int $lots_count Общее количество лотов
 * @param int $per_page Количество лотов на странице
 *
 * @return int Количество страниц
 * @throws RuntimeException Если количество лотов на странице отрицательное
 */
function get_count_pages(int $lots_count, int $per_page): int
{
    if ($per_page < 0) {
        echo "Количество позиций на странице не может быть отрицательным";
        die();
    }

    return ceil($lots_count / $per_page);
}

/**
 * Выполняет SQL-запрос без входящих параметров и возвращает все строки результата
 *
 * @param mysqli $con Объект подключения к базе данных MySQLi
 * @param string $sql SQL-запрос для выполнения
 *
 * @return array<int, array<string, mixed>> Все строки результата запроса в виде массива ассоциативных массивов
 */
function get_sql_result_without_params(mysqli $con, string $sql): array
{
    $result = mysqli_query($con, $sql);

    if (!$result) {
        echo "Произошла ошибка MySQL";
        die();
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Выполняет параметризованный SQL-запрос и возвращает результат в указанном формате
 *
 * @param mysqli $con Объект подключения к базе данных MySQLi
 * @param string $sql SQL-запрос с плейсхолдерами (?)
 * @param array $params Параметры для подстановки в запрос
 * @param string $data_fetch_method Метод получения данных: 'assoc' для одной строки, иначе все строки
 *
 * @return array<string, mixed>|array<int, array<string, mixed>> Одна строка в виде ассоциативного массива или массив всех строк, или null если ничего не найдено
 */
function get_sql_result_with_params(mysqli $con, string $sql, array $params, string $data_fetch_method): ?array
{
    $stmt = db_get_prepare_stmt($con, $sql, $params);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        echo "Произошла ошибка MySQL";
        die();
    }

    if ($data_fetch_method === 'assoc') {
        return mysqli_fetch_assoc($result);
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
