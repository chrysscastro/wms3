<?php

date_default_timezone_set('America/Sao_Paulo');

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


$ger->doc_json();

$json = null;


if (isset($_POST['usuario']) && isset($_POST['senha'])) {
    $usuario = strtoupper($_POST['usuario']);
    $senha = $sec->encryptString($_POST['senha'], 'md5');

    $query = "SELECT * FROM " . $setting::PREFIX_TABELAS . "acesso WHERE USUARIO = '$usuario' and SENHA = '$senha' AND ATIVO = -1";

    $con_req = $bd->getQueryMysql($query);

    if ($con_req) {

        $con_num = $bd->getCountMysql($con_req);

        if ($con_num > 0) {

            while ($row = $con_req->fetch_assoc()) {

                $usuario = $row['USUARIO'];
                $nome = $row['NOME'];
                $iduser = $row['ID'];
                $loem = $row['LOEM'];
                $local = $row['LOCAL'];

                $sec->setCustomCookie("user_ck", $usuario);
                $sec->setCustomCookie("nome_ck", $nome);
                $sec->setCustomCookie("iduser_ck", $iduser);
                $sec->setCustomCookie("loem_ck", $loem);
                $sec->setCustomCookie("local_ck", $local);
                $sec->setCustomCookie("senha_ck", $_POST['senha']);

                $query_empresa = "SELECT tte.emp_pat AS codemp, tte.emp_cnpj AS cnpj, tte.emp_nome AS razao
                            FROM tb_txc_emp_user tteu
                            INNER JOIN txc_tb_empresas tte ON tte.emp_pat = tteu.emp_pat
                            WHERE tteu.id_user = $iduser ";

                $con_empresa = $bd->getQueryMysql($query_empresa);

                $con_num_empresa = mysqli_num_rows($con_empresa);


                if ($con_num_empresa == 0) {
                    $json = array("status" => "falha", "mensagem" => "Não há empresa cadastrada para o usuário!");
                    break;
                }


                $json = array("status" => "sucesso", "mensagem" => "Login feito com sucesso!", "id" => $iduser);


                $log = date('d/m/Y H:i:s', time()) . " Login";

                $query = "UPDATE " . $setting::PREFIX_TABELAS . "acesso SET `LOG` = '$log' WHERE ID = '$iduser'";
                $con_req_update = $bd->getQueryMysql($query);
            }
        } else {
            $json = array("status" => "falha", "mensagem" => "Usuario ou Senha incorretos!");
        }
    } else {
        $json = array("status" => "falha", "mensagem" => "Usuario ou Senha incorretos!");
    }
} else {
    $json = array("status" => "falha", "mensagem" => "Usuario ou Senha nao informado!");
}


$ger->imprimir(json_encode($json, JSON_OBJECT_AS_ARRAY));
