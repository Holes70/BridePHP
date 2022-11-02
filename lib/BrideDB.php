<?php

namespace Lib;

use mysqli;
use Exception;

class BrideDB
{

	private $dbName = '';
	private $userName = '';
	private $password = '';
	private $host = 'localhost';
	private $port = 3306;
	private $socket = null;
	private $encoding = 'latin1';

	private $mysqliCon = null;

	protected function setup(
		string $dbName, 
		string $userName, 
		string $password
	) {
		$this->dbName = $dbName;
		$this->userName= $userName;
		$this->password = $password;

		$this->mysqliCon = new mysqli($this->dbName, $this->userName, $this->password);

		if ($this->mysqliCon->connect_error) {
			throw new Exception('BrideDB connect error: ' . $this->mysqliCon->connect_error);
		}
	}
}