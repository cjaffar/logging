<?php namespace BidLog\Controller;

use Psr\Container\ContainerInterface;
use Slim\Psr7\Response;
use PDO;

use Slim\Views\PhpRenderer;

/**
 * Base Controller. All controllers should extend this base controller.
 * @author jaffar
 *
 */
class _Controller {
    
    protected $request;
    
    protected $response;
    
    protected $container;
    
    protected $data = [];

    private $title = 'BidvestData Logs.';
    
    /**
    * Constructor
    */
    public function __construct(ContainerInterface $container) 
    {
        $this->container = $container;
    }

    /**
     * Get particular item from Settings.
     * 
     * @param unknown $setting
     * @return array|mixed setting if available.
     */
    public function getSettings($setting) 
    {        
        $settings = $this->container->get('settings');
        return isset($settings[$setting]) ? $settings[$setting] : [];
    }

    /**
     * 
     * @return PDO connection
     */
    public function getConnection(): PDO 
    {
        return $this->container->get('PDO');
    }

    /**
    * Setter for Page Title / also used in breadcrumbs
    * 
    * @param string $title
    */
    public function setTitle(string $title): void 
    {
        $this->title = $title;
    }

    /**
    * Getter for page title
    * 
    * @return string $title
    */
    public function getTitle(): string
    {
        return $this->title;
    }
    
    /**
     * 
     * @param string $key
     * @param unknown $val
     */
    public function addData(string $key, $val): void
    {
    	$this->data[$key] = $val;
    }

    /**
     * Returns JSON response.
     * 
     * @param array $data
     * @return \Slim\Psr7\Response
     */
    public function returnJson(array $data) : Response
    {
        $response =  new Response();
        $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write( json_encode($data) );

        return $response;
    }

    /**
     * redirect helper.
     * 
     * @param String $path
     * @return Response
     */
    protected function redirect(string $path='/'): Response
    {
        $response =  new Response();
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    /**
     * get session.
     * 
     * @return array|mixed
     */
    public function getSession() {
        return $this->getSettings('session');
    }

    /**
     * return the view response.
     * 
     * @param string $view path where view resides
     * @param array $data data to be embedded into the view.
     * @param string $layout layout path for the view
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function view(string $view, array $data, string $layout='layout/layout.php'): Response 
    {

        $template_path = $this->getSettings('templates');

        $phpView = new PhpRenderer($template_path);

        if($layout) { $phpView->setLayout($layout); }

        $phpView->addAttribute('user', $this->getSettings('user'));
        $phpView->addAttribute('title', $this->getTitle());
        
        foreach($this->data as $key=>$value) {
        	$phpView->addAttribute($key, $value);
        }

        return $phpView->render(new Response(), $view, $data);
    }

    /**
     * Passing extra params to the controller.
     *
     * @deprecated Not being used currently. rather pass the params to data
     * @param unknown $req
     * @param unknown $resp
     * @param unknown $params
     */
    protected function setDefaults($req, $resp, $params) {
        $this->response = $resp;
        $this->request = $req;
        $this->params = $params;
    }

}