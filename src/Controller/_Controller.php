<?php namespace BidLog\Controller;

use Psr\Container\ContainerInterface;
use Slim\Psr7\Response;

use Slim\Views\PhpRenderer;

class _Controller {
    
    protected $request;
    
    protected $response;
    
    protected $container;
    
    protected $status = '200';
    
    protected $data = [];
    
    protected $start_time = null;

    private $title = 'BidvestData Logs.';
    
    /**
    * Constructor
    */
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function getSettings($setting) 
    {
        
        $settings = $this->container->get('settings');
        return isset($settings[$setting]) ? $settings[$setting] : [];
    }

    public function getConnection() {
        return $this->container->get('PDO');
    }

    /**
    * Setter for Page Title / also used in breadcrumbs
    */
    public function setTitle(String $title) 
    {
        $this->title = $title;
    }

    /**
    * Getter for page title
    */
    public function getTitle() : String
    {
        return $this->title;
    }

    public function returnJson($data)
    {
        $response =  new Response();
        $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write( json_encode($data) );

        return $response;
    }

    protected function redirect(String $path='/'): Response
    {
        $response =  new Response();
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    public function getSession() {
        return $this->getSettings('session');
    }

    protected function view($view, $data, $layout='layout/layout.php') {

        // $data = array_merge($this->getSession());

        $template_path = $this->getSettings('templates');

        $phpView = new PhpRenderer($template_path);

        if($layout) { $phpView->setLayout($layout); }

        $phpView->addAttribute('user', $this->getSettings('user'));
        $phpView->addAttribute('title', $this->getTitle());

        return $phpView->render(new Response(), $view, $data);
    }

    protected function setDefaults($req, $resp, $params) {
        $this->response = $resp;
        $this->request = $req;
        $this->params = $params;
    }

}