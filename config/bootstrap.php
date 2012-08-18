<?php

error_reporting(E_ALL);
ini_set('display_errors', true);

include 'config/datetime.php';

include 'lib/html.php';

require 'lib/tokens.php';
$pdo = include 'config/pdo.php';

require 'lib/facebook.php';
$facebook = include 'config/facebook.php';
