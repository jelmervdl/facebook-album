<?php

require 'config/bootstrap.php';

if (!fb_is_logged_in() || !isset($_POST['token']))
{
	header('Location: index.php');
	exit;
}

$token = Token::getById($pdo, $_POST['token']);

$token->revoke($pdo);

header('Location: index.php');
printf('<a href="index.php">Terug naar lijst met albums</a>');
