<?php namespace BidLog\Controller;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ResponseInterface as ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as RequestInterface;

use Slim\Psr7\Response;
use Carbon\Carbon;

use Slim\Views\PhpRenderer;

use BidLog\Model\Logs as log;
use BidLog\Model\Client as client;

class Search extends _Controller 
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
	public function index(RequestInterface $request, ResponseInterface $response, array $args): Response
	{
		$result = [ ];

		$this->setTitle('Logs');

		$log = new log($this->getConnection());
		$client = new client($this->getConnection());

		$result['clients'] = $client->getClients([], 1);
		$result['min_date'] = $log->minLog();

		$result['min_date'] = isset($result['min_date']['created']) ? Carbon::now()->diffInDays( Carbon::parse($result['min_date']['created']))  : Carbon::now()->diffInDays( Carbon::parse('5 days ago') );
// \d($result);
		return $this->view('home.php', $result);
	}

	public function fake(RequestInterface $request, ResponseInterface $response, array $args): Response
	{

		$log = new log( $this->getConnection() );

		$result = $log->fake();

		return $this->returnJson([ 'logs' => $result ]);
	}

	public function search(RequestInterface $request, ResponseInterface $response, array $args): Response
	{

		$log = new log( $this->getConnection() );
		$client = new client( $this->getConnection() );

		$url_path = $request->getUri()->getPath();

		$MAX_RECORDS = 20;

		$result = [];

		$params = $request->getParsedBody();

		$client_slug = isset($args['client-slug']) ? $args['client-slug'] : 'N/A';
		$page_number = isset($args['page']) ? (int)$args['page'] : 1;
		$format = isset($args['format']) ? $args['format'] : '';

		if((int)$page_number == 0 ) {
			$page_number = 1;
		}

		$r_client = $client->getClients( [ 'slug' => $client_slug], 1 );
		if($r_client) {
			$r_client = array_shift($r_client);
		}

		$l_params = [];
		if(!empty($r_client) ) {
			$l_params['clientid'] = (isset($r_client['id'])) ? $r_client['id'] : false;
		}

		if(isset($params['subject']) && !empty($params['subject'])){
			$l_params['subject'] = trim( $params['subject'] );
		}

		if(isset($params['email']) && !empty($params['email'])){
			$l_params['email'] = trim( $params['email'] );
		}

		if(isset($params['date1']) && !empty($params['date1'])){
			$l_params['date1'] = date('Y-m-d', strtotime( $params['date1'] ) );
		}

		if(isset($params['date2']) && !empty($params['date2'])){
			$l_params['date2'] = date('Y-m-d', strtotime( $params['date2'] ) );
		}

		$cols = ['l.created','c.name as system','subject','address_from','address_to', 'address_replyto', 'subject', 'detail'];
		$record_count = $log->getLogs($l_params, $cols, true);

		$pag = $this->paginationVars( $record_count['total'], $MAX_RECORDS, $page_number, $format );
		$pag['path'] = $client_slug;
// var_dump($pag); 

		$logs = $log->getLogs($l_params, $cols, false, $pag['start'], $pag['end']);

		$result = [ 'logs' => $logs, 'pagination' => $pag ];
		$result['pagination'] = '';

		if($format) {
			return $this->createCsv($params, $result);
		}

		if($page_number) { $result['pagination'] = $this->getRenderedPagination( $pag, $page_number ); }

		return $this->returnJson( $result );

		// return $this->view('logs/logs.php', [], false);
	}

	protected function createCsv(array $params, array $results) : Response 
	{
		$assets_path = $this->getSettings('assets_path');
		$csv_path = $assets_path . 'downloads/';
// var_dump($results);
		$filename = md5(json_encode($params)) . '.csv';
		// if(!is_file( $csv_path . $filename )) {

			try{

				if($fh = fopen($csv_path . $filename, 'w')) {
					foreach($results['logs'] as $log) {
	
						fputcsv($fh, $log);
					}
					fclose($fh);
				}

				// fputcsv($fh, [1,2,3,4,5]);// $results['logs']);
				
			} catch(\Exception $ex) {
// d($ex->getMessage());
			}
		// }
// var_dump($csv_path . $filename);
		if(is_file( $csv_path . $filename )) {
			$results['location'] = '/assets/downloads/' . $filename;
		}
// var_dump($results);
		return $this->returnJson( $results );
	}

	protected function paginationVars(int $total, int $perpage, int $pagenumber, string $format) : array
	{
		$pagination = [ 'num_pages' => 1, 'start' => 0, 'end' => 50000000 ];

		if($total > $perpage && trim($format) == '' ) {

			$pagination['num_pages'] = floor( $total  / $perpage);
			$pagination['start'] = $perpage * ($pagenumber-1);
			$pagination['end'] = $pagination['start'] + $perpage;
			$pagination['total'] = $total;
		}

		return $pagination;
	}

	protected function getRenderedPagination(array $pagination_vars, $pagenumber) : string
	{
		if($pagenumber > 1) {
			return ''; //we only render pagination for the first page.
		}
// var_dump($pagenumber);
		$pagination_vars['page_number']=$pagenumber;

        $template_path = $this->getSettings('templates');

        $renderer = new PhpRenderer($template_path);
        return $renderer->fetch('logs/pagination.php', $pagination_vars);
	}
}