<?php

namespace App;

use PDO;

class User
{
	protected $id;
	protected $first_name;
	protected $middle_name;
	protected $last_name;
	protected $gender;
	protected $birthdate;
	protected $address;
	protected $contact;
	protected $email;
	protected $pass;
	protected $created_at;

	public function getId()
	{
		return $this->id;
	}

	public function getFullName()
	{
		return $this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name;
	}

	public function getFirstName()
	{
		return $this->first_name;
	}

	public function getMiddleName()
	{
		return $this->middle_name;
	}

	public function getLastName()
	{
		return $this->last_name;
	}

	public function getEmail()
	{
		return $this->email;
	}

	public function getBirthDate()
	{
		return $this->birthdate;
	}

	public function getGender()
	{
		return $this->gender;
	}

	public function getAddress()
	{
		return $this->address;
	}

	public function getContact()
	{
		return $this->contact;
	}

	public static function getById($id)
	{
		global $conn;

		try {
			$sql = "
				SELECT * FROM users
				WHERE id=:id
				LIMIT 1
			";
			$statement = $conn->prepare($sql);
			$statement->execute([
				'id' => $id
			]);
			$result = $statement->fetchObject('App\User');
			return $result;
		} catch (PDOException $e) {
			error_log($e->getMessage());
		}

		return null;
	}

	public static function hashPassword($password)
	{
		$hashed_password = null;
		$hashed_password = crypt($password,'$5$');
		return $hashed_password;
	}

	public static function attemptLogin($email, $pass)
	{
		global $conn;

		try {
			$sql = "
				SELECT * FROM users
				WHERE email=:email
					AND pass=:pass
				LIMIT 1
			";
			$statement = $conn->prepare($sql);
			$user = $statement->fetchObject('App\User');
		
			if (password_verify($pass, User::hashPassword($pass))) {
				$pass = User::hashPassword($pass);
				$statement->execute([
					'email' => $email,
					'pass' => $pass
				]);
				$result = $statement->fetchObject('App\user');
			}

			return $result;
		} catch (PDOException $e) {
			error_log($e->getMessage());
		}

		return null;
	}

	public static function register($first_name, $middle_name, $last_name, $gender, $birthdate, $address, $contact, $email, $password)
	{
		global $conn;
		

		try {
			$hashed_password = self::hashPassword($password);
			$sql = "
				INSERT INTO users (first_name, middle_name, last_name, gender, birthdate, address, contact, email, pass)
				VALUES ('$first_name', '$middle_name', '$last_name', '$gender', '$birthdate', '$address', '$contact', '$email', '$hashed_password')
			";

			$conn->exec($sql);
			// echo "<li>Executed SQL query " . $sql;
			return $conn->lastInsertId();
		} catch (PDOException $e) {
			error_log($e->getMessage());
		}

		return false;
	}

	public static function registerMany($users)
	{
		global $conn;

		try {
			foreach ($users as $user) {
				$sql = "
					INSERT INTO users
					SET
						first_name=\"{$user['first_name']}\",
						middle_name=\"{$user['middle_name']}\",
						last_name=\"{$user['last_name']}\",
						email=\"{$user['email']}\",
						birthdate=\"{$user['birthdate']}\",
						gender=\"{$user['gender']}\",
						address=\"{$user['address']}\",
						contact=\"{$user['contact']}\",
						pass=\"{$user['pass']}\"
				";
				$conn->exec($sql);
				// echo "<li>Executed SQL query " . $sql;
			}
			return true;
		} catch (PDOException $e) {
			error_log($e->getMessage());
		}

		return false;
	}	
}