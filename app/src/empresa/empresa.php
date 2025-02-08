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

$metodo = $_SERVER['REQUEST_METHOD'];


switch ($metodo) {
    case 'GET':
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $tipo = isset($_GET['tipo']) ? $_GET['tipo'] : null;
        switch ($tipo) {
            case 'emp':
                $json = get_not_emp_user($id);
                break;
            default:
                $json = get_emp_user($id);
                break;
        }
        
        break;
    case 'POST':
        $id = isset($_POST['id']) ? $_POST['id'] : null;
        $empresa = isset($_POST['empresa']) ? $_POST['empresa'] : null;
        $json = get_emp($id, $empresa);
        break;
    default:
        $json = array("code" => 500, "mensagem" => "Método selecionado incorreto!");
        break;
}

$ger->imprimir(json_encode($json, JSON_UNESCAPED_UNICODE));


function get_emp($iduser, $emp)
{

    global $bd;
    global $sec;

    $json = null;

    $query = "SELECT tte.emp_pat AS codemp, tte.emp_cnpj AS cnpj, tte.emp_nome AS razao
                FROM tb_txc_emp_user tteu
                INNER JOIN txc_tb_empresas tte ON tte.emp_pat = tteu.emp_pat
                WHERE tteu.id_user = $iduser and  tteu.emp_pat = '$emp'";


    $con = $bd->getQueryMysql($query);

    if ($con) {

        if (mysqli_num_rows($con) > 0) {
            $data = array();

            while ($row = $con->fetch_assoc()) {
                $data[] = array("codemp" => $row['codemp'], "cnpj" => $row['cnpj'], "razao" => $row['razao']);
            }

            $json = array("code" => 200, "mensagem" => "Empresa selecionada com sucesso!", "data" => $data);
            $sec->setCustomCookie("empresa_ck", $emp);
        } else {
            $json = array("code" => 404, "mensagem" => "Empresa não encontrada!");
        }
    } else {
        $json = array("code" => 500, "mensagem" => "Erro ao selecionar empresa!");
    }

    return $json;
}


function get_emp_user($iduser)
{

    global $bd;

    $json = null;

    $query = "SELECT tte.emp_pat AS codemp, tte.emp_cnpj AS cnpj, tte.emp_nome AS razao
                FROM tb_txc_emp_user tteu
                INNER JOIN txc_tb_empresas tte ON tte.emp_pat = tteu.emp_pat
                WHERE tteu.id_user = $iduser ORDER BY tte.emp_pat";


    $con = $bd->getQueryMysql($query);

    if ($con) {

        $data = array();

        while ($row = $con->fetch_assoc()) {
            $data[] = array("codemp" => $row['codemp'], "cnpj" => $row['cnpj'], "razao" => $row['razao']);
        }

        $json = array("code" => 200, "mensagem" => "Consulta realizada com sucesso!", "data" => $data);
    } else {
        $json = array("code" => 500, "mensagem" => "Erro ao processar consulta!");
    }

    return $json;
}

function get_not_emp_user($iduser)
{

    global $bd;

    $json = null;

    $query = "SELECT DISTINCT tte.emp_pat AS codemp, tte.emp_cnpj AS cnpj, tte.emp_nome AS razao
                FROM txc_tb_empresas tte
                WHERE tte.emp_pat NOT IN (
                    SELECT tteu.emp_pat
                    FROM tb_txc_emp_user tteu
                    WHERE tteu.id_user = $iduser
                )";


    $con = $bd->getQueryMysql($query);

    if ($con) {

        $data = array();

        while ($row = $con->fetch_assoc()) {
            $data[] = array("codemp" => $row['codemp'], "cnpj" => $row['cnpj'], "razao" => $row['razao']);
        }

        $json = array("code" => 200, "mensagem" => "Consulta realizada com sucesso!", "data" => $data);
    } else {
        $json = array("code" => 500, "mensagem" => "Erro ao processar consulta!");
    }

    return $json;
}
