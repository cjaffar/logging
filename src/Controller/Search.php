<?php namespace BidLog\Controller;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ResponseInterface as ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as RequestInterface;

use Slim\Psr7\Response;
use Carbon\Carbon;

use Aws\S3\S3Client;
// use Aws\S3\Exception\S3Exception;

use Slim\Views\PhpRenderer;

use BidLog\Model\Logs as log;
use BidLog\Model\Client as client;

class Search extends _Controller 
{

	/**
	 * Constructor
	 * 
	 * @param ContainerInterface $cont
	 */
	public function __construct(ContainerInterface $cont) 
	{
		parent::__construct($cont);
		
		$this->setData();
	}
	
	/**
	 * Sets any extra defaults required by the controller.
	 */
	protected function setData(): void 
	{
		$this->addData('page_type', 'search');
	}

	/**
	 * Landing page.
	 * 
	 * @param RequestInterface $request
	 * @param ResponseInterface $response
	 * @param array $args
	 * 
	 * @return Response
	 */
	public function index(RequestInterface $request, ResponseInterface $response, array $args): Response
	{
		$result = [ ];

		$this->setTitle('Logs');

		$log = new log($this->getConnection());
		$client = new client($this->getConnection());

// 		$systems = $this->getSettings('db');

		$result['clients'] = $client->getClients([], 1);
		$result['min_date'] = $log->minLog();
		$result['systems'] = $client->getSystems(); #(isset($systems['systems'])) ? $systems['systems'] : [];
// \d($result); exit;
		$result['min_date'] = isset($result['min_date']['created']) ? Carbon::now()->diffInDays( Carbon::parse($result['min_date']['created']))  : Carbon::now()->diffInDays( Carbon::parse('5 days ago') );

		return $this->view('home.php', $result);
	}

	/**
	 * Get Clients for Systems.
	 * 
	 * @param RequestInterface $request
	 * @param ResponseInterface $response
	 * @param array $args
	 * @return Response Json Response
	 */
	public function getClients(RequestInterface $request, ResponseInterface $response, array $args): Response
	{
		$client = new client($this->getConnection());

		$system = (isset($args['system'])) ? $args['system'] : 'N/A';

		$clients = $client->getClients( ['system' => $system] , 1 );

		return $this->returnJson( [ 'clients' => $clients ] );
	}

	/**
	 * Insert fake results into DB.
	 * 
	 * @param RequestInterface $request
	 * @param ResponseInterface $response
	 * @param array $args
	 * @return Response
	 */
	public function fake(RequestInterface $request, ResponseInterface $response, array $args): Response
	{

		$log = new log( $this->getConnection() );

		$result = $log->fake();

		return $this->returnJson([ 'logs' => $result ]);
	}

	/**
	 * Main utility function for fetching logs from Db.
	 * 
	 * @param RequestInterface $request
	 * @param ResponseInterface $response
	 * @param array $args
	 * @return Response JSON Response
	 */
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
// var_dump($r_client);
		if($r_client) {
			$r_client = array_shift($r_client);
		}
// var_dump($r_client);exit;
		$l_params = [];
		if(!empty($r_client) ) {
			$l_params['clientid'] = (isset($r_client['guid'])) ? $r_client['guid'] : false;
		}

		if(isset($params['subject']) && !empty($params['subject'])){
			$l_params['subject'] = trim( $params['subject'] );
		}

		if(isset($params['email_from']) && !empty($params['email_from'])){
			$l_params['email_from'] = trim( $params['email_from'] );
		}

		if(isset($params['email_to']) && !empty($params['email_to'])){
			$l_params['email_to'] = trim( $params['email_to'] );
		}
		
		if(isset($params['email_reply']) && !empty($params['email_reply'])){
			$l_params['email_reply'] = trim( $params['email_reply'] );
		}
		
		if(isset($params['date1']) && !empty($params['date1'])){
			$l_params['date1'] = date('Y-m-d', strtotime( $params['date1'] ) );
		}

		if(isset($params['detail']) && !empty($params['detail'])){
			$l_params['detail'] = trim($params['detail']);
		}

		if(isset($params['date2']) && !empty($params['date2'])){
			$l_params['date2'] = date('Y-m-d', strtotime( $params['date2'] ) );
		}
// var_dump($l_params); exit;
		$cols = ['l.guid','l.created','c.name as client', 'system','REPLACE(from_address, \'"\', \'\' ) AS from_address','to_address', 'subject_line', 'dequeuer_line as detail']; // 'detail'];//'address_replyto', 
		$record_count = $log->getLogs($l_params, $cols, true);

		$pag = $this->paginationVars( $record_count['total'], $MAX_RECORDS, $page_number, $format );
		$pag['path'] = $client_slug;


		$logs = $log->getLogs($l_params, $cols, false, $pag['start'], $pag['end']);
		
		//clear the logs, remove htmlchars
		

		$result = [ 'logs' => $logs, 'pagination' => $pag, 'num_pages' => $pag['num_pages']];
		$result['pagination'] = '';

		if($format) {
			return $this->createCsv($params, $result);
		}
// \var_dump($pag);
		if($page_number) { $result['pagination'] = $this->getRenderedPagination( $pag, $page_number ); }

		return $this->returnJson( $result );

		// return $this->view('logs/logs.php', [], false);
	}
	
	/**
	 * Get an AWS Log.
	 * 
	 * @param RequestInterface $request
	 * @param ResponseInterface $response
	 * @param array $args
	 * @return Response
	 */
	public function getAWSLog(RequestInterface $request, ResponseInterface $response, array $args): Response
	{
		$return = [];
		
		$log = new log( $this->getConnection() );
		
		$logid = (isset($args['logid'])) ? $args['logid'] : 0;

		$cols = ['l.id','l.created','c.name as client', 'system','slug','subject','address_from','address_to', 'address_replyto', 'subject', 'detail'];
		$logs = $log->getLogs(['guid' => $logid], $cols, false, 0, 1);

		if($logs) { 
			$logs = array_shift($logs);
		}

		$s3_details = $this->getS3();

		$s3 = $s3_details['s3'];
		$aws_settings = $s3_details['settings'];
		
		if($s3_details['error']) {
			$return['error'] = $s3_details['error'];
			return $this->returnJson($return);
		}
// \d($aws_settings);
		return $this->getAWSFile($s3, $logs, $aws_settings );
	}
	
	/**
	 * Gets file from AWS, saves locally and returns location.
	 * 
	 * @param array $log
	 * @param array $settings
	 * @return Response
	 */
	protected function getAWSFile( $s3, array $log, array $settings ): Response
	{		
		$result = [];

		$folder = $log['system'];
		$filename = "{$log['slug']}/" . date('Y/m/d', strtotime($log['created'])) . '/' . $log['id']  . '.log';
		
		$local_filename = str_replace('/', '', $filename);
		try {
			
			$data = $s3->getObject([
					'Bucket' => $settings['bucket'],
					'Key' => $folder.'/' . $filename, //filename
					'SaveAs' => $settings['assets_path'] . $local_filename
			]);

			if(is_file($settings['assets_path'] . $local_filename)) {
				$result['location'] = '/assets/downloads/' . $local_filename;
			}
			
		} catch(\Exception $ex) {
			$result['error'] = $ex->getMessage();
		}
		
		if(!is_file($settings['assets_path'] . $local_filename)) {
			$result['error'] = $result['error'] ? $result['error'] : 'Unable to create AWS File in local.';
		}
		
		return $this->returnJson( $result );
	}

	/**
	 * 
	 * @return array[] | settings[]|\BidLog\Controller\S3Client[]
	 */
	protected function getS3()
	{
		$aws_settings = $this->getSettings('aws');
		
		$aws_settings['assets_path'] = $this->getSettings('assets_path') . 'downloads/';
		
		$error_message = false;
		
		try {
			
			$s3 = new S3Client([
					'version' => 'latest',
					'region'  => 'eu-west-2',
					'credentials' => [
							'key'    => $aws_settings['id'],
							'secret' => $aws_settings['key'],
					]
			]);
			
		} catch(\Exception $ex) {
			$s3 = false;
			$error_message = $ex->getMessage();
		}
		
		return ['s3' => $s3, 'settings' => $aws_settings, 'error' => $error_message];
	}
	/**
	 * Downloads a CSV file. Save to file, and then respond with the URL location of the file.
	 * 
	 * @param array $params
	 * @param array $results
	 * @return Response
	 */
	protected function createCsv(array $params, array $results) : Response 
	{
		$assets_path = $this->getSettings('assets_path');
		$csv_path = $assets_path . 'downloads/';

		$filename = md5(json_encode($params)) . '.csv';
		// if(!is_file( $csv_path . $filename )) {

			try{

				$headers = array_keys($results['logs'][0]);

				if($fh = fopen($csv_path . $filename, 'w')) {

					fputcsv($fh, $headers);
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

	/**
	 * Get pagination variables.
	 * 
	 * @param int $total
	 * @param int $perpage
	 * @param int $pagenumber
	 * @param string $format
	 * @return array
	 */
	protected function paginationVars(int $total, int $perpage, int $pagenumber, string $format) : array
	{
		$pagination = [ 'num_pages' => 1, 'start' => 0, 'end' => 50000000, 'total' => $total ];

		if($total > $perpage && trim($format) == '' ) {

			$pagination['num_pages'] = ceil( $total  / $perpage);
			$pagination['start'] = $perpage * ($pagenumber-1);
			$pagination['end'] = $perpage;
// 			$pagination['total'] = $total;
		}

		return $pagination;
	}

	/**
	 * Returns a pagination div.
	 * 
	 * @param array $pagination_vars
	 * @param unknown $pagenumber
	 * @return string
	 */
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