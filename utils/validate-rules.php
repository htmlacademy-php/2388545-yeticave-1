<?php

function getPostVal($name)
{
    return htmlspecialchars($_POST[$name] ?? "");
}

function validateCategory($option)
{
    if ($option === 'default') {
        return "Выберите категорию";
    }
}

function validatePrice($value)
{
    if (!is_numeric($value) || $value <= 0) {
        return "Цена должна быть больше 0";
    }
}

function validateRate($value)
{
    if (!is_numeric($value) || $value <= 0) {
        return "Ставка должна быть больше 0";
    }

    $is_rate_correct = filter_var($value, FILTER_VALIDATE_INT);

    if ($is_rate_correct === false) {
        return "Ставка не может быть дробной";
    }
}

function validateDate($date)
{
    $is_correct_date_format = is_date_valid($date);

    if ($is_correct_date_format === false) {
        return "Введите дату в указанном формате";
    }

    $input_timestamp = new DateTime($date);
    $tomorrow_timestamp = new DateTime('tomorrow');

    if ($input_timestamp < $tomorrow_timestamp) {
        return "Дата должна быть больше текущей хотя бы на 1 день";
    }
}
