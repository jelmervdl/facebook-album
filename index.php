<?php

require 'config/bootstrap.php';

if (!fb_is_logged_in())
	fb_login();
else
{
	$facebook_albums = fb_list_albums();

	$existing_tokens = Token::findByAccessToken($pdo, $facebook->getAccessToken());

	printf('<a href="%s">Log out</a>', $facebook->getLogoutUrl());

	echo '<ul>';

	foreach ($facebook_albums as $album)
		if (isset($existing_tokens[$album['aid']]))
			printf('
				<li>
					<h3><a href="%2$s">%s</a></h3>
					<input type="text" value="%s" readonly>
					<form method="post" action="revoke-album.php">
						<input type="hidden" name="token" value="%s">
						<button type="submit">Stop met delen</button>
					</form>
				</li>',
				_html($album['name']),
				$existing_tokens[$album['aid']]->url(),
				_attr($existing_tokens[$album['aid']]->id()));
		else
			printf('
				<li>
					<h3>%s</h3>
					<form method="post" action="create-album.php">
						<input type="hidden" name="album_id" value="%s">
						<button type="submit">Deel</button>
					</form>
				</li>',
				_html($album['name']),
				_attr($album['aid']));

	echo '</ul>';
}
?>
<script>
Array.prototype.forEach.call(
	document.getElementsByTagName('input'),
	function(input) {
		if (input.type == 'text')
			input.onclick = function(e) {
				e.preventDefault();
				this.select();
			};
	});
</script>