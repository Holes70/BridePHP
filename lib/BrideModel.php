<?php

namespace Lib;

class BrideModel {

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