<?php

/**
 * @var mysqli $con
 */

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

require_once('./utils/db.php');
require_once('./config.php');
require_once('./utils/helpers.php');
require_once('./utils/functions.php');
require_once('./repository/sql-lots.php');
require_once('./repository/sql-lot.php');
require_once('./repository/sql-rates.php');

require 'vendor/autoload.php';

// поиск лотов с истекшим сроком

$lots = get_expired_lots($con);

if (empty($lots)) {
    return;
}

foreach ($lots as $lot) {
    $last_rate_info = get_last_rate($con, $lot['id']);

    if ($last_rate_info === null) {
        continue;
    }

    // запись победителя в БД

    add_winner_into_lot($con, $lot['id'], $last_rate_info['user_id']);

    // отправка письма победителю

    $email_content = include_template('email.php', [
        'user_login' => $last_rate_info['login'],
        'lot_id' => $lot['id'],
        'lot_name' => $lot['name'],
        'app_url' => APP_URL,
    ]);

    // Конфигурация траспорта
    $dsn = MAILER_DSN;
    $transport = Transport::fromDsn($dsn);

    // Формирование сообщения
    $message = new Email();
    $message->to($last_rate_info['email']);
    $message->from(MAIL_FROM);
    $message->subject("Ваша ставка победила");
    $message->html($email_content);

    // Отправка сообщения
    $mailer = new Mailer($transport);
    $mailer->send($message);
}
