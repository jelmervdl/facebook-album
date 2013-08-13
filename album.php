<?php

require 'config/bootstrap.php';

$token = Token::getById($pdo, $_GET['token']);

if (!$token || $token->isRevoked())
{
	header('Status: 404 Not Found');
	echo "Dit album wordt niet (langer) gedeeld.";
	exit;
}

$facebook->setAccessToken($token->accessToken());

$album = $facebook->getAlbum($token->albumId());

$photos = $album->listPhotos();

?>
<!DOCTYPE html>
<html>
	<head>
		<title><?=_html($album->name())?></title>
		<link rel="stylesheet" href="album.css">
	</head>
	<body>
		<h1><?=_html($album->name())?></h1>
		<ol>
		<?php foreach ($photos as $photo): ?>
		<?php printf('
			<li class="photo">
				<a href="%s" data-width="%d" data-height="%d">
					<figure>
						<img src="%s" width="%d" height="%d">
						<figcaption>%s</figcation>
					</figure>
				</a>
			</li>',
				_attr($photo->large()->src()), $photo->large()->width(), $photo->large()->height(),
				_attr($photo->small()->src()), $photo->small()->width(), $photo->small()->height(),
				_html($photo->caption())); ?>
		<?php endforeach ?>
		</ol>
		
		<figure id="overlay">
			<img class="background">
			<img class="full-size">
			<figcaption id="caption"></figcaption>
			<button class="next-button">Next</button>
			<button class="prev-button">Prev</button>
		</figure>

		<script src="js/app.js"></script>
		<script>app_init()</script>
	</body>
</html>