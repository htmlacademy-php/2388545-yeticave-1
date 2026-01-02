<?php

require_once('./utils/init-session.php');

if (isset($_SESSION['username'])) {
    unset($_SESSION['username']);
};

header("Location: /");
exit();
