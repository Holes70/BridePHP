<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/../lib/BrideDB.php');
require_once(__DIR__ . '/../lib/BrideModel.php');

require_once(__DIR__ . '/../BridePHP.php');

$bride = new BridePHP('test_ucm_skladky', 'root', '');

$bride->tablePrefix('ucm');
$userModel = $bride->initModel('skladky');

$userModel->defineColumn('nazov')->type('varchar')->size(255)->default(0);


var_dump($userModel->tableColumns);
//var_dump($userModel->query('SELECT * FROM {model}'));

//var_dump($usersData );

/*$bride->model('users')->create('name')->default(0);
$bride->model('users')->create('vek');
$bride->model('users')->install();*/

//$bride->query("SELECT * FROM users");