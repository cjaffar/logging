<?php namespace BidLog\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
// use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

use Slim\Psr7\Response;

class Authorised 
{

	public function __invoke(Request $request, RequestHandler $handler): Response {

		$response = $handler->handle($request);

		$user = isset($_SESSION['user']) ? $_SESSION['user'] : [];

		if ($user) {
			return $response;
		} else {
			$response =  new Response();
			return $response->withHeader('Location', '/')->withStatus(302);
			exit;
		}
	}
}