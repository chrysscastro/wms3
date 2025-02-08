<?php



include_once(__DIR__ . "/../../database/database.php");
include_once(__DIR__ . "/../../public/gerais.php");
include_once(__DIR__ . "/../../src/config/config_system.php");


use \app\database\connect;
use \app\public_\gerais;
use \app\public_\seguranca;
use \app\config\setting;


$ger = new gerais();
$bd = new connect();
$sec = new seguranca();
$setting = new setting();


//$ger->doc_json();

$json = null;

@$usuario = $ger->getCookie("user_ck");
@$senha = $ger->getCookie("senha_ck");



if (!empty($usuario) && !empty($senha)) {


    $usuario = $usuario;
    $senha = $sec->encryptString($senha, 'md5');

    $query = "SELECT * FROM " . $setting::PREFIX_TABELAS . "acesso WHERE USUARIO = '$usuario' and SENHA = '$senha'  AND ativo = -1";

    $con_req = $bd->getQueryMysql($query);

    if ($con_req) {
        while ($row = $con_req->fetch_assoc()) {
            $usuario_geral = $row['USUARIO'];
            $nome_geral = $row['NOME'];
            $empresa_geral = $ger->getCookie("empresa_ck");
            $iduser_geral = $row['ID'];
            $loem_geral = $row['LOEM'];
            $local_geral = $row['LOCAL'];
            $tema_usuario = $row['TEMA'];
        }
    } else {

        header("Location: ../login/index.php");
    }
} else {

    header("Location: ../login/index.php");
}


//$ger->imprimir(json_encode($json,JSON_OBJECT_AS_ARRAY));
