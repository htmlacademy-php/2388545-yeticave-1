 <?php

/**
 * Генерация шаблона письма победителю
 *
 * @param string $user_login Имя пользователя
 * @param string $lot_url Ссылка на лот
 * @param string $lot_name Название лота
 * @param string $my_rates_url Ссылка на "мои ставки"
 * @return string Шаблон письма победителю
 */
function get_winner_email_template($user_login, $lot_id, $lot_name)
{
    $lot_url = "http://localhost/lot.php?id=" . $lot_id;

    return <<<HTML
    <h1>Поздравляем с победой</h1>
    <p>Здравствуйте, {$user_login}</p>
    <p>Ваша ставка для лота <a href="{$lot_url}">{$lot_name}</a> победила.</p>
    <p>Перейдите по ссылке <a href="http://localhost/pages/my-bets.html">мои ставки</a>,
    чтобы связаться с автором объявления</p>
    <small>Интернет-Аукцион "YetiCave"</small>
    HTML;
}
