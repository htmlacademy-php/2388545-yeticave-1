<?php

require_once('./repository/sql-templates.php');

/**
 * Константа, определяющая разделитель для правил
 */
const RULES_DELIMITER = "|";

/**
 * Константа, отделяющая параметры от названия функции
 */
const PARAMETERES_DELIMITER = ":";

/**
 * Константа, отделяющая параметры друг от друга
 */
const PARAMETERES_SPLIT_DELIMITER = ",";

/**
 * Текст ошибок для уникальных полей
 */
const UNIQUE_ERROR_DESCRIPTIONS = [
    'email' => 'Пользователь с таким e-mail уже существует',
];

/**
 * Преобразует правила к массиву правил
 *
 * @param array $rules массив полей и правил ['name' => 'validate|min:10']
 * @return array массив полей и правил, где правила являются массивом ['name' => ['validate', 'min:10']]
 */
function _get_normalized_array(array $rules): array
{
    $out = [];
    foreach ($rules as $field => $rule) {
        if (is_array($rule)) {
            $out[$field] = $rule;
            continue;
        }

        if (!is_string($rule)) {
            die("Правила должны быть определены строкой или массивом");
        }

        $rule_array = explode(RULES_DELIMITER, $rule);
        $out[$field] = $rule_array;
    }

    return $out;
}

/**
 * Создает название функции на основе правила
 *
 * @param string $rule правило
 * @return string название функции
 */
function _get_normalized_rule_name(string $rule): string
{
    return "validate" . '_' . $rule;
}

function validate(array $form_fields, array $rules, mysqli $con): ?array
{
    // Нормализуем массив
    $rules = _get_normalized_array($rules);

    $errors = [];

    //Проверяем, что входные значения полностью удовлетворяют правилам
    foreach ($rules as $field_name => $field_rules) {
        foreach ($field_rules as $rule) {
            $rule_array = explode(PARAMETERES_DELIMITER, $rule);
            $name = array_shift($rule_array);
            $callable = _get_normalized_rule_name($name);
            if (!function_exists($callable)) {
                die("Функция {$callable} не существует");
            }

            $parameteres = [];
            if (count($rule_array) === 1) {
                $parameteres = explode(PARAMETERES_SPLIT_DELIMITER, $rule_array[0]);
            }

            $errorMessage = $callable($field_name, $form_fields[$field_name] ?? null, $form_fields, $con, ...$parameteres);

            if ($errorMessage !== null) {
                $errors[$field_name] = $errorMessage;
                break;
            }
        }
    }

    return empty(array_filter($errors)) ? null : $errors;
}

/**
 * Проверяет заполненность поля
 *
 * @param string $field_name название поля
 * @param mixed $value значение поля
 * @param array $form_fields массив полей формы
 * @param mysqli $con sql connection
 * @param [type] ...$args прочие параметры
 * @return string|null текст ошибки либо null
 */
function validate_required(string $field_name, mixed $value, array $form_fields, mysqli $con, ...$args): ?string
{
    if (empty($value)) {
        return "Поле необходимо заполнить";
    }

    return null;
}

/**
 * Проверяет, что селект был выбран
 *
 * @param string $field_name название поля
 * @param mixed $value значение поля
 * @param array $form_fields массив полей формы
 * @param mysqli $con sql connection
 * @param string $default_value значение по умолчанию
 * @param [type] ...$args прочие параметры
 * @return string|null текст ошибки либо null
 */
function validate_select_not_default(string $field_name, mixed $value, array $form_fields, mysqli $con, string $default_value, ...$args): ?string
{
    if ($value === $default_value) {
        return "Выберите один из вариантов";
    }

    return null;
}

/**
 * Проверяет, что значение является неотрицательным числом
 *
 * @param string $field_name название поля
 * @param mixed $value значение поля
 * @param array $form_fields массив полей формы
 * @param mysqli $con sql connection
 * @param [type] ...$args прочие параметры
 * @return string|null текст ошибки либо null
 */
function validate_positive_number(string $field_name, mixed $value, array $form_fields, mysqli $con, ...$args): ?string
{
    if ($value === null) {
        return null;
    }

    if (!is_numeric($value) || $value <= 0) {
        return "Значение должно быть больше 0";
    }

    return null;
}

/**
 * Проверяет, что значение является целым числом
 *
 * @param string $field_name название поля
 * @param mixed $value значение поля
 * @param array $form_fields массив полей формы
 * @param mysqli $con sql connection
 * @param [type] ...$args прочие параметры
 * @return string|null текст ошибки либо null
 */
function validate_int(string $field_name, mixed $value, array $form_fields, mysqli $con, ...$args): ?string
{
    if ($value === null) {
        return null;
    }

    if (filter_var($value, FILTER_VALIDATE_INT) === false) {
        return "Значение не может быть дробным";
    }

    return null;
}

/**
 * Проверяет, что значение является строкой
 *
 * @param string $field_name название поля
 * @param mixed $value значение поля
 * @param array $form_fields массив полей формы
 * @param mysqli $con sql connection
 * @param [type] ...$args прочие параметры
 * @return string|null текст ошибки либо null
 */
function validate_string(string $field_name, mixed $value, array $form_fields, mysqli $con, ...$args): ?string
{
    if ($value === null) {
        return null;
    }

    if (!is_string($value)) {
        return "Некорректный тип данных";
    }

    return null;
}

/**
 * Проверяет, что дата отправлена в верном формате
 *
 * @param string $field_name название поля
 * @param mixed $value значение поля
 * @param array $form_fields массив полей формы
 * @param mysqli $con sql connection
 * @param [type] ...$args прочие параметры
 * @return string|null текст ошибки либо null
 */
function validate_date_format(string $field_name, mixed $value, array $form_fields, mysqli $con, ...$args): ?string
{
    if ($value === null) {
        return null;
    }

    $is_correct_date_format = is_date_valid($value);

    if ($is_correct_date_format === false) {
        return "Введите дату в указанном формате";
    }

    return null;
}

/**
 * Проверяет, что дата больше текущей минимум на 1 день
 *
 * @param string $field_name название поля
 * @param mixed $value значение поля
 * @param array $form_fields массив полей формы
 * @param mysqli $con sql connection
 * @param [type] ...$args прочие параметры
 * @return string|null текст ошибки либо null
 */
function validate_date_after_tomorrow(string $field_name, mixed $value, array $form_fields, mysqli $con, ...$args): ?string
{
    if ($value === null) {
        return null;
    }

    $input_timestamp = new DateTime($value);
    $tomorrow_timestamp = new DateTime('tomorrow');

    if ($input_timestamp < $tomorrow_timestamp) {
        return "Дата должна быть больше текущей хотя бы на 1 день";
    }

    return null;
}

/**
 * Проверяет наличие изображения
 *
 * @param string $field_name название поля
 * @param mixed $value структура файла с изображением
 * @param array $form_fields массив полей формы
 * @param mysqli $con sql connection
 * @param [type] ...$args прочие параметры
 * @return string|null текст ошибки либо null
 */
function validate_required_img(string $field_name, mixed $uploaded_file, array $form_fields, mysqli $con, ...$args): ?string
{
    if (empty($uploaded_file['name'])) {
        return "Загрузите изображение";
    }

    return null;
}

/**
 * Проверяет формат изображения
 *
 * @param string $field_name название поля
 * @param mixed $uploaded_file структура файла с изображением
 * @param array $form_fields массив полей формы
 * @param mysqli $con sql connection
 * @param [type] ...$args прочие параметры
 * @return string|null текст ошибки либо null
 */
function validate_img_format(string $field_name, mixed $uploaded_file, array $form_fields, mysqli $con, ...$args): ?string
{
    if (empty($uploaded_file['name'])) {
        return null;
    }

    $tmp_name = $uploaded_file['tmp_name'];

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $file_type = finfo_file($finfo, $tmp_name);

    $allowed_mime_types = [];

    foreach ($args as $arg) {
        $allowed_mime_types[] = "image/" . $arg;
    }

    if (empty($allowed_mime_types)) {
        die("Не указаны допустимые форматы изображения");
    }

    if (!in_array($file_type, $allowed_mime_types)) {
        return "Загрузите картинку в формате .png, .jpg или .jpeg";
    }

    return null;
}

/**
 * Проверяет формат email
 *
 * @param string $field_name название поля
 * @param mixed $value значение поля
 * @param array $form_fields массив полей формы
 * @param mysqli $con sql connection
 * @param [type] ...$args прочие параметры
 * @return string|null текст ошибки либо null
 */
function validate_email(string $field_name, mixed $value, array $form_fields, mysqli $con, ...$args): ?string
{
    if ($value === null) {
        return null;
    }

    if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
        return "Некорректный формат email";
    }

    return null;
}

/**
 * Проверяет минимальную длину строки
 *
 * @param string $field_name название поля
 * @param mixed $value значение поля
 * @param array $form_fields массив полей формы
 * @param mysqli $con sql connection
 * @param string $min_length минимальная длина строки
 * @param [type] ...$args прочие параметры
 * @return string|null текст ошибки либо null
 */
function validate_min(string $field_name, mixed $value, array $form_fields, mysqli $con, string $min_length, ...$args): ?string
{
    if ($value === null) {
        return null;
    }

    if (!is_string($value)) {
        return "Некорректный тип данных";
    }

    if (strlen($value) < $min_length) {
        return "Введите минимум $min_length символов";
    }

    return null;
}

/**
 * Проверяет уникальность значения
 *
 * @param string $field_name название поля
 * @param mixed $value значение поля
 * @param array $form_fields массив полей формы
 * @param mysqli $con sql connection
 * @param string $table_name название таблицы, содержащей проверяемое значение
 * @param string $column_name название поля, содержащее проверяемое значение
 * @param [type] ...$args прочие параметры
 * @return string|null текст ошибки либо null
 */
function validate_unique(string $field_name, mixed $value, array $form_fields, mysqli $con, string $table_name, string $column_name, ...$args): ?string
{
    if ($value === null) {
        return null;
    }

    $existing_value = get_existing_data($con, $value, $table_name, $column_name);

    if ($existing_value !== null) {
        if (isset(UNIQUE_ERROR_DESCRIPTIONS[$column_name])) {
            return UNIQUE_ERROR_DESCRIPTIONS[$column_name];
        }

        return "Значение не является уникальным";
    }

    return null;
}
