<?php

$pdo = require 'config/pdo.php';

require_once 'config/datetime.php';

class Token
{
	static public function create($access_token, $album_id)
	{
		return new self(array(
			'token_id' => sha1($access_token . uniqid()),
			'access_token' => $access_token,
			'album_id' => $album_id,
			'created_on' => date('Y-m-d H:i:s'),
			'revoked_on' => null
		));
	}

	static public function getById(PDO $pdo, $token_id)
	{
		$stmt = $pdo->prepare('
			SELECT
				token_id, access_token, album_id, created_on, revoked_on
			FROM
				tokens
			WHERE
				token_id = :token_id');

		$stmt->execute(array('token_id' => $token_id));

		$data = $stmt->fetch(PDO::FETCH_ASSOC);

		return $data ? new self($data) : null;
	}

	static public function findByAccessToken(PDO $pdo, $access_token)
	{
		$stmt = $pdo->prepare('
			SELECT
				token_id, access_token, album_id, created_on, revoked_on
			FROM
				tokens
			WHERE
				access_token = :access_token
				AND revoked_on IS NULL');

		$stmt->execute(array('access_token' => $access_token));

		$tokens = array();

		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
			$tokens[$row['album_id']] = new self($row);

		return $tokens;
	}

	private $id;

	private $access_token;

	private $album_id;

	private $created_on;

	private $revoked_on;

	protected function __construct(array $data)
	{
		$this->id = $data['token_id'];

		$this->access_token = $data['access_token'];

		$this->album_id = $data['album_id'];

		$this->created_on = $data['created_on'];

		$this->revoked_on = $data['revoked_on'];
	}

	public function id()
	{
		return $this->id;
	}

	public function accessToken()
	{
		return $this->access_token;
	}

	public function albumId()
	{
		return $this->album_id;
	}

	public function createdOn()
	{
		return new DateTime($this->created_on);
	}

	public function revokedOn()
	{
		return !empty($this->revoked_on)
			? new DateTime($this->revoked_on)
			: null;
	}

	public function isRevoked()
	{
		return !empty($this->revoked_on);
	}

	public function url()
	{
		return 'album.php?token=' . $this->id();
	}

	public function insert(PDO $pdo)
	{
		return $pdo
			->prepare("
				INSERT INTO tokens
					(token_id, access_token, album_id, created_on)
					VALUES (:token_id, :access_token, :album_id, :created_on)")
			->execute(array(
				'token_id' => $this->id,
				'access_token' => $this->access_token,
				'album_id' => $this->album_id,
				'created_on' => $this->created_on
			));
	}

	public function revoke(PDO $pdo)
	{
		$this->revoked_on = date('Y-m-d H:i:s');

		return $pdo->prepare("
				UPDATE
					tokens
				SET
					revoked_on = :revoked_on
				WHERE
					token_id = :token_id")
			->execute(array(
				'token_id' => $this->id,
				'revoked_on' => $this->revoked_on
			));
	}
}
