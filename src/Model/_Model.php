<?php namespace BidLog\Model;

use PDO;

class _Model 
{

	protected $connection;

	public function __construct(PDO $connection) 
	{
		$this->connection = $connection;
	}

	/**
	* Do a select query and get one row result
	*/
	public function getOne(String $sql, array $vars): array {

		$qry = $this->connection->prepare($sql);

		$qry->execute($vars);
		$results = $qry->fetch( PDO::FETCH_ORI_FIRST);

		return ($results) ? $results : [] ;
	}

	/**
	* Do a Select query and return all results as array
	*/
	public function getAll(String $sql, array $vars) : array
	{
		$qry = $this->connection->prepare($sql);

		$qry->execute($vars);

		$results = $qry->fetchAll( PDO::FETCH_ASSOC );

		return ($results) ? $results : [];
	}

	/**
	* Generic insert query.
	*/
	public function doInsert(String $sql, array $vars): int {

		$qry = $this->connection->prepare($sql);

		$qry->execute( array_values($vars) );

		return $this->connection->lastInsertId();
	}

	/**
	* Generic update query.
	*
	* @param $sql String
	* @param $vars array
	* @return int Number of affected rows.
	*/
	public function doUpdate(String $sql, array $vars): int {
		$qry = $this->connection->prepare($sql);

		return $qry->execute( array_values($vars) );
	}
}