<?php


include_once(__DIR__ . "/../../database/database.php");
include_once(__DIR__ . "/../config/config_system.php");
include_once(__DIR__ . "/../../public/gerais.php");
include_once(__DIR__ . "/../access/check_access.php");


use app\database\connect;
use app\public_\gerais;
use app\config\setting;


$bd = new connect();
$setting = new setting();
$ger = new gerais();


//DECLARA JSON
$json = null;
$ger->doc_json();

$metodo = $ger->get_metodo();


switch ($metodo) {
    case 'GET':
        $type = isset($_GET['type']) ? $_GET['type'] : '';
        if (!empty($type)) {
            switch ($type) {
                case 'get_ped':
                    $dtini = isset($_GET['dtini']) ? $_GET['dtini'] : null;
                    $dtfim = isset($_GET['dtfim']) ? $_GET['dtfim'] : null;
                    $imp = isset($_GET['imp']) ? $_GET['imp'] : null;
                    $ped = isset($_GET['ped']) ? $_GET['ped'] : '';
                    if (!empty($ped)) {
                        $ped = "AND PEDIDO.NUMERO = '$ped'";
                    }
                    $json = get_ped($empresa_geral, $dtini, $dtfim, $ped, $imp);
                    break;
                default:
                    $json = array("code" => 500, "mensagem" => "Type não reconhecido! Informado: $type");
                    break;
            }
        } else {
            $json = array("code" => 500, "mensagem" => "Type não informado!");
        }
        break;
    case 'PUT':

        break;
    case 'POST':
        break;
    default:
        $json = array("code" => 500, "mensagem" => "Método não permitido!");
        break;
}


$ger->imprimir(json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));


function get_ped($empresa, $dtini, $dtfim, $ped, $emp)
{

    global $bd;

    $json = null;

    if($emp == 0){
        $emp = '0 OR PEDIDO.IMPRESSO IS NULL';
    }

    $query = "SELECT 
        1 AS TIPO,
        PEDIDO.NUMERO as numero, REPRESEN.NOME as rep, 
        PEDIDO.CODREP AS CODREP_PED, ENTIDADE.nome AS CLIENTE,PEDIDO.DT_EMISSAO AS emissao
        FROM PEDIDO_$empresa PEDIDO 
        LEFT JOIN ENTIDADE_001 ENTIDADE ON (PEDIDO.CODCLI = ENTIDADE.CODCLI) 
        LEFT JOIN REPRESEN_$empresa REPRESEN ON (REPRESEN.CODREP = PEDIDO.CODREP) 
        WHERE 
        1 = 1
        AND PEDIDO.DT_EMISSAO <= '$dtfim'  
        AND PEDIDO.DT_EMISSAO >= '$dtini'
        AND (PEDIDO.IMPRESSO = $emp)
        $ped
        ORDER BY PEDIDO.DT_EMISSAO DESC";



    $con = $bd->getQueryPostgres($query);

    if ($con) {
        $ped = array();
        while ($reg = pg_fetch_assoc($con)) {
            $ped[] = array(
                "rep" => $reg['rep'],
                "pedido" => $reg['numero'],
                "data" => date('d/m/Y', strtotime($reg['emissao']))
            );
        }

        $json = array("code" => 200, "mensagem" => "Pedidos capturados com sucesso!", "data" => $ped);
    } else {
        $json = array("code" => 500, "mensagem" => "Erro ao buscar pedido!");
    }


    return $json;
}
