<?php

$db = [
    'host' => '127.0.0.1',
    'user' => 'root',
    'password' => '',
    'database' => 'yeticave',
];

$con = mysqli_connect($db['host'], $db['user'], $db['password'], $db['database']);
mysqli_set_charset($con, charset:"utf8mb4");
