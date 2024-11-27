<?php

include_once(__DIR__ . "/../../database/database.php");
include_once(__DIR__ . "/../../public/gerais.php");
include_once(__DIR__ . "/../access/check_access.php");

use app\database\connect;
use app\public_\gerais;


$bd = new connect();
$ger = new gerais();


if ($ger->get_metodo() == "GET") {

    $query = "SELECT *
                    FROM " . $setting::PREFIX_TABELAS . "local 
                    WHERE  empresa = '$empresa_geral'
        ";

    $con_endereco = $bd->getQueryMysql($query);

    if ($con_endereco) {
        while ($row = $con_endereco->fetch_assoc()) {

            $ger->imprimir('<tr>');
            $ger->imprimir('<td>' . $row['id'] . '</td>');
            $ger->imprimir('<td>');
            $ger->imprimir('<form action="enderecamento.php" method="post">');
            $ger->imprimir('<input type="hidden" name = "endereco" value = "' . $row['id'] . '" />');
            $ger->imprimir('<button type = "submit"  class = "btn btn-danger" >Excluír</button>');
            $ger->imprimir('</form>');
            $ger->imprimir('</td>');
            $ger->imprimir('</tr>');
        }
    } else {
        $ger->imprimir('<tr>');
        $ger->imprimir('<td>Local não encontrado!</td>');
        $ger->imprimir('</tr>');
    }
} elseif ($ger->get_metodo() == "POST") {
    $endereco = $_POST['endereco'];


    
    $query = "SELECT *
                    FROM " . $setting::PREFIX_TABELAS . "local 
                    WHERE  empresa = '$empresa_geral' AND id = '$endereco'
        ";

    $con = $bd->getQueryMysql($query);

    $ger->doc_json();

    $json = null;

    if ($con) {
        $con_num = $bd->getCountMysql($con);

        if ($con_num > 0) {
            $json = array("type" => "error", "mensagem" => "Já existe este local para esta empresa!");
        } else {
            $query = $query = "INSERT INTO " . $setting::PREFIX_TABELAS . "local (id,descricao,empresa) VALUES ('$endereco','$endereco','$empresa_geral')";
            $con = $bd->getQueryMysql($query);
            if ($con) {
                $json = array("type" => "success", "mensagem" => "Local cadastrado com sucesso!");
            }else{
                $json = array("type" => "error", "mensagem" => "Erro ao cadastrar o Local!");
            }
        }
    }

    echo (json_encode($json));
}
