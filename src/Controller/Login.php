<?php namespace BidLog\Controller;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ResponseInterface as ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as RequestInterface;

use Slim\Psr7\Response;

use BidLog\Model\User as user;

class Login extends _Controller 
{
	/**
	* Constructor
	*/
	public function __construct(ContainerInterface $cont) 
	{

		parent::__construct($cont);
	}

	/**
	* Gets Login html form. Checks whether user is logged in and sends to landing page.
	* 
	* @param $request RequestInterface
	* @param $response ResponseInterface
	* @param $args array
	* @return Response login page.
	*/
	public function get(RequestInterface $request, ResponseInterface $response, array $args): Response
	{
		$this->setDefaults($request, $response, $args);

		return $this->view('login.php', [], $layout='layout/plain.php');
	}

	/**
	/* Handles logic for logging in the User.
	*/
	public function post(RequestInterface $request, ResponseInterface $response, array $args): Response
	{
		$user = new user( $this->getConnection() );

		$post = $request->getParsedBody();

		$username = (isset($post['username'])) ? trim($post['username']) : 'N/A';
		$password = (isset($post['password'])) ? trim($post['password']) : 'N/A';

		$result = $user->login( $username, $password );
		$result['success'] = false;

		if(!empty($result)) {

			if(isset($result['username'])) {
				
				$result['success'] = true;
				// $_SESSION['user'] = $result;
			}

			if(isset($result['dirty']) && $result['dirty'] == 0) {
				$_SESSION['user'] = $result;
				$user->loggedIn( $result['username'] );
			}

		}

		return $this->returnJson($result);
	}

	/**
	* Populates User table with fake data.
	*/
	public function fake(RequestInterface $request, ResponseInterface $response, array $args): Response
	{
		
		$limit = 10;
		
		$users = new user( $this->getConnection() );
		$users->fake($limit);

		return $this->returnJson([ 'users' => $limit]);
	}

	/*
	* Get user's profile.
	*
	*/
	public function profileGet(RequestInterface $request, ResponseInterface $response, array $args) : Response 
	{
		$this->setDefaults($request, $response, $args);
		$session = $this->getSettings('user');

		$username = (isset($session['username'])) ? $session['username'] : 'N/A';

		$this->setTitle('Edit My Profile');

		return $this->returnUserProfile($username, 'my-profile');
	}

	/**
	* Handles User profile edit.
	*/
	public function profilePost(RequestInterface $request, ResponseInterface $response, array $args) : Response 
	{
		$this->setDefaults($request, $response, $args);
		$return = ['success' => false];

		$session = $this->getSettings('user');

		$username = (isset($session['username'])) ? $session['username'] : 'N/A';

		$post = $request->getParsedBody();
		$p_username = (isset($post['username'])) ? trim($post['username']) : 'N/A';

		if($p_username != $username) { $return['message'] = 'Not Allowed.'; return $this->returnJson($return); }

		return $this->userProfileUpdate($username, $post);
	}

	/**
	* Handles User profile edit.
	*/
	public function editUserPost(RequestInterface $request, ResponseInterface $response, array $args) : Response 
	{
		// $this->setDefaults($request, $response, $args);
		$return = ['success' => false];

		$post = $request->getParsedBody();
		$username = (isset($post['username'])) ? trim($post['username']) : 'N/A';

		return $this->userProfileUpdate($username, $post);
	}

	/**
	* Generic function to return User edit details page.
	*/
	protected function returnUserProfile(String $username, String $section): Response 
	{

		$users = new user( $this->getConnection() );

		$profile = [ 'profile' => $users->getUser( $username ) ];
		$profile['function'] = $section;

		return $this->view('user/profile.php', $profile);
	}

	/**
	* Admit edits a user's profile.
	*/
	public function editUserGet(RequestInterface $request, ResponseInterface $response, array $args) : Response 
	{
		$username = (isset($args['username'])) ? trim($args['username']) : 'N/A';

		$this->setTitle('Edit Profile :: ' . $username);

		return $this->returnUserProfile($username, 'edit');
	}

	/**
	* Generic function to update user profiles.
	*/
	protected function userProfileUpdate(String $username, array $post): Response
	{

		$results = [ 'success' => false ];

		$vars = [];
		foreach($post as $k => $v) {
			$vars[strtolower($k)] = $v;
		}

		if(isset($vars['inputpwd']) && !empty($vars['inputpwd'])) {
			$vars['password'] = $vars['inputpwd'];
		}

		if(isset($vars['password']) && empty($vars['password']) ) {
			unset($vars['password']);
		}

		$user = new User($this->getConnection());
		$return = $user->updateProfile($username, $vars);

		if($return) {
			$results = array_merge($results, $return);

			$results['success'] = true;
		}

		return $this->returnJson( $results );
	}

	/**
	* Add new User
	*/
	public function newUserPost(RequestInterface $request, ResponseInterface $response, array $args) : Response 
	{

		$result = ['success' => false ];

		$post = $request->getParsedBody();

		$username = (isset($post['username'])) ? $post['username'] : 'N/A';

		$user = new User( $this->getConnection() );

		$res = $user->newUser( $username, $post );
		if(!$res) {
			$result['message'] = 'Adding new User unsuccessful, please try again!';
			return $this->returnJson($result);
		}

		unset($res['password']);
		unset($res['salt']); 
		$result['success'] = true;

		$result = array_merge($res, $result);
		return $this->returnJson($result);
	}

	/**
	* Password change for user.
	*/
	public function passwordChange(RequestInterface $request, ResponseInterface $response, array $args) : Response 
	{

		$result = [ 'success' => false ];
		
		// $session = $this->getSession();
		$post = $request->getParsedBody();

		// if($session) {

		$password = (isset($post['password'])) ? trim($post['password']) : 'N/A';
		$password2 = (isset($post['password2'])) ? trim($post['password2']) : 'N/A';
		$username = (isset($post['userattempt'])) ? trim($post['userattempt']) : 'N/A';

		if( $password == $password2 ) {

			$user = new user( $this->getConnection() );

			$res = $user->resetPassword($username, $password );
			if(is_array($res) && !empty($res)) {

				$res['success'] = true;
				$_SESSION['user'] = $res;

				$user->loggedIn( $res['username'] );

				$result = $res;
			}
		}

		// }

		return $this->returnJson($result);
	}

	/**
	* Add new User
	*/
	public function newUser(RequestInterface $request, ResponseInterface $response, array $args) : Response 
	{

		$options = [];
		$actions[] = '<a href="/login/users" class="btn">Users</a>';

		$options['actions'] = $actions;
		$options['function'] = 'add';

		$this->setTitle('Add User');
		return $this->view('user/add.php', $options);
	}

	/**
	* Get Users List
	* 
	* @todo Add logic for pagination and stuff.
	*/
	public function usersList(RequestInterface $request, ResponseInterface $response, array $args) : Response 
	{
		$this->setTitle('Users');

		$options = [];
		$actions = [];

		$user = new User( $this->getConnection() );
		$options['list'] = $user->getList(); //

		
		$actions[] = '<a href="/login/add-user" class="btn">Add New User</a>';

		$options['actions'] = $actions;

		return $this->view('user/list.php', $options);
	}

	/**
	* Logout a user.
	*/
	public function logout(RequestInterface $request, ResponseInterface $response, array $args): Response
	{

		$_SESSION['user'] = [];
		session_destroy();
		session_regenerate_id();

		return $this->redirect('/');
	}

}