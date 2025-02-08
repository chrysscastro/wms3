<?php

//Inclusão dos Arquivos dependentes
include_once(__DIR__ . "/../../database/database.php");
include_once(__DIR__ . "/../../public/gerais.php");
include_once(__DIR__ . "/../config/config_system.php");

//Chamando Namespaces

use app\database\connect;
use app\src\theme\construct_theme;
use app\public_\gerais;
use app\config\setting;

$setting = new setting();
$ger = new gerais();
$bd = new connect();

//Recebendo metodo Get
$metodo = $_GET['tipo'];
$retorno = null;

$ger->doc_json();

switch ($metodo) {
    case 'show':
        $dados = $_POST['dados'];
        $retorno = showUser($dados);
        break;
    case 'showUserI':
        $dados = $_POST['dados'];
        $retorno = showUserI($dados);
        break;
    case 'delEmp':
        $dados = $_POST['dados'];
        $emp = $_POST['emp'];
        $retorno = delEmp($dados, $emp);
        break;
    case 'insEmp':
        $dados = $_POST['dados'];
        $emp = $_POST['emp'];
        $retorno = insEmp($dados, $emp);
        break;
    case 'show_lib':
        $dados = $_POST['dados'];
        $retorno = showNivel($dados);
        break;
    case 'gravar':
        $json = file_get_contents('php://input');
        $retorno = gravar($json);
        break;
    default:
        # code...
        break;
}

$ger->imprimir(json_encode($retorno));

function isValidMd5($str) {
    return preg_match('/^[a-f0-9]{32}$/i', $str) === 1;
}

function gravar($json){
    
    global $bd;

    $dados = json_decode($json, true);

    // Verifica se a decodificação foi bem-sucedida
    if (json_last_error() !== JSON_ERROR_NONE) {
        return json_encode([
            'type' => 'error',
            'mensagem' => 'JSON inválido recebido.'
        ]);
    }

    $id = $dados['id'] ?? 0;
    $usuario = $dados['usuario'] ?? null;
    $senha = $dados['senha'] ?? null;
    $nome = $dados['nome'] ?? null;
    $tipo = $dados['tipo'] ?? null;
    $permissoes = $dados['permissao'] ?? [];

    if (!$id || !$usuario || !$senha || !$nome || is_null($tipo)) {
        return json_encode([
            'type' => 'error',
            'mensagem' => 'Dados do usuário incompletos.'
        ]);
    }



    if (isValidMd5($senha)) {
        $hashSenha = $senha;
    } else {
        $hashSenha = md5($senha);
    }

    if($id != 0){
        
        $log = date('d/M/Y H:i:s') . " Alteração";

        $query = "UPDATE txc_tb_acesso SET NOME = '$nome',  PERMISSAO = $tipo, SENHA = '$hashSenha', ATIVO = -1, LOG = '$log' WHERE id = $id";

        $con = $bd->getQueryMysql($query);

    }else{

        $log = date('d/M/Y H:i:s') . " Criação";

        $query = "INSERT INTO txc_tb_acesso (USUARIO,NOME,SENHA,PERMISSAO,LOG,ATIVO,EMPRESA)
        VALUES ('$usuario','$nome','$hashSenha','$tipo','$log',-1,'004)";
        
        $con = $bd->getQueryMysql($query);

        $id = $bd->getInsertId();

    }


    foreach ($permissoes as $permissao) {



        $query = "SELECT * FROM txc_tb_nivel_acesso WHERE usuario = '$id' AND nivel = '" . $permissao['nivel'] ."' ";
        

        $con2 = $bd->getQueryMysql($query);
        $con2_num = $bd->getCountMysql($con2);

        if ($con2_num == 0)
        {
            
            $query = "INSERT INTO txc_tb_nivel_acesso (usuario, nivel, liberado)
            VALUES ('$id', '" . $permissao['nivel'] ."', '".$permissao['liberacao']."')";

            $con3 = $bd->getQueryMysql($query);

        }else{

            $query = "UPDATE txc_tb_nivel_acesso SET liberado = '".$permissao['liberacao']."' WHERE usuario = '$id' AND nivel = '" . $permissao['nivel'] ."' ";
            $con3 = $bd->getQueryMysql($query);

        }

    }

    return array("code" => 200, "mensagem" => "Usuário ok!", "type" => "success");


}

function showUserI($usuario)
{

    global $setting;
    $bd = new connect();

    $query = "SELECT * FROM " . $setting::PREFIX_TABELAS . "acesso WHERE id = $usuario LIMIT 1";


    $con = $bd->getQueryMysql($query);

    $retorno = null;
    $usuarios = array();

    if ($con) {
        while ($row = $con->fetch_assoc()) {
            $usuarios[] = array(
                "id" => $row['ID'],
                "nome" => $row['NOME'],
                "usuario" => $row['USUARIO'],
                "token" => $row['SENHA'],
                "permissao" => $row['PERMISSAO'],
                "ultevento" => $row['LOG'],
                "ativo" => $row['ATIVO'],
                "empresa" => $row['EMPRESA']
            );
        }

        $retorno = array("code" => 200, "mensagem" => "Usuários encontrados!", "retorno" => $bd->getCountMysql($con), "dados" => $usuarios);
    } else {
        $retorno = array("code" => 401, "mensagem" => "Sem dados", "dados" => $usuarios);
    }

    return $retorno;
}

function insEmp($usuario, $empresa)
{


    global $ger;
    global $bd;

    $query = "INSERT INTO tb_txc_emp_user (id_user,emp_pat) VALUE('$usuario','$empresa')
            ";



    $con = $bd->getQueryMysql($query);

    if ($con) {
        return array("code" => 200,  "mensagem" => "Empresa adicionada com sucesso!", "type" => "success");
    } else {
        return array("code" => 500,  "mensagem" => "Erro ao inserir empresa", "type" => "error");
    }
}

function delEmp($usuario, $empresa)
{


    global $ger;
    global $bd;

    $query = "DELETE FROM tb_txc_emp_user WHERE id_user = '$usuario' AND emp_pat = '$empresa'
            ";



    $con = $bd->getQueryMysql($query);

    if ($con) {
        return array("code" => 200,  "mensagem" => "Empresa deletada com sucesso!", "type" => "success");
    } else {
        return array("code" => 500,  "mensagem" => "Erro ao deletar empresa", "type" => "error");
    }
}

function showNivel($usuario)
{
    global $ger;
    global $bd;

    $query = "SELECT 
                CONCAT(ttm.principal, ' . ', ttm.subnivel) AS retorno, 
                COALESCE(ttna.liberado, 0) AS libe,
                CASE COALESCE(ttna.liberado, 0)
                    WHEN 0 THEN ''
                    WHEN -1 THEN 'checked'
                END AS ch,
                ttm.id AS id
            FROM 
                txc_tb_menu ttm
            LEFT JOIN 
                txc_tb_acesso tta ON tta.ID = '$usuario' 
            LEFT JOIN  
                txc_tb_nivel_acesso ttna ON ttna.usuario = tta.ID AND ttna.nivel = ttm.id 
            GROUP BY 
                ttm.principal, ttm.subnivel, ttna.liberado, ttm.id
            ORDER BY 
                ttm.principal, ttm.subnivel;
            ";

    $con = $bd->getQueryMysql($query);
    $html = '';

    if ($con) {
        while ($row = $con->fetch_assoc()) {
            $html .= '<div class="checkbox">
                        <label>
                        <input type="checkbox" value="' . $row['id'] . '" ' . $row['ch'] . '>
                        ' . $row['retorno'] . '
                        </label>
                      </div>';
        }
    }

    // Retorna o HTML gerado
    return $html;
}

function showUser($dados)
{
    global $setting;
    $bd = new connect();

    $query = "SELECT * FROM " . $setting::PREFIX_TABELAS . "acesso WHERE NOME LIKE '%$dados%' ORDER BY ATIVO,NOME";

    $con = $bd->getQueryMysql($query);

    $retorno = null;
    $usuarios = array();

    if ($con) {
        while ($row = $con->fetch_assoc()) {
            $usuarios[] = array(
                "id" => $row['ID'],
                "nome" => $row['NOME'],
                "usuario" => $row['USUARIO'],
                "token" => $row['SENHA'],
                "permissao" => $row['PERMISSAO'],
                "ultevento" => $row['LOG'],
                "ativo" => $row['ATIVO'],
                "empresa" => $row['EMPRESA']
            );
        }

        $retorno = array("code" => 200, "mensagem" => "Usuários encontrados!", "retorno" => $bd->getCountMysql($con), "dados" => $usuarios);
    } else {
        $retorno = array("code" => 401, "mensagem" => "Sem dados", "dados" => $usuarios);
    }

    return $retorno;
}
