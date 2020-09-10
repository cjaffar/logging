<?php namespace BidLog\Model;

use Faker;
use PDO;

class Logs extends _Model {
	
	protected string $table = 'logs';

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

	public function minLog() {
		$sql = "select min(created) as created from {$this->table}";

		$qry = $this->getOne($sql, []);
		return ($qry) ? $qry : [];
	}

	/**
	* Get All logs
	*/
	public function getLogs(array $params, array $columns=[], bool $count_only=false, int $start=0, int $limit = 500000000) : array
	{
		if(!$columns) { $columns = ['*']; }

		$columns = implode(',', $columns);
		
		$sql = "SELECT {$columns} FROM {$this->table} l ";

		if($count_only) {
			$sql = "SELECT count(l.id) AS total FROM {$this->table} l ";
		}

		$sub_sql = "";

		$vars = [];

		$where_and = [];
		$where_or = [];

		if(isset($params['clientid'])) {
			$where_and['clientid'] = ' l.clientid = ? ';
			$vars[] = (int)$params['clientid'];
		}
		
		if(isset($params['id'])) {
			$where_and['id'] = ' l.id = ? ';
			$vars[] = (int)$params['id'];
		}

		if(isset($params['date1']) && isset($params['date2']) ) {
			$where_and['datebetween'] = ' l.created BETWEEN ? AND ?';
			$vars[] = $params['date1'];
			$vars[] = $params['date2'];
		}

		if(isset($params['email_from'])) {
			$where_or['address_from'] = ' address_from LIKE ?'; //(int)$params['clientid'];
			$vars[] = "%{$params['email_from']}%";
		}
		
		if(isset($params['email_to'])) {
			$where_or['address_to'] = ' address_to LIKE ?'; //(int)$params['clientid'];
			$vars[] = "%{$params['email_to']}%";
		}

		if(isset($params['email_reply'])) {
			$where_or['address_replyto'] = ' address_reply to LIKE ?'; //(int)$params['clientid'];
			$vars[] = "%{$params['email_replyto']}%";
		}

		// if(isset($params['email'])) {
			// $vars['clientid'] = (int)$params['clientid'];
		// }

		$sql .= " LEFT JOIN clients c ON c.id = l.clientid ";

		$sql .= ($where_and || $where_or) ? ' WHERE ' : ''; //and the where keyword if we have any and-or where blocks.

		$sql .= ($where_and) ? ' ( ' . implode( ' AND  ', $where_and ) . ')' : '';

		$sql .= ($where_and && $where_or) ? ' AND ' : ''; // ADD AND AND BETWEEN THE OR AND THE AND WHERE BLOCKS

		$sql .= ($where_or) ? ' ( ' . implode( ' OR  ', $where_or ) . ')' : '';

		if(isset($params['subject'])) {
			$sql .= (stripos($sql, 'WHERE' ) === false) ? ' WHERE ' : ' AND ';
			$sql .= ' MATCH(subject) AGAINST(?) ';

			$vars[] = '%'.trim($params['subject']).'%';
		}

		if(isset($params['detail`'])) {
			$sql .= (stripos($sql, 'WHERE' ) === false) ? ' WHERE ' : ' AND ';
			$sql .= ' MATCH(detail) AGAINST(?) ';

			$vars[] = '%'.trim($params['subject']).'%';
		}


		if($count_only) {
			return $this->getOne($sql, array_values($vars) );
		}

		$sql .= " ORDER BY l.created DESC";
		$sql .= ' LIMIT ' . $start . ','. $limit;// . (int; // . ' OFFSET ' . ;
// d($vars);
// echo $sql;
		return $this->getAll($sql, array_values($vars) );
	}


	public function fake(int $limit = 50): int
	{
		$faker = Faker\Factory::create();

		$loop  = range(0, $limit);

		$count = 0;
		foreach($loop as $l) {
			$vars = [
				'clientid' => $faker->numberBetween(1, 20),
				'address_from' => $faker->safeEmail(),
				'address_replyto' => $faker->safeEmail(),
				'address_to' => $faker->safeEmail(),
				'subject' => $faker->realText(42),
				'detail' => $faker->text,
				'created' => $faker->dateTimeBetween($startDate = '-30 years', $endDate = 'now')->format('Y-m-d H:i:s')
			];

			$sql = "INSERT INTO {$this->table}(" . implode(",", array_keys($vars)) . ")  VALUES(?, ? ,? ,? ,?, ?, ?)";

			if( $this->doInsert($sql, array_values($vars) ) ) {
				$count++;
			}
		}

		foreach(range(0, $limit * 5) as $i) {

			$sql = "UPDATE {$this->table} set CREATED = ? WHERE created is null limit 1";
			$this->doUpdate($sql, [ $faker->dateTimeBetween($startDate = '-30 years', $endDate = 'now')->format('Y-m-d H:i:s') ]);
		}

		return $count;
	}
}