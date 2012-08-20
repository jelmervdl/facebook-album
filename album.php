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
		<script>
			var overlay = document.createElement('figure');
			overlay.id = 'overlay';

			var large = document.createElement('img');
			overlay.appendChild(large);

			var caption = document.createElement('figcaption');
			overlay.appendChild(caption);

			var next = document.createElement('button');
			next.id = 'next-button';
			next.appendChild(document.createTextNode('Volgende'));
			overlay.appendChild(next);

			var prev = document.createElement('button');
			prev.id = 'prev-button';
			prev.appendChild(document.createTextNode('Vorige'));
			overlay.appendChild(prev);

			document.body.appendChild(overlay);

			overlay.addEventListener('click', function(e) {
				e.preventDefault();

				overlay.classList.remove('visible');
			});

			var currentPhoto = null;

			next.addEventListener('click', function(e) {
				e.stopPropagation();

				var nextPhoto = findPhoto(currentPhoto, +1);

				if (nextPhoto)
					showPhoto(nextPhoto);
			});

			prev.addEventListener('click', function(e) {
				e.stopPropagation();

				var prevPhoto = findPhoto(currentPhoto, -1);

				if (prevPhoto)
					showPhoto(prevPhoto);
			});

			var findPhoto = function(photo, offset)
			{
				var photos = document.querySelectorAll('.photo a');

				for (var i = 0; i < photos.length; ++i)
					if (photos[i] == photo)
						return photos[i + offset] || null;

				return null;
			}

			var showPhoto = function(photo)
			{
				// Keep track of which photo is now shown for next & prev links.
				currentPhoto = photo;

				// Set large image to the link target.
				large.width = photo.getAttribute('data-width');
				large.height = photo.getAttribute('data-height');
				large.src = photo.href;

				// Also set caption.
				caption.innerHTML = photo.querySelector('figcaption').innerHTML;

				// Show large image.
				overlay.classList.add('visible');

				// Preload next 2 photo's.
				for (var i = 1; i <= 2; ++i)
				{
					var nextPhoto = findPhoto(photo, i);
				
					if (!nextPhoto) break;

					preloadPhoto(nextPhoto);
				}
			}

			var preloadPhoto = function(photo)
			{
				var img = new Image();
				img.src = photo.href;
			}

			Array.prototype.forEach.call(
				document.querySelectorAll('.photo a'),
				function(photo) {
					photo.addEventListener('click', function(e) {
						e.preventDefault();

						showPhoto(photo);
					});
				}
			);
		</script>
	</body>
</html>