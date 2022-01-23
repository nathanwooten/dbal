<?php

namespace nathanwooten\Databaser\Dbal;

use PDO;
use PDOStatement;

class Dbal
{

	protected $procedure = [
		'select' => [
			'fetch' => [
				[ 'stmt' ], [ 'result' ]
			]
		],
		'query' => [
			'prepare' => [
				[ 'sql' ], [ 'stmt' ]
			],
			'bind' => [
				[ 'stmt', 'params' ], [ 'stmt' ]
			],
			'execute' => [
				[ 'stmt' ], [ 'stmt' ]
			]
		]
	];

	public function __construct( $connection = [] )
	{

		try {
			$this->pdo = new PDO( ...array_values( $connection ) );

			$this->pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

		} catch ( PDOException $e ) {
			$this->handle( $e );
		}

	}

	public function operate( $action, ...$parameters )
	{

		$result = $this->$action( ...$parameters );
		return $result;


	}

	protected function query( $procedure, $sql, $params = [] )
	{

		try {

			foreach ( $procedure as $method => $parts ) {
				$in = $parts[ 0 ];
				$out = $parts[ 1 ];

				foreach ( $in as $key => $var ) {
					$in[ $key ] = ${$var};
				}

				${$out[0]} = $this->$method( ...$in );

			}
		} catch ( DatabaserException $e ) {
			$this->handle( $e );
		}

		$result = ${$out[0]};

		return $result;

	}

	public function select( $sql, $params = [] )
	{

		$select = array_merge( $this->procedure[ 'query' ], $this->procedure[ 'select' ] );

		$result = $this->query( $select, $sql, $params );

		return $result;

	}

	public function insert( $sql, $params = [] )
	{

		$result = $this->query( $this->procedure[ 'query' ], $sql, $params );
		return $result;

	}

	public function update( $sql, $params = [] )
	{

		$result = $this->query( $this->procedure[ 'query' ], $sql, $params );
		return $result;

	}

	public function delete( $sql, $params = [] )
	{

		$result = $this->query( $this->procedure[ 'query' ], $sql, $params );
		return $result;

	}

	protected function prepare( $sql )
	{

		$stmt = $this->getPDO()->prepare( $sql );
		return $stmt;

	}

	protected function bind( PDOStatement $stmt, $params = [] )
	{

		$result = [];

		foreach ( $params as $key => $param ) {

			$key = is_string( $key ) ? ':' . ltrim( $key, ':' ) : $key +1;

			$result[] = $stmt->bindValue( $key, $param );
		}

		if ( ! in_array( false, $result ) ) {
			return $stmt;
		}

		return false;

	}

	protected function execute( $stmt ) {

		$result = $stmt->execute();

		if ( $result ) {
			return $stmt;
		}

		throw new Exception( 'Database execute error' );

	}

	protected function fetch( $stmt, $fetch_mode = PDO::FETCH_ASSOC )
	{

		$fetched = [];

		while ( $data = $stmt->fetch( $fetch_mode ) ) {

			$fetched[] = $data;
		}

		return $fetched;

	}

	protected function getPDO()
	{

		return $this->pdo;

	}

	public function getName()
	{

		return 'dbal';

	}

}
