<?php

require 'config/bootstrap.php';

if (!fb_is_logged_in() || !isset($_POST['album_id']) || !fb_is_legid_album_id($_POST['album_id']))
{
	header('Location: index.php');
	exit;
}

$token = Token::create($facebook->getAccessToken(), $_POST['album_id']);

header('Location: ' . $token->url());
printf('<a href="%s">%1$s</a>', $token->url());
