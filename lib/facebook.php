<?php

require_once 'lib/facebook-php-sdk/src/facebook.php';

class FacebookWrapper extends Facebook
{
	public function isLoggedIn()
	{
		return $this->getUser() != 0;
	}

	public function listAlbums()
	{
		$albums = $this->api(array(
			'method' => 'fql.query',
			'query' => 'SELECT aid, name FROM album WHERE owner = me()'
		));

		foreach ($albums as &$album)
			$album = new FacebookAlbum($this, $album);

		return $albums;
	}

	public function getAlbum($album_id)
	{
		$albums = $this->api(array(
			'method' => 'fql.query',
			'query' => sprintf('SELECT aid, name FROM album WHERE aid = "%s"', $album_id)
		));

		return new FacebookAlbum($this, $albums[0]);
	}

	public function albumExists($album_id)
	{
		foreach ($this->listAlbums() as $album)
			if ($album->id() == $album_id)
				return true;

		return false;
	}

	public function listPhotos($album_id)
	{
		$photos = $this->api(array(
			'method' => 'fql.query',
			'query' => sprintf('SELECT pid, src_small, src_small_width, src_small_height, src_big, src_big_width, src_big_height, caption FROM photo WHERE aid = "%s"', $album_id)
		));

		foreach ($photos as &$photo)
			$photo = new FacebookPhoto($this, $photo);

		return $photos;
	}
}

class FacebookEntity
{
	protected $_api;

	protected $_data;

	public function __construct(FacebookWrapper $api, array $data)
	{
		$this->_api = $api;

		$this->_data = $data;
	}

}

class FacebookAlbum extends FacebookEntity
{
	public function id()
	{
		return $this->_data['aid'];
	}

	public function name()
	{
		return $this->_data['name'];
	}

	public function listPhotos()
	{
		return $this->_api->listPhotos($this->id());
	}
}

class FacebookPhoto extends FacebookEntity
{
	public function id()
	{
		return $this->_data['pid'];
	}

	public function caption()
	{
		return $this->_data['caption'];
	}

	public function small()
	{
		return new FacebookImage(
			$this->_data['src_small'],
			$this->_data['src_small_width'],
			$this->_data['src_small_height']);
	}

	public function large()
	{
		return new FacebookImage(
			$this->_data['src_big'],
			$this->_data['src_big_width'],
			$this->_data['src_big_height']);
	}
}

class FacebookImage
{
	private $_src;

	private $_width;

	private $_height;

	public function __construct($src, $width, $height)
	{
		$this->_src = $src;

		$this->_width = (int) $width;

		$this->_height = (int) $height;
	}

	public function src()
	{
		return $this->_src;
	}

	public function width()
	{
		return $this->_width;
	}

	public function height()
	{
		return $this->_height;
	}
}