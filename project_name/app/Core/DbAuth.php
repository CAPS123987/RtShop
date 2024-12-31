<?php

namespace App\Core;

use Nette\Security\SimpleIdentity;
use Nette\Security\IIdentity;
use Nette;
use Tracy\Debugger;

class DbAuth implements Nette\Security\Authenticator, Nette\Security\IdentityHandler
{
	private $salt = "salt";
	public function __construct(
		private Nette\Database\Explorer $database,
	) {
	}

	public function authenticate(string $username, string $password): SimpleIdentity
	{
		$username = htmlspecialchars($username);
		$password = htmlspecialchars($password);

		$row = $this->database->table('users')
			->where('email', $username)
			->fetch();

		if (!$row) {
			throw new Nette\Security\AuthenticationException('User not found.');
		}

		$password = hash("sha256",$password.$this->salt);

		if ($password != $row->password) {
			throw new Nette\Security\AuthenticationException('Invalid password.');
		}

		return new SimpleIdentity(
			$row->id,
			$row->role, // nebo pole více rolí
			json_decode($row->data,true),
		);
	}

	public function register(string $username, string $email, string $password): void
	{	
		$username = htmlspecialchars($username);
		$email = htmlspecialchars($email);
		$password = htmlspecialchars($password);
		
		$row = $this->database->table('users')->where('email', $email)->fetch();
		if ($row) {
			throw new Nette\Security\AuthenticationException('User with this email already exists.');
		}
		$password = hash("sha256",$password.$this->salt);
		
		$this->database->table('users')->insert([
			'email' => $email,
			'password' => $password,
			'data' => json_encode([]),
		]);
		
		$row = $this->database->table('users')->where('email', $email)->where('password', $password)->fetch();
	}

	public function sleepIdentity(IIdentity $identity): IIdentity
	{
		// here you can change the identity before storing after logging in,
		// but we don't need that now
		return $identity;
	}

	public function wakeupIdentity(IIdentity $identity): ?SimpleIdentity
	{
		// updating roles in identity
		$userId = $identity->getId();
		$row = $this->database->table('users')->where('id', $userId)->fetch();

		return $row ? new SimpleIdentity(
			$row->id,
			$row->role,
			json_decode($row->data,true),
		):
		null;
	}
}
