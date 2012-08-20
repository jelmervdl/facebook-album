<?php

error_reporting(E_ALL);
ini_set('display_errors', true);

include 'config/datetime.php';

require_once 'lib/html.php';

require_once 'lib/template.php';

require_once 'lib/tokens.php';
$pdo = include 'config/pdo.php';

require_once 'lib/facebook.php';
$facebook = include 'config/facebook.php';
