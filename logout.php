<?php

require_once('./utils/init-session.php');

session_destroy();

header("Location: /");
exit();
