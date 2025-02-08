<?php


include_once(__DIR__ . "/../../database/database.php");
include_once(__DIR__ . "/../../public/gerais.php");
include_once(__DIR__ . "/../access/check_access.php");
include_once(__DIR__ . "/../../src/config/config_system.php");

use app\database\connect;
use app\public_\gerais;
use \app\config\setting;
use \app\public_\seguranca;


$bd = new connect();
$ger = new gerais();
$setting = new setting();
$sec = new seguranca();


$json = null;

$ger->doc_json();

$json = array("id" => 1, "usuario" => "admin", "senha" => "123","nome" => "Chrystopher Castro","tipo" => 1,"permissao" =>
array(array("nivel" => 1,"liberacao" => -1),
array("nivel" => 2,"liberacao" => -1),
array("nivel" => 3,"liberacao" => -1)));


$ger->imprimir(json_encode($json, JSON_UNESCAPED_UNICODE));

