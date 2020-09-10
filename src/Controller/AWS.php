<?php namespace BidLog\Controller;


use Psr\Container\ContainerInterface;

use Psr\Http\Message\ResponseInterface as ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as RequestInterface;

use Slim\Psr7\Response;

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
// use Aws\Resource\Aws;

/**
 * This class is abandoned. logic moved into BidLog\Controller\Search
 * 
 * @author jaffar
 * @deprecated
 */
class AWS extends _Controller {

	protected $s3;

	protected string $assets_path;

	protected array $aws_settings;
	/**
	* Constructor
	*/
	public function __construct(ContainerInterface $cont) 
	{
		parent::__construct($cont);

		$this->_initialise();
	}

	/**
	* Initialise vars used for AWS connection.
	*/
	private function _initialise() {

		$this->aws_settings = $aws_settings = $this->getSettings('aws');

		$this->assets_path = $this->getSettings('assets_path') . 'downloads/';
		
		try {

			$this->s3 = new S3Client([
			    'version' => 'latest',
			    'region'  => 'eu-west-2',
			    'credentials' => [
			        'key'    => $aws_settings['id'],
			        'secret' => $aws_settings['key'],
			    ]
			]);

		} catch(\Exception $ex) {
			$this->s3 = false;
		}
	}

	/**
	* Landing page for AWS.
	* 
	*/
	public function index(RequestInterface $request, ResponseInterface $response, array $args): Response
	{

		$arr = [];

		$this->setTitle('AWS File List');

		$filelist = $this->getFileList();

		if(isset($filelist['Contents'])) {
			$arr['filelist'] = [];

			foreach($filelist['Contents'] as $content) {

				if($content['Key'][-1] == '/') { continue; } //do not display the folder.

				$arr['filelist'][] = [ 'filename' =>  $content['Key'], 'modified' => $content['LastModified'] ] ;
			}
		}

		if(!$arr['filelist']) {
			$arr['error'] = 'There was an error making connection to AWS. File list not found.';
		}

		return $this->view( 'aws/index.php', $arr );

	}

	protected function getBucketList($s3) 
	{
		return $s3->listBuckets();
	}

	/**
	* Gets File list at AWS in the specified folder.
	*/
	protected function getFileList( ) : array
	{

		if(!$this->s3 || !$this->aws_settings ) { return [];  }

		$result = $this->s3->listObjects([
			'Bucket' => $this->aws_settings['bucket'],
			'Prefix' => $this->aws_settings['folder']
		]);

		return ($result) ? $result->toArray() : [];
	}

	public function getFile(RequestInterface $request, ResponseInterface $response, array $args): Response
	{

		$result = [];
	
		$query_params = $request->getQueryParams();

		$filename = '';
		if(isset($query_params['file'])) {
			$filename = urldecode($query_params['file']);
		}

		if(!$filename) { $result['error'] = 'Did not get filename in request.'; }
		else {
			try {

				$basename = basename($filename);

				$data = $this->s3->getObject([
				    'Bucket' => $this->aws_settings['bucket'],
				    'Key' => $filename, //filename
				    'SaveAs' => $this->assets_path . $basename . 'hell'
				]);

				// if($data) {
				// 	$data = $data->toArray();
				// }
				// $object_exists = $s3->doesObjectExist($settings['bucket'], $settings['key'] );
				if(is_file($this->assets_path . $basename)) {
					$result['location'] = '/assets/downloads/' . $basename;
				}

			} catch(\Exception $ex) {
				$result['error'] = $ex->getMessage();
			}

			 if(!is_file($this->assets_path . $basename) && !isset($result['error'])) { // this is a redudant fail safe!
				$result['error'] = 'File creation error. File not created';
			}
		}
// \d($result);
		return $this->returnJson($result);
	}
}