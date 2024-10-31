<?php

abstract class Model {
	use Database;

	public readonly string $table;
	public readonly string $idColumn;
	private $columns;

	/**
	 * Create the table if it doesn't exist
	 * @param string $table
	 */
	public function __construct( string $idColumn, string $table = '' ) {
		// Use the class name as the table name if not set
		$this->table = $table === '' || ! is_string( $table ) || is_numeric( $table )
			? get_class( $this )
			: $table;
		$this->idColumn = $idColumn;

		// Create id of the table
		$this->columns[ $this->idColumn ] = [ 
			'type' => 'INTEGER',
			'autoincrement' => true,
			'nullable' => false
		];

		// Set the class attributes into columns
		$attributes = get_object_vars( $this );
		foreach ( $attributes as $attribute => $data ) {
			if ( $attribute === 'table' || $attribute === 'columns' || $attribute === 'idColumn' ) {
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
				. ( isset( $data['unique'] ) && $data['unique'] ? ' UNIQUE' : '' );

			$index++;
		}

		// Setup the query for foreign keys
		$foreignKeys = [];
		$index = 0;
		foreach ( $this->columns as $columnName => $data ) {
			if ( isset( $data['model'] ) ) {
				$foreignKeys[ $index ] = "FOREIGN KEY ($columnName) REFERENCES {$data['model']->table} ({$data['model']->idColumn})";
			}
			$index++;
		}

		// Create the query
		$query = "CREATE TABLE IF NOT EXISTS $this->table("
			. implode( ', ', $columns ) . ", "
			. ( empty( $foreignKeys ) ? '' : implode( ', ', $foreignKeys ) . ", " )
			. "createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP, "
			. "updatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, "
			. "PRIMARY KEY ({$this->idColumn}));";

		$this->query( $query );
	}

	protected function charField( int $maxLengh = 255, bool $nullable = false, bool $unique = false ) {
		return [ 
			'type' => "VARCHAR($maxLengh)",
			'unique' => $unique,
			'nullable' => $nullable
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
	 * Specify the tableName.id if join is set to true.
	 *
	 * @param array $data An associative array of column-value pairs to include in the WHERE clause as equality conditions.
	 * @param array $data_not An associative array of column-value pairs to include in the WHERE clause as inequality conditions.
	 * @param array $join Specify if you want to join other tables.
	 * @return array The records found as an array of an associative array.
	 */
	public function findAll( array $data = [], array $data_not = [], bool $join = false ) {
		$query = "SELECT * FROM $this->table";

		if ( $join ) {
			foreach ( $this->columns as $columnName => $data ) {
				if ( isset( $data['model'] ) ) {
					$foreignTable = $data['model']->table;
					$foreignIdColumn = $data['model']->idColumn;
					$query .= " INNER JOIN {$foreignTable} ON {$this->table}.{$columnName} = {$foreignTable}.{$foreignIdColumn}";
				}
			}
		}

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
		return $this->query( $query, $data );
	}


	/**
	 * Find a record from the table by its ID.
	 *
	 * @param int $id The ID of the record to find.
	 * @param array $join Specify if you want to join other tables.
	 * @return array|null The record data as an associative array, or null if not found.
	 */
	public function findById( int $id, bool $join = false ) {
		$query = "SELECT * FROM $this->table";

		if ( $join ) {
			foreach ( $this->columns as $columnName => $data ) {
				if ( isset( $data['model'] ) ) {
					$foreignTable = $data['model']->table;
					$foreignIdColumn = $data['model']->idColumn;
					$query .= " INNER JOIN {$foreignTable} ON {$this->table}.{$columnName} = {$foreignTable}.{$foreignIdColumn}";
				}
			}
		}

		$query .= " WHERE {$this->table}.{$this->idColumn} = ?";
		return $this->query( $query, [ $id ] )[0] ?? null;
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