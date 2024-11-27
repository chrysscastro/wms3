<?php

include_once(__DIR__ . "/../../database/database.php");
include_once(__DIR__ . "/../../public/gerais.php");
include_once(__DIR__ . "/../config/config_system.php");

use app\database\connect;
use app\public_\gerais;
use app\config\setting;
use app\public_\seguranca;

$db = new connect;
$ger = new gerais();
$setting = new setting();


$path_souce = realpath(__DIR__ . "/../../files/xml/");
$usuario = $_COOKIE['user_ck'];

if ($_GET['tipo'] == "upload") {
    if (isset($_FILES['arquivo'])) {
        $arquivo = $_FILES['arquivo']['tmp_name'];
        $name_arquivo = $_FILES['arquivo']['name'];

        $query = "INSERT INTO " . $setting::PREFIX_TABELAS . "xml (arquivo,usuario,processado) VALUES ('$name_arquivo','$usuario',0)";
        $db->getQueryMysql($query);


        $path_destiny = $path_souce . "/" . $name_arquivo;

        if (move_uploaded_file($arquivo, $path_destiny)) {
            echo "Arquivo enviado com sucesso";
        } else {
            echo "Erro ao enviar o arquivo";
        }
    } else {
        echo "Arquivo não enviado";
    }
} elseif ($_GET['tipo'] == "return") {

    $query = "SELECT arquivo as arquivo, case processado
                                             when '0' then 'À enviar'
                                             when '1' then 'Enviado'
                                             when '2' then 'Falha'
                                             when '3' then 'Processando'
                                             when '4' then 'Capturando Itens'
                                             end  as processado
                                     FROM " . $setting::PREFIX_TABELAS . "xml WHERE usuario = '$usuario' and processado <> 1 and processado <> 2";
    $con = $db->getQueryMysql($query);

    if ($con) {
        while ($reg = $con->fetch_assoc()) {
            $ger->imprimir("<tr>");
            $ger->imprimir("<td>" . $reg['arquivo'] . "</td>");
            $ger->imprimir("<td>" . $reg['processado'] . "</td>");
            $ger->imprimir("</tr>");
        }
    }
} elseif ($_GET['tipo'] == "processa") {


    $ger->doc_json();

    $query = "SELECT * FROM "  . $setting::PREFIX_TABELAS .  "xml WHERE usuario = '$usuario' and processado = 0";

    $con = $db->getQueryMysql($query);

    if ($con) {
        while ($row = $con->fetch_assoc()) {
            $produtos = lerProdutosDeNFe($path_souce . "/" . $row['arquivo']);
            if (is_array($produtos)) {
                altera_status($row['id'], 4);
                foreach ($produtos as $produto) {
                    $query = "INSERT INTO " . $setting::PREFIX_TABELAS . "xml_itens (codigo,descricao,qnt,preco,auxiliar,ean13,data_hora) values ('" . $produto['codigo'] . "',
                        '" . $produto['descricao'] . "','" . $produto['quantidade'] . "','" . $produto['valor'] . "','" . $row['id'] . "','" . $produto['ean13'] . "',NOW())";
                    $db->getQueryMysql($query);
                }
                altera_status($row['id'], 3);
            } else {
                altera_status($row['id'], 2);
            }
        }
        criadocumento();
    }
} elseif ($_GET['tipo'] == "limpa") {
    $query = "DELETE FROM " . $setting::PREFIX_TABELAS . "xml WHERE usuario = '$usuario' AND processado <> 1";
    $db->getQueryMysql($query);

    $return = array("status" => "success", "mensagem" => "Fila limpa com sucesso!");

    echo json_encode($return);
}


function criadocumento()
{

    $db = new connect;
    $setting = new setting();
    $json_retorno = null;
    $usuario = $_COOKIE['user_ck'];

    $query = "SELECT ttp.artigo as artigo,ttp.preco as preco, ttp.cor as cor, ttp.desccor as desccor, 
                ttp.tam as tam, ttp.descgrupo as descgrupo, ttp.barracli as barracli,
                ttp.artigo as artigo, ttp.descricao as descitem, ttp.ean13 as ean13, sum(ttxi.qnt) as qnt, ttx.id as ids
                FROM " . $setting::PREFIX_TABELAS . "xml ttx 
                inner join " . $setting::PREFIX_TABELAS . "xml_itens ttxi on (ttxi.auxiliar = ttx.id)
                inner join " . $setting::PREFIX_TABELAS . "prod_2 ttp on (ttp.barra28 = ttxi.codigo)
                WHERE (ttx.usuario = '$usuario' and ttx.processado = 3)
                GROUP BY ttp.preco, ttp.cor, ttp.desccor, 
                ttp.tam, ttp.descgrupo, ttp.barracli,
                ttp.artigo, ttp.descricao, ttp.ean13, ttx.id,ttp.artigo;";



    $con = $db->getQueryMysql($query);

    $json_envia_lote =  array(
        "quantidadeVolumes" => null,
        "motivo" => "Motivo: Importação de XML de NF",
        "itens" => array()
    );
    $dataEmissao = date('Y-m-d') . "T00:00:00";
    $dataatualref = null;

    $itens = array();

    $ultid = null;

    if ($con) {
        while ($row = $con->fetch_assoc()) {


            $dataatualref = date('Y-m-d H:i:s');

            $qtde = intval($row['qnt']);

            $item = array(
                "caracteristicas" => array(
                    array(
                        "codigoIntegracao" => "preco-" . $row['preco'],
                        "descricao" => $row['preco'],
                        "identificadorClassificacao" => "preco"
                    ),
                    array(
                        "codigoIntegracao" => "cor-" . $row['cor'],
                        "descricao" => $row['cor'],
                        "identificadorClassificacao" => "cor"
                    ),
                    array(
                        "codigoIntegracao" => "descricaoCor-" . $row['desccor'],
                        "descricao" => $row['desccor'],
                        "identificadorClassificacao" => "descricaoCor"
                    ),
                    array(
                        "codigoIntegracao" => "tamanho-" . $row['tam'],
                        "descricao" => $row['tam'],
                        "identificadorClassificacao" => "tamanho"
                    ),
                    array(
                        "codigoIntegracao" => "grupo-" . $row['descgrupo'],
                        "descricao" => $row['descgrupo'],
                        "identificadorClassificacao" => "grupo"
                    ),
                    array(
                        "codigoIntegracao" => "barracli-" . $row['barracli'],
                        "descricao" => $row['barracli'],
                        "identificadorClassificacao" => "barracli"
                    )
                ),
                "cfop" => null,
                "codigoIntegracao" => null,
                "codigoObjeto" => $row['artigo'],
                "descricao" => $row['descitem'],
                "identificadorTipoObjeto" => "Produto acabado",
                "leiauteImpressao" => array("identificador" => "EtiquetaProdutoAcabado"),
                "lote" => array(
                    "codigo" => "000000",
                    "codigoIntegracao" => "000000"
                ),
                "naturezaOperacao" => null,
                "parteCodigo" => null,
                "parteDescricao" => "GERAL",
                "partePrincipal" => true,
                "quantidade" => $qtde,
                "rastreavel" => true,
                "sku" => $row['ean13'],
                "skuCodigoIntegracao" => $row['ean13'],
                "skuOrigem" => null,
                "tags" => array(),
                "unidadeMedida" => "UNIDADE"
            );

            $itens[] = $item;

            altera_status($row['ids'], 1);

            $ultid = $row['ids'];
        }
    }

    $json_envia_lote['itens'] = $itens;

    $ger = new gerais();

    $impressoras = $ger->get_imp_user($usuario);

    $json_envia_lote += array(
        "numero" => $ultid,
        "impressoraPadrao" => array("identificador" => $impressoras['impressora']),
        "dataEmissao" => $dataEmissao,
        "uuid" => null,
        "linhaProducao" => null,
        "tipoDocumento" => array(
            "codigoIntegracao" => "entrada-nf",
            "identificador" => "entrada-nf"
        ),
        "receptor" => array(
            "tipo" => "UNIDADE",
            "codigoIntegracao" => null,
            "nome" => "Textile Xtra CO. Ltda",
            "cnpj" => "42548082000153",
            "razaoSocial" => "Textile Xtra CO. Ltda"
        ),
        "dataEntrada" => $dataEmissao,
        "leiauteImpressao" => array(
            "identificador" => $impressoras['layout']
        ),
        "codigoIntegracao" => $ultid . '/1',
        "inconformidades" => array(),
        "emissor" => array(
            "tipo" => "UNIDADE",
            "codigoIntegracao" => null,
            "nome" => "Textile Xtra CO. Ltda",
            "cnpj" => "42548082000153",
            "razaoSocial" => "Textile Xtra CO. Ltda"
        ),
        "operacao" => array(
            "codigoIntegracao" => "entrada",
            "identificador" => "entrada"
        ),
        "serie" => "1",
        "versoes" => array(),
        "id" => null,
        "destino" => array(
            "codigoIntegracao" => "0005",
            "uuid" => null,
            "tipoLocalizacao" => "DEPOSITO",
            "descricao" => "COLEÇÃO"
        )
    );

    $json_envia_lote = json_encode($json_envia_lote, JSON_HEX_AMP);

    $con_in = $db->getQueryMysql("INSERT INTO " . $setting::PREFIX_TABELAS . "job_serv (datacria,tipo,status,chave,serie)
        values ('$dataEmissao','entrada-nf','PENDENTE','$ultid','1')");

    $ultimoIdDoc = $db->getInsertId();

    $con_in = $db->getQueryMysql("INSERT INTO " . $setting::PREFIX_TABELAS . "job_mov (json,movimento) values ('$json_envia_lote','$ultimoIdDoc')");

    $con_inp = $db->getQueryMysql("INSERT INTO  " . $setting::PREFIX_TABELAS . "impressao_rfid (local,serie,data,usuario) 
        values ('$ultid','1','$dataEmissao','$usuario')");

    $json_retorno = array("status" => "success", "mensagem" => "Documento criados à partir de arquivos XML's");

    $ger->imprimir(json_encode($json_retorno));
}

function altera_status($id, $status)
{

    $db = new connect;
    $setting = new setting();



    $query = "UPDATE " . $setting::PREFIX_TABELAS . "xml SET processado = '$status' WHERE id = '$id'";
    $db->getQueryMysql($query);
}


function lerProdutosDeNFe($caminhoXml)
{
    if (!file_exists($caminhoXml)) {
        return "Arquivo XML não encontrado.";
    }

    // Carrega o XML
    $xml = simplexml_load_file($caminhoXml);

    if ($xml === false) {
        return "Erro ao carregar o XML.";
    }

    $produtos = [];

    // Navegar até a seção de produtos
    foreach ($xml->NFe->infNFe->det as $det) {
        $produto = [
            'codigo' => (string)$det->prod->cProd,
            'descricao' => (string)$det->prod->xProd,
            'quantidade' => (float)$det->prod->qCom,
            'valor' => (float)$det->prod->vProd,
            'ean13' => (float)$det->prod->cEANTrib
        ];
        $produtos[] = $produto;
    }

    return $produtos;
}
