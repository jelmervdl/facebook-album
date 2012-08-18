<?php

require 'lib/facebook-php-sdk/src/facebook.php';

function fb_is_logged_in()
{
	global $facebook;

	return $facebook->getUser() != 0;
}

function fb_login()
{
	global $facebook;

	$login_url = $facebook->getLoginUrl(array(
		'scope' => 'user_photos'
	));

	printf('<a href="%s">Log in</a>', $login_url);
}

function fb_list_albums()
{
	global $facebook;

	return $facebook->api(array(
		'method' => 'fql.query',
		'query' => 'SELECT aid, name FROM album WHERE owner = me()'
	));
}

function fb_get_album($aid)
{
	global $facebook;

	$albums = $facebook->api(array(
		'method' => 'fql.query',
		'query' => sprintf('SELECT aid, name FROM album WHERE aid = "%s"', $aid)
	));

	return $albums[0];
}

function fb_is_legid_album_id($aid)
{
	foreach (fb_list_albums() as $album)
		if ($album['aid'] == $aid)
			return true;

	return false;
}

function fb_list_photos($aid)
{
	global $facebook;

	return $facebook->api(array(
		'method' => 'fql.query',
		'query' => sprintf('SELECT pid, src_small, src_small_width, src_small_height, src_big, src_big_width, src_big_height, caption FROM photo WHERE aid = "%s"', $aid)
	));
}
