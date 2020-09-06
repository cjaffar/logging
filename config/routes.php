<?php 

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy; 
use Slim\App;

use BidLog\Middleware\Authorised;
use BidLog\Middleware\Permitted;

use BidLog\Controller;

return function($app) {

	$app->group('/search', function (RouteCollectorProxy $group) {

		$group->get('/fake', Controller\Search::class . ':fake');
		$group->post('/{client-slug}/{page}[/{format}]', Controller\Search::class . ':search')->add(new Authorised());
		$group->get('', Controller\Search::class . ':index')->add(new Authorised());

	});

	$app->group('/client', function (RouteCollectorProxy $group) {

		$group->get('/fake', Controller\System::class . ':fake');

	});

	$app->group('/login', function (RouteCollectorProxy $group) {

		$group->get('/fake', Controller\Login::class.':fake' );
		$group->get('/logout', Controller\Login::class.':logout' )->add(new Authorised());

		$group->get('/add-user', Controller\Login::class.':newUser')->add(new Permitted()); 
		$group->post('/add-user', Controller\Login::class.':newUserPost')->add(new Permitted()); 
		$group->get('/users', Controller\Login::class.':usersList')->add(new Authorised()); 

		$group->get('/user-edit/{username}', Controller\Login::class.':editUserGet')->add(new Permitted());
		$group->post('/user-edit/{username}', Controller\Login::class.':editUserPost')->add(new Permitted());

		$group->get('/my-profile', Controller\Login::class . ':profileGet')->add(new Authorised());
		$group->post('/my-profile', Controller\Login::class . ':profilePost')->add(new Authorised());

		$group->post('', Controller\Login::class.':post'); //function(Request $request, Response $response, $args) {
		$group->post('/creds-change', Controller\Login::class.':passwordChange')->add(new Authorised());
	
	});


	$app->get('/', Controller\Login::class . ':get');
	
};