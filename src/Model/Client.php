<?php namespace BidLog\Model;

use Faker;
use PDO;

class Client extends _Model {
	
	protected string $table = 'clients';

	/**
	* Constructor. Takes in PDO Conn
	*
	* @param $connection PDO
	* @return null
	*/
	public function __construct(PDO $connection)
	{
		parent::__construct($connection);
	}

	public function getClients(array $params, int $active): array
	{
		$vars = [];
		$sql = "SELECT * FROM {$this->table} WHERE ";
		
		$vars['active'] = $active;
		// $vars['1'] = 1;

		$table_cols = [ 'id', 'system', 'slug', 'name', 'created' ];
		foreach($params as $k => $v) {
			if(in_array($k, $table_cols) && !empty($v)) {
				$vars[$k] = $v;
			}
		}

		$sql .= implode(" = ? AND ", array_keys($vars)) . " = ?";
		$sql .= " ORDER BY name ASC";

		$results = $this->getAll($sql, array_values($vars) );

		return ($results) ? $results : [] ;
	}

	/**
	* Add new client.
	*/
	public function add(array $params): int
	{

		$vars = [];
		foreach($params as $key => $value) {
			$vars[$key] = $value;
		}

		$vars['active'] = 1;
		$vars['created'] = date('Y-m-d H:i:s');

		$count = 0;
		$sql = "INSERT IGNORE INTO {$this->table}(" . implode(",", array_keys($vars)) . ")  VALUES(?, ? ,? ,?, ?)";

		if( $this->doInsert($sql, array_values($vars) ) ) {
			$count++;
		}	

		return $count;
	}
	
	/**
	* Update Client settings.
	 */
	public function edit(string $slug, array $params): array
	{

		$client = $this->getClients(['slug' => $slug], 1);

		if(!$client) {
			return [];
		}
		
		$vars = [];
		$valid_cols = ['system', 'name', 'active'];
		foreach($params as $key => $value) {
			if(in_array($key, $valid_cols)) {
				$vars[$key] = $value;
			}
		}
		
		if(!$vars) {
			return [];
		}

		$sql = "UPDATE {$this->table} SET ";
		$sql .= implode('=?,', array_keys($vars)) . '=?';
		$sql .= ' WHERE slug LIKE(?) LIMIT 1';

		$vars['slug'] = $slug;
		if( $this->doUpdate( $sql, array_values($vars) ) ) {
			return $this->getClients( ['slug' => $slug ], 1);
		}
		
		return [];
	}

	/**
	* Build fake entries into clients table.
	*/
	public function fake($limit = 10): int
	{
		$faker = Faker\Factory::create();

		$loop  = range(0, $limit);

		$count = 0;
		foreach($loop as $l) {

			$company = substr($faker->company, 0, 18);

			$vars = [
				'system' => $faker->numberBetween(1, 20),
				'slug' => strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $company), '-')),
				'name' => $company,
				'created' => date('Y-m-d H:i:s'),
			];

			$sql = "INSERT IGNORE INTO {$this->table}(" . implode(",", array_keys($vars)) . ")  VALUES(?, ? ,? ,?)";

			if( $this->doInsert($sql, array_values($vars) ) ) {
				$count++;
			}
		}

		return $count;
	}
}