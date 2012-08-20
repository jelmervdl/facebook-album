<?php

require 'config/bootstrap.php';

if (!$facebook->isLoggedIn())
	echo include_template('views/login.phtml', array(
		'login_url' => $facebook->getLoginUrl(
			array('scope' => 'user_photos'))
	));
else
	echo include_template('views/albums.phtml', array(
		'facebook_albums' => $facebook->listAlbums(),
		'shared_albums' => Token::findByAccessToken($pdo, $facebook->getAccessToken())
	));
