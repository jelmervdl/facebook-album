<?php

require 'config/bootstrap.php';

if (!$facebook->isLoggedIn() || !isset($_POST['album_id']) || !$facebook->albumExists($_POST['album_id']))
{
	header('Location: index.php');
	exit;
}

$token = Token::create($facebook->getAccessToken(), $_POST['album_id']);

$token->insert($pdo);

header('Location: ' . $token->url());
printf('<a href="%s">%1$s</a>', $token->url());
