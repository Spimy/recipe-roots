<?php

abstract class Model {
	use Database;

	public readonly string $table;
	private $columns = [ 
		'id' => [ 
			'type' => 'INTEGER',
			'autoincrement' => true,
			'nullable' => false
		],
	];

	/**
	 * Create the table if it doesn't exist
	 * @param string $table
	 */
	public function __construct( string $table = '' ) {
		// Use the class name as the table name if not set
		$this->table = $table === '' || ! is_string( $table ) || is_numeric( $table )
			? get_class( $this )
			: $table;

		// Set the class attributes into columns
		$attributes = get_object_vars( $this );
		foreach ( $attributes as $attribute => $data ) {
			if ( $attribute === 'table' || $attribute === 'columns' ) {
				continue;
			}
			$this->columns[ $attribute ] = $data;
		}

		// Setup the query for creating the columns
		$columns = [];
		$index = 0;
		foreach ( $this->columns as $columnName => $data ) {
			$columns[ $index ] = "$columnName "
				. "{$data['type']}"
				. ( isset( $data['nullable'] ) && ! $data['nullable'] ? ' NOT NULL' : '' )
				. ( isset( $data['autoincrement'] ) && $data['autoincrement'] ? ' AUTO_INCREMENT' : '' )
				. ( isset( $data['unique'] ) && $data['unique'] ? ' UNIQUE' : '' )
				. ( isset( $data['default'] ) ? " DEFAULT '{$data['default']}'" : '' );

			$index++;
		}

		// Setup the query for foreign keys
		$foreignKeys = [];
		$index = 0;
		foreach ( $this->columns as $columnName => $data ) {
			if ( isset( $data['model'] ) ) {
				$foreignKeys[ $index ] = "FOREIGN KEY ($columnName) REFERENCES {$data['model']->table} (id)";
			}
			$index++;
		}

		// Create the query
		$query = "CREATE TABLE IF NOT EXISTS $this->table("
			. implode( ', ', $columns ) . ", "
			. ( empty( $foreignKeys ) ? '' : implode( ', ', $foreignKeys ) . ", " )
			. "createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP, "
			. "updatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, "
			. "PRIMARY KEY (id));";

		$this->query( $query );
	}

	protected function charField( int $maxLengh = 255, bool $nullable = false, bool $unique = false, string $default = '' ) {
		return [ 
			'type' => "VARCHAR($maxLengh)",
			'unique' => $unique,
			'nullable' => $nullable,
			'default' => $default == '' && $nullable ? null : $default,
		];
	}

	protected function integerField( bool $nullable = false, bool $unique = false, int $default = 0 ) {
		return [ 
			'type' => "INTEGER",
			'unique' => $unique,
			'nullable' => $nullable,
			'default' => is_numeric( $default ) ? $default : 0,
		];
	}

	protected function booleanField( bool $default = false ) {
		return [ 
			'type' => "BOOLEAN",
			"default" => is_bool( $default ) ? $default : false,
		];
	}

	protected function textField( bool $nullable = false, string $default = '' ) {
		return [ 
			'type' => "TEXT",
			'nullable' => $nullable,
			'default' => $default == '' && $nullable ? null : $default,
		];
	}

	protected function foreignKey( Model $model, bool $cascade = true ) {
		return [ 
			'type' => "INTEGER",
			'model' => $model,
			'cascade' => $cascade,
			'nullable' => false
		];
	}

	/**
	 * Create a new record into the table with the specified data.
	 * It only inserts columns that are defined in the model and ignores any extra data provided.
	 *
	 * @param array $data An associative array of column-value pairs that are the data to insert.
	 * @return void
	 */
	public function create( array $data ) {
		// Remove unwanted data
		foreach ( $data as $key => $value ) {
			if ( ! in_array( $key, array_keys( $this->columns ) ) ) {
				unset( $data[ $key ] );
			}
		}

		$keys = array_keys( $data );
		$query = "INSERT INTO $this->table (" . implode( ",", $keys ) . ") VALUES (:" . implode( ",:", $keys ) . ")";
		$this->query( $query, $data );
	}

	/**
	 * Find all records from the table that match the specified conditions.
	 *
	 * @param array $data An associative array of column-value pairs to include in the WHERE clause as equality conditions.
	 * @param array $data_not An associative array of column-value pairs to include in the WHERE clause as inequality conditions.
	 * @return array The records found as an array of an associative array.
	 */
	public function findAll( array $data = [], array $data_not = [], bool $join = false ) {
		$query = "SELECT * FROM $this->table";

		$keys = array_keys( $data );
		$keys_not = array_keys( $data_not );

		// Construct the WHERE clause if conditions are specified
		if ( ! empty( $keys ) || ! empty( $keys_not ) ) {
			$query .= " WHERE ";

			foreach ( $keys as $key ) {
				$query .= "$key = :$key && ";
			}

			foreach ( $keys_not as $key ) {
				$query .= "$key != :$key && ";
			}

			$query = trim( $query, " && " );
		}

		$data = array_merge( $data, $data_not );
		$result = $this->query( $query, $data );

		// O(n^3) HELP
		// Can't figure out a better way to optimise the code
		// Did not use INNER JOIN because the id and timestamps column names clash
		if ( $join ) {
			foreach ( $result as $index => $row ) {
				foreach ( $this->columns as $columnName => $columnData ) {
					if ( isset( $columnData['model'] ) ) {
						foreach ( $row as $key => $value ) {
							if ( $key == $columnName ) {
								$result[ $index ][ $columnData['model']->table ] = $columnData['model']->findById( $value );
							}
						}
					}
				}
			}
		}

		return $result;
	}


	/**
	 * Find a record from the table by its ID.
	 *
	 * @param int $id The ID of the record to find.
	 * @return array|null The record data as an associative array, or null if not found.
	 */
	public function findById( int $id, bool $join = false ) {
		$result = $this->query( "SELECT * FROM $this->table WHERE id = ?", [ $id ] )[0] ?? null;

		// O(n^2)
		// Similar to findAll() but with one less loop... can't think of a better way to optimise the code
		// Did not use INNER JOIN because the id and timestamps column names clash
		if ( $join && $result != null ) {
			foreach ( $this->columns as $columnName => $columnData ) {
				if ( isset( $columnData['model'] ) ) {
					foreach ( $result as $key => $value ) {
						if ( $key == $columnName ) {
							$result[ $columnData['model']->table ] = $columnData['model']->findById( $value );
						}
					}
				}
			}
		}
		return $result;
	}

	/**
	 * Update a record in the table with the specified data.
	 * It only updates columns that are defined in the model and ignores any extra data provided.
	 *
	 * @param int $id The ID of the record to update.
	 * @param array $data An associative array of column-value pairs which are the new data to update.
	 * @return void
	 */
	public function update( int $id, array $data ) {
		// Remove unwanted data
		foreach ( $data as $key => $value ) {
			if ( ! in_array( $key, array_keys( $this->columns ) ) ) {
				unset( $data[ $key ] );
			}
		}

		$keys = array_keys( $data );
		$query = "UPDATE $this->table SET ";

		foreach ( $keys as $key ) {
			$query .= "$key = :$key, ";
		}

		$query = trim( $query, ", " );
		$query .= " WHERE id = :id ";

		$data['id'] = $id;
		$this->query( $query, $data );
	}

	/**
	 * Deletes a record from the table by its ID.
	 *
	 * @param int $id The ID of the record to delete.
	 * @return void
	 */
	public function delete( int $id ) {
		$query = "DELETE FROM $this->table WHERE id = ?";
		$this->query( $query, [ $id ] );
	}

}