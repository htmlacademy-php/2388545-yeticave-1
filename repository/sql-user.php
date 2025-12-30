<?php

function add_user(mysqli $con, array $form_fields)
{
    $user_email = $form_fields['email'];
    $user_login = $form_fields['name'];
    $user_original_password = $form_fields['password'];
    $user_contact = $form_fields['message'];

    $user_hash_password = password_hash($user_original_password, PASSWORD_DEFAULT);

    $sql_users_insert = <<<SQL
    INSERT INTO users (email, login, password, contact)
    VALUES (?, ?, ?, ?)
    SQL;

    $stmt_lot = db_get_prepare_stmt($con, $sql_users_insert, [$user_email, $user_login, $user_hash_password, $user_contact]);
    mysqli_stmt_execute($stmt_lot);
}
