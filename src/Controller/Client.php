<?php namespace BidLog\Controller;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ResponseInterface as ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as RequestInterface;

use Slim\Psr7\Response;

use BidLog\Model\Client as clientModel;

/**
 * Clients main controller.
 * 
 * @author jaffar
 *
 */
class Client extends _Controller 
{
	/**
	* Constructor
	*/
	public function __construct(ContainerInterface $cont) 
	{
		parent::__construct($cont);
	}


	/**
	 * Listing page for Clients page.
	 * 
	 * @param RequestInterface $request
	 * @param ResponseInterface $response
	 * @param array $args
	 * 
	 * @return Response
	 */
	public function index(RequestInterface $request, ResponseInterface $response, array $args): Response
	{
		$vars = [];

		$client = new clientModel( $this->getConnection() );

		$this->setTitle('Clients');

		$vars['clients'] = $client->getClients([], 1);
		
		return $this->view('client/index.php', $vars);
	}


	/**
	 * Adding a Client form.
	 * 
	 * @param RequestInterface $request
	 * @param ResponseInterface $response
	 * @param array $args
	 * @return Response
	 */
	public function getAddClient(RequestInterface $request, ResponseInterface $response, array $args): Response
	{
		$vars = [];
		$client = new clientModel( $this->getConnection() );

		$systems = $this->getSettings('db');

		$vars['systems'] = (isset($systems['systems'])) ? $systems['systems'] : [];

		$this->setTitle('Add New Client');
		return $this->view('client/add.php', $vars);
	}

	/**
	 * Edit clients page.
	 * 
	 * @param RequestInterface $request
	 * @param ResponseInterface $response
	 * @param array $args
	 * @return Response
	 */
	public function getEditClient(RequestInterface $request, ResponseInterface $response, array $args): Response
	{
		$vars = [];
		$client = new clientModel( $this->getConnection() );

		$systems = $this->getSettings('db');

		$param = isset($args['client-slug']) ? $args['client-slug'] : 'N/A';

		$vars['systems'] = (isset($systems['systems'])) ? $systems['systems'] : [];
		$client = $client->getClients(['slug' => $param], 1);

		if($client) { $client = array_shift($client); }

		$vars['client'] = $client;
		$this->setTitle('Edit Client :: ' . $client['name']);
		return $this->view('client/edit.php', $vars);
	}


	/**
	 * Handle post for Add Client.
	 * 
	 * @param RequestInterface $request
	 * @param ResponseInterface $response
	 * @param array $args
	 * @return Response
	 */
	public function postAddClient(RequestInterface $request, ResponseInterface $response, array $args): Response
	{
		$vars = [];
		$return = ['success' => false];
		$client = new clientModel( $this->getConnection() );

		$params = $request->getParsedBody();
		$params['system'] = (isset($params['system'])) ? trim($params['system']) : 'N/A';
		$params['slug'] = (isset($params['slug'])) ? trim($params['slug']) : 'N/A';
		$params['name'] = (isset($params['name'])) ? trim($params['name']) : 'N/A';

		$result = $client->add($params);
		if($result) {
			$return['success'] = true;
		}

		return $this->returnJson( $return );
	}


	/**
	 * Handles post for Edit Clients.
	 * 
	 * @param RequestInterface $request
	 * @param ResponseInterface $response
	 * @param array $args
	 * @return Response
	 */
	public function postEditClient(RequestInterface $request, ResponseInterface $response, array $args): Response
	{
		$return = ['success' => false];
		
		$vars = [];
		$client = new clientModel( $this->getConnection() );

		$params = $request->getParsedBody();
		$slug = $args['client-slug'] ? $args['client-slug'] : 'N/A';
		
		if(isset($params['system']) && trim($params['system']) != '') {
			$vars['system'] = trim( $params['system'] );
		}

		if(isset($params['name']) && trim($params['name']) != '') {
			$vars['name'] = trim($params['name']);
		}
		
		if(!$vars) {
			return $this->returnJson( $return );
		}

		$result = $client->edit($slug, $vars);
		if($result) {
			$return['success'] = true;
		}

		return $this->returnJson( $return );
	}

	/**
	 * Faking db entries.
	 * 
	 * @param RequestInterface $request
	 * @param ResponseInterface $response
	 * @param array $args
	 * @return Response
	 */
 	public function fake(RequestInterface $request, ResponseInterface $response, array $args): Response
 	{

 		$client = new Client( $this->getConnection() );
 		$result = $client->fake();

 		return $this->returnJson([ 'clients' => $result ]);
 	}
}