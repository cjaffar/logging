<?php namespace BidLog\Model;

use Faker;

use PDO;

class User extends _Model 
{

	protected String $table = 'users';

	/**
	* Constructor. Takes in PDO Conn
	*
	* @param $connection PDO
	* @return null
	*/
	public function __construct(PDO $connection) 
	{
		parent::__construct($connection);
	}

	/**
	* Gets user details from DB.
	*
	* @param $username String
	* @return array User details.
	*/
	public function getUser(String $username): array 
	{
		$sql = "SELECT username, concat(firstname, ' ', lastname) as name, firstname, lastname, admin, dirty, lastlogin, password, salt FROM {$this->table} WHERE username LIKE ?";
		
		return $this->getOne($sql, [ $username ]);
	}

	/**
	* Gets list of users from DB
	* 
	* @todo logic for pagination.
	*/
	public function getList(int $limit = 20) : array 
	{
		$sql = "SELECT * FROM {$this->table} LIMIT {$limit}";

		return $this->getAll( $sql, [] );
	}

	public function updateProfile(String $username, array $post): array
	{
		$user = $this->getUser($post['username']);

		if(!$user) {
			return [];
		}

		$columns = [];
		if(isset($post['password']) && !empty($post['password']))	 {
			$columns['password'] = trim($post['password']);
		}

		if(isset($post['firstname'])) {
			$columns['firstname'] = $post['firstname'];
		}

		if(isset($post['lastname'])) {
			$columns['lastname'] = $post['lastname'];
		}

		if(isset($post['isadmin']) && $post['isadmin'] == 'on') {
			$columns['admin'] = 1;
		}

		if(isset($columns['password'])) {
			$columns['password'] = $this->preparePassword($user['salt'], $post['password']);
		}

		$sql = "UPDATE {$this->table} SET ";
		$sql .= implode('=?,', array_keys($columns)) . '=?';
		$sql .= ' WHERE username = ? LIMIT 1';

		$columns['username'] = $username;

		if( $this->doUpdate( $sql, array_values($columns) ) ) {
			return $this->getUser( $username );
		}

		return [];
	}


	/**
	* Do login query.
	*/
	public function login(String $username, String $pwd): array 
	{

		$user = $this->getUser($username);

		if(!$user) { return []; }

		$password = $this->preparePassword($user['salt'], $pwd);
		if($password != $user['password']) {
			return [];
		}

		unset($user['password']);
		unset($user['salt']);

		return $user;
	}

	public function loggedIn($username): int {

		$sql = "UPDATE {$this->table} SET lastlogin = ?, dirty=0 WHERE username LIKE ? LIMIT 1";
		return $this->doUpdate($sql, [ date('Y-m-d H:i:s'), $username] );
	}

	/**
	* Resetting a Password.
	*
	* @param $username String
	* @param @$pwd String
	* @return array user credentials signify a successful password reset.
	*/
	public function resetPassword(String $username, String $pwd) : array
	{

		$user = $this->getUser($username);

		if (!$user) {
			return [];
		}
		
		$password_h = $this->preparePassword($user['salt'] , $username);

		$sql = "UPDATE {$this->table} SET password = ? WHERE username LIKE ? LIMIT 1";
		$qry = $this->doUpdate($sql, [$username, $password_h]);

		if($qry) {
			unset($user['password']);
			unset($user['salt']);

			return $user;
		}

		return [];
	}

	protected function preparePassword($salt, $pwd) 
	{
		return hash('sha1', $salt.$pwd);
	}

	/**
	* Salt Logic
	*/
	protected function getSalt(): String {
		return substr(uniqid(rand(), TRUE), 0, 10);
	}

	public function newUser(String $username, array $params): array
	{
		$user = $this->getUser($username);

		if($user) {
			return [];
		}

		$sql = "INSERT INTO {$this->table}(username, password, firstname, lastname, salt, admin, dirty) VALUES(?, ?, ?, ?, ?, ?, ?)";

		$vars = [];
		$vars['username'] = $username;
		$vars['password'] = (isset($params['inputPwd'])) ? $params['inputPwd'] : '12345678';
		$vars['firstname'] = (isset($params['firstName'])) ? $params['firstName'] : 'N/A';
		$vars['lastname'] = (isset($params['lastName'])) ? $params['lastName'] : 'N/A';
		$vars['salt'] = $this->getSalt();
		$vars['admin'] = (isset($params['isAdmin']) && $params['isAdmin'] == 'on') ? 1 : 0;
		$vars['dirty'] = 1;

		$vars['password'] = $this->preparePassword($vars['salt'], $vars['password']);

		if( $this->doInsert($sql, $vars) ) {
			return $this->getUser($username);
		}

		return [];
	}

	/**
	* Inserts fake users into DB.
	*/
	public function fake(int $limit = 10): int {

		$faker = Faker\Factory::create();

		$sql = "INSERT INTO users(username, password, firstname, lastname, salt) VALUES(?, ?, ?, ?, ?)";

		$count = 0;
		for($i=0; $i<$limit; $i++) {

			$vars = [
				'username' => $faker->userName,
				'password' => '12345678',
				'firstname' => $faker->firstName,
				'lastname' => $faker->lastName,
				'salt' => $this->getSalt()
			];

			$vars['password'] = $this->preparePassword($vars['salt'], '12345678');

			if( $this->doInsert($sql, $vars) ) {
				$count++;
			}
		}

		return $count;

	}
}