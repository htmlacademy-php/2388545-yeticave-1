<?php

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

require_once('./utils/db.php');
require_once('./email.php');
require_once('./repository/sql-lots.php');
require_once('./repository/sql-lot.php');
require_once('./repository/sql-rates.php');

require 'vendor/autoload.php';

// поиск лотов с истекшим сроком

$lots = get_expired_lots($con);

foreach ($lots as $lot) {
    $last_rate_info = get_last_rate($con, $lot['id']);
    if ($last_rate_info === NULL) continue;

    // запись победителя в БД

    add_winner_into_lot($con, $lot['id'], $last_rate_info['user_id']);

    // отправка письма победителю

    $email_content = get_winner_email_template($last_rate_info['login'], $lot['id'], $lot['name']);

    var_dump($email_content);

    // Конфигурация траспорта
    $dsn = 'smtp://299d0f68880f14:0ee196ce2eceb0@sandbox.smtp.mailtrap.io:2525';
    $transport = Transport::fromDsn($dsn);

    // Формирование сообщения
    $message = new Email();
    $message->to($last_rate_info['email']);
    $message->from("keks@phpdemo.ru");
    $message->subject("Ваша ставка победила");
    $message->html($email_content);

    // Отправка сообщения
    $mailer = new Mailer($transport);
    $mailer->send($message);
}
