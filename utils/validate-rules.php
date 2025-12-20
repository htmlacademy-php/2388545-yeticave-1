<?php

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
    $is_rate_correct = filter_var($value, FILTER_VALIDATE_INT);

    if ($is_rate_correct === false) {
        return "Значение не может быть дробным";
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
    $input_timestamp = new DateTime($value);
    $tomorrow_timestamp = new DateTime('tomorrow');

    if ($input_timestamp < $tomorrow_timestamp) {
        return "Дата должна быть больше текущей хотя бы на 1 день";
    }

    return null;
}
