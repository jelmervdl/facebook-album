<?php

require 'config/bootstrap.php';

if (!$facebook->isLoggedIn() || !isset($_POST['token']))
{
	header('Location: index.php');
	exit;
}

$token = Token::getById($pdo, $_POST['token']);

$token->revoke($pdo);

header('Location: index.php');
printf('<a href="index.php">Terug naar lijst met albums</a>');
