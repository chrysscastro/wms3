<?php


include_once(__DIR__ . "/../../database/database.php");
include_once(__DIR__ . "/../config/config_system.php");
include_once(__DIR__ . "/../../public/gerais.php");



use app\database\connect;
use app\public_\gerais;
use app\config\setting;


$bd = new connect();
$setting = new setting();
$ger = new gerais();


//DECLARA JSON
$json = null;
$ger->doc_json();

if (isset($_GET['funcao'])) {
    $funcao = $_GET['funcao'];
    $id = isset($_POST['id']) ? $_POST['id'] : 0;

    $status = isset($_POST['status']) ? $_POST['status'] : '';
    $chave = isset($_POST['chave']) ? $_POST['chave'] : '';
    $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : '';
    $limit = isset($_POST['limit']) ? $_POST['limit'] : '';

    switch ($funcao) {

        case 'fila':
            $json = fila($status, $tipo, $chave,$limit);
            break;
        case 'json':
            $json = get_json($id);
            break;
        case 'refresh':
            $json = refresh_fila($id);
            break;
        case 'return':
            $json = get_return($id);
            break;
        case 'delete':
            $json = del_job($id);
            break;
        default:
            $json = array("status" => "error", "mensagem" => "Função não desconhecida!");
            break;
    }
} else {
    $json = array("status" => "error", "mensagem" => "Função não informada!");
}


if ($funcao != "json") {
    $ger->imprimir(json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
} else {
    $ger->imprimir($json);
}

function del_job($id)
{
    global $bd;
    global $setting;

    $query = "DELETE " . $setting::PREFIX_TABELAS . "job_serv  WHERE id = '$id'";


    $con = $bd->getQueryMysql($query);

    $json = null;

    if ($con) {
        $json = array("status" => "success", "mensagem" => "Documento deletado com sucesso!");
    } else {
        $json = array("status" => "error", "mensagem" => "Falha ao deletar o documento!");
    }

    return $json;
}

function refresh_fila($id)
{
    global $bd;
    global $setting;

    $query = "UPDATE " . $setting::PREFIX_TABELAS . "job_serv SET `status` = 'PENDENTE'
         WHERE id = '$id'";


    $con = $bd->getQueryMysql($query);

    $json = null;

    if ($con) {
        $json = array("status" => "success", "mensagem" => "Documento Enviado novamente para a Fila de Impressão!");
    } else {
        $json = array("status" => "error", "mensagem" => "Falha ao enviar documento para a Fila de Impressão!");
    }

    return $json;
}

function get_return($id)
{

    global $bd;
    global $setting;

    $query = "SELECT retorno FROM " . $setting::PREFIX_TABELAS . "job_mov WHERE movimento = '$id'";

    $con = $bd->getQueryMysql($query);

    $json = null;

    if ($con) {
        $row = $con->fetch_assoc();
        $json = $row['retorno'];
    }

    return json_decode($json);
}

function get_json($id)
{

    global $bd;
    global $setting;

    $query = "SELECT `json` as retorno FROM " . $setting::PREFIX_TABELAS . "job_mov WHERE movimento = '$id'";

    $con = $bd->getQueryMysql($query);

    $json = null;

    if ($con) {

        $row = $con->fetch_assoc();
        $json = $row['retorno'];
    }




    return $json;
}

function fila($status, $tipo, $chave,$limit)
{

    global $bd;
    global $setting;

    $query = "SELECT 
                    i.datacria AS datacria, 
                    i.id AS id, 
                    i.tipo AS tipo, 
                    i.`status` AS `status`, 
                    i.chave AS chave, 
                    i.serie AS serie, 
                    tta.NOME AS usuario
                FROM 
                    " . $setting::PREFIX_TABELAS . "job_serv i
                INNER JOIN 
                    " . $setting::PREFIX_TABELAS . "impressao_rfid s 
                ON 
                    s.`local` = i.chave AND s.serie = i.serie
                INNER JOIN 
                    txc_tb_acesso tta 
                ON
                    tta.USUARIO = s.usuario 
                WHERE
                    (COALESCE('$tipo', '') = '' OR i.tipo LIKE '%$tipo%')
                    AND
                    (COALESCE('$chave', '') = '' OR i.chave LIKE '%$chave%')
                    AND
                    (COALESCE('$status', '') = '' OR i.status = '$status')
                ORDER BY 
                    CASE 
                        WHEN i.`status` = 'enviando' THEN 0 
                        WHEN i.`status` = 'falha' THEN 1
                        ELSE 2 
                    END, 
                    datacria DESC, 
                    `status` DESC
                LIMIT $limit;
                ";



    $result = $bd->getQueryMysql($query);
    $result_num = $bd->getCountMysql($result);

    $item = array();

    if ($result_num > 0) {
        while ($row = $result->fetch_assoc()) {
            $data = DateTime::createFromFormat('Y-m-d H:i:s', $row['datacria']);
            $data_formatada = $data->format('d/m/Y H:i:s');

            $item[] =  array(
                "id" => $row['id'],
                "tipo" => $row['tipo'],
                "data" => $data_formatada,
                "chave" => $row['chave'],
                "serie" => $row['serie'],
                "status" => $row['status'],
                "usuario" => $row['usuario']
            );
        }
    }

    $json = array("status" => "success", "mensagem" => "Dados retornados!", "retorno" => $item);
    return $json;
}
