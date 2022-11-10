<?php

namespace Bride\Lib;

class Model {

	public string $modelName = '';
	public array $options;

	public string $tableName = '';
	public array $tableColumns;

	public function __construct(
		string $modelName,
		array $options
	) {
		$this->modelName = $modelName;

		if (isset($options['tablePrefix'])) {
			$this->tableName = $options['tablePrefix'] . '_' . $this->modelName;
		}
	}

	public function query(string $query) {
		$query = str_replace('{model}', $this->tableName, $query);

		$bindParams = func_get_args();
		unset($bindParams[0]);

		return \DB::query($query, $bindParams);
	}

	/**
   * @param int $id
   * @return array data
   */
  public function getById(int $id) : array {
    return (array)\DB::queryFirstRow("SELECT * FROM {$this->tableName} WHERE id = %d", $id);
  }

	/**
   * @param int $id
   * @return array data
   */
  public function getByCustom(string $colName, $value) : array {
    return (array)\DB::queryFirstRow("SELECT * FROM {$this->tableName} WHERE {$colName} = %s", $value);
  }

	/**
   * @return array data
   */
  public function getAll() : array {
    return \DB::query("SELECT * FROM {$this->tableName} ORDER BY id DESC");
  }

  /**
   * @return array data
   */
  public function getAllOrderBy(string $orderByCol = "id", string $orderByAso = "DESC") : array {
    return \DB::query("SELECT * FROM {$this->tableName} ORDER BY %s %s", $orderByCol, $orderByAso);
  }

  /**
   * @param array data to insert
   * @return int created record id
   */
  public function insert(array $dataToInsert) : int {
    \DB::insert($this->tableName, $dataToInsert);

    return \DB::insertId();
  }

	/**
   * @param int $id
   * @return bool
   */
  public function delete(int $id): bool {
    return \DB::delete($this->tableName, 'id = %d', $id);
  }

  /**
   * @param array $data
   * @param int $id
   */
  public function update(array $data, int $id) {
    return \DB::update(
      $this->tableName, 
      $data, 
      "id = %i",
      $id
    );
  }


	public function defineColumn(string $columnName) {
		$this->tableColumns[$columnName] = [];

		return $this;
	}

	public function type(string $columnType) {
		$this->tableColumns[key(array_slice($this->tableColumns, -1))]['type'] = $columnType;

		return $this;
	}

	public function size(string $columnSize) {
		$this->tableColumns[key(array_slice($this->tableColumns, -1))]['size'] = $columnSize;

		return $this;
	}

	public function default($default) {
		$this->tableColumns[key(array_slice($this->tableColumns, -1))]['default'] = $default;

		return $this;
	}

	public function null(bool $null = true) {
		$this->tableColumns[key(array_slice($this->tableColumns, -1))]['null'] = $null === true 
			? "NULL"
			: "NOT NULL"
		;

		return $this;
	}

	public function initTable() {
		\DB::query("DROP TABLE IF EXISTS `{$this->tableName}`");

		$query = "CREATE TABLE `{$this->tableName}` (id int AUTO_INCREMENT PRIMARY KEY";

		foreach ($this->tableColumns as $tableColumnName => $tableColumnParam) {
			$size = isset($tableColumnParam['size']) 
				? "({$tableColumnParam['size']})"
				: ""
			;
			
			$defaultValue = isset($tableColumnParam['default']) 
				? "DEFAULT '{$tableColumnParam['default']}'"
				: ""
			;

			$null = isset($tableColumnParam['null']) 
				? "{$tableColumnParam['null']}"
				: ""
			;

			$query .= ",{$tableColumnName} {$tableColumnParam['type']}{$size} {$defaultValue} {$null}"; 
		}

		$query .= ");";

		return \DB::query($query);
	}
}