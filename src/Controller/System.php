<?php namespace BidLog\Controller;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ResponseInterface as ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as RequestInterface;

use Slim\Psr7\Response;

use BidLog\Model\Client;

class System extends _Controller 
{
	/**
	* Constructor
	*/
	public function __construct(ContainerInterface $cont) 
	{
		parent::__construct($cont);
	}

	/**
	* Landing page.
	*/
	// public function index(RequestInterface $request, ResponseInterface $response, array $args): Response
	// {
	// 	#$log = new Log($this->getConnection());

		
	// 	#return $this->view('home.php', ['hello' => 'world']);
	// }

 	public function fake(RequestInterface $request, ResponseInterface $response, array $args): Response
 	{

 		$client = new Client( $this->getConnection() );
 		$result = $client->fake();

 		return $this->returnJson([ 'clients' => $result ]);
 	}
}