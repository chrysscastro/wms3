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

    $chave = isset($_POST['chave']) ? $_POST['chave'] : '';
    $empresa = isset($_POST['empresa']) ? $_POST['empresa'] : '';

    switch ($funcao) {

        case 'cria':

            break;
        case 'return_item_oc':

            $json = return_item_oc($chave, $empresa);
            break;
        case 'return':
            $json = return_oc($chave);
            break;
        default:
            $json = array("status" => "error", "mensagem" => "Função não desconhecida!");
            break;
    }
} else {
    $json = array("status" => "error", "mensagem" => "Função não informada!");
}


$ger->imprimir(json_encode($json, JSON_ERROR_UTF8 | JSON_HEX_QUOT));


function return_item_oc($ordem, $empresa)
{

    global $bd;

    try {

        $empresa = str_pad($empresa, 3, '0', STR_PAD_LEFT);


        $query = "SELECT DISTINCT(OCI.CODIGO) AS CODIGO,
                    prod.descricao AS DESCRICAO,
                    OCI.tam AS TAM,
                    OCI.cor AS COR,
                    COR.descricao AS DESCCOR,
                    COALESCE(IT.BARRA,'SEM EAN') AS EAN13,
                    OCI.qtde AS QTDE,
                    grupo.codigo AS grupo,
                    grupo.descricao AS descgrupo,
                    COALESCE(PRECO.preco_00,0) AS PRECO,
                    IT.barracli AS BARRACLI
                FROM co_iten_$empresa OCI
                INNER JOIN pa_iten_001 IT ON IT.codigo = OCI.codigo
                AND IT.cor = OCI.cor
                AND IT.tam = OCI.tam
                INNER JOIN produto_001 prod ON prod.codigo = OCI.codigo
                INNER JOIN cadcor_001 COR ON COR.cor = OCI.cor
                INNER JOIN grupo_pa_001 grupo ON grupo.codigo = prod.grupo
                LEFT JOIN TABPRECO_001 PRECO ON (IT.CODIGO = PRECO.CODIGO
                                                AND PRECO.DESCRICAO LIKE '%VAREJO%')
                WHERE NUMERO = '$ordem'
                GROUP BY OCI.CODIGO,
                        prod.descricao,
                        OCI.TAM,
                        OCI.COR,
                        COR.DESCRICAO,
                        IT.BARRA,
                        OCI.QTDE,
                        GRUPO.CODIGO,
                        GRUPO.DESCRICAO ,
                        PRECO.PRECO_00,
                        IT.BARRACLI
                ORDER BY
                 	 prod.descricao,
                 	 OCI.COR,
                 	 OCI.TAM ";

        $conn = $bd->getQueryPostgres($query);

        $x = 0;

        if ($conn) {
            $itens = array();

            while ($row = pg_fetch_assoc($conn)) {
                $x = $x + $row['qtde'];

                $itens[] = array(
                    "codigo" => $row['codigo'],
                    "descricao" => $row['descricao'],
                    "tam" => $row['tam'],
                    "cor" => $row['cor'],
                    "desc_cor" => $row['desccor'],
                    "ean13" => $row['ean13'],
                    "qtde" => intval($row['qtde']),
                    "grupo" => $row['grupo'],
                    "desc_grupo" => $row['descgrupo'],
                    "preco" => number_format($row['preco'], 2, ',', '.'),
                    "barracli" => $row['barracli']
                );
            }

            $json = array("code" => 200,"oc" => "$ordem","empresa" => $empresa,"tags" => $x, "dados" => $itens);
        }
    } catch (\Throwable $th) {
        $json = array("code" => 500, "itens" => $itens, "dados" => "");
    }


    return $json;
}

function return_oc($ordem)
{

    $bd = new connect();


    $query = "SELECT 2 AS codempresa,
                'MATRIZ' AS empresa,
                        COMPRA.NUMERO AS NUMERO,
                        FORNECED.NOME AS NOME,
                        CO_ITEN.VALOR AS VALOR,
                        COMPRA.DT_EMISSAO AS DT
                    FROM COMPRA_002 COMPRA
                    LEFT JOIN ENTIDADE_001 FORNECED ON (COMPRA.CODFOR = FORNECED.CODCLI)
                    LEFT JOIN SOL_COMPRA_001 SOL_COMPRA ON (COMPRA.SOLICITACAO = SOL_COMPRA.NUMERO)
                    LEFT JOIN PESSOAL_002 PESSOAL ON (SOL_COMPRA.SOLICITANTE = PESSOAL.CODIGO)
                    LEFT JOIN GRUPO_CLI_001 GRUPO_CLI ON (FORNECED.GRUPO = GRUPO_CLI.CODIGO)
                    LEFT JOIN COLECAO_001 COLECAO ON (COMPRA.COLECAO = COLECAO.CODIGO)
                    INNER JOIN
                    (SELECT CI.NUMERO,
                            SUM(CI.QTDE) QTDE,
                            SUM(CI.QTDE * CI.PRECO) VALOR,
                            SUM(CI.QTDE_B) QTDE_B,
                            SUM((CI.QTDE - CI.QTDE_B) * CI.PRECO) AS VAL_SALDO,
                            CASE
                                WHEN MIN(COALESCE(CI.SITUACAO, 'A')) <> 'B' THEN 'A'
                                ELSE 'B'
                            END AS SITUACAO
                    FROM CO_ITEN_002 CI
                    LEFT JOIN MATERIAL_001 MATERIAL ON (CI.CODIGO = MATERIAL.CODIGO)
                    WHERE 1 = 1
                    GROUP BY CI.NUMERO) AS CO_ITEN ON (COMPRA.NUMERO = CO_ITEN.NUMERO)
                    LEFT JOIN MENSAGEM_001 MOTIVO ON (COMPRA.MOTIVO = MOTIVO.CODMEN)
                    LEFT JOIN CADCEP_001 CADCEP ON (FORNECED.CEP = CADCEP.CEP)
                    LEFT JOIN CIDADE_001 CIDADE ON (CADCEP.COD_CID = CIDADE.COD_CID)
                    WHERE COMPRA.DT_ENTREGA >= '2021-7-1'
                    AND COMPRA.DT_ENTREGA <= '2027-7-31'
                    AND COMPRA.DT_EMISSAO >= '1980-1-1'
                    AND COMPRA.DT_EMISSAO <= '2050-12-31'
                    AND COMPRA.numero = '$ordem'
    

    UNION ALL 
    
    SELECT 3 AS codempresa ,
            'E-COMMERCE' AS empresa,
                        COMPRA.NUMERO AS NUMERO,
                        FORNECED.NOME AS NOME,
                        CO_ITEN.VALOR AS VALOR,
                        COMPRA.DT_EMISSAO AS DT
                    FROM COMPRA_003 COMPRA
                    LEFT JOIN ENTIDADE_001 FORNECED ON (COMPRA.CODFOR = FORNECED.CODCLI)
                    LEFT JOIN SOL_COMPRA_001 SOL_COMPRA ON (COMPRA.SOLICITACAO = SOL_COMPRA.NUMERO)
                    LEFT JOIN PESSOAL_003 PESSOAL ON (SOL_COMPRA.SOLICITANTE = PESSOAL.CODIGO)
                    LEFT JOIN GRUPO_CLI_001 GRUPO_CLI ON (FORNECED.GRUPO = GRUPO_CLI.CODIGO)
                    LEFT JOIN COLECAO_001 COLECAO ON (COMPRA.COLECAO = COLECAO.CODIGO)
                    INNER JOIN
                    (SELECT CI.NUMERO,
                            SUM(CI.QTDE) QTDE,
                            SUM(CI.QTDE * CI.PRECO) VALOR,
                            SUM(CI.QTDE_B) QTDE_B,
                            SUM((CI.QTDE - CI.QTDE_B) * CI.PRECO) AS VAL_SALDO,
                            CASE
                                WHEN MIN(COALESCE(CI.SITUACAO, 'A')) <> 'B' THEN 'A'
                                ELSE 'B'
                            END AS SITUACAO
                    FROM CO_ITEN_004 CI
                    LEFT JOIN MATERIAL_001 MATERIAL ON (CI.CODIGO = MATERIAL.CODIGO)
                    WHERE 1 = 1
                    GROUP BY CI.NUMERO) AS CO_ITEN ON (COMPRA.NUMERO = CO_ITEN.NUMERO)
                    LEFT JOIN MENSAGEM_001 MOTIVO ON (COMPRA.MOTIVO = MOTIVO.CODMEN)
                    LEFT JOIN CADCEP_001 CADCEP ON (FORNECED.CEP = CADCEP.CEP)
                    LEFT JOIN CIDADE_001 CIDADE ON (CADCEP.COD_CID = CIDADE.COD_CID)
                    WHERE COMPRA.DT_ENTREGA >= '2021-7-1'
                    AND COMPRA.DT_ENTREGA <= '2027-7-31'
                    AND COMPRA.DT_EMISSAO >= '1980-1-1'
                    AND COMPRA.DT_EMISSAO <= '2050-12-31'
                    AND COMPRA.numero = '$ordem'
    
    UNION ALL 

    SELECT 4 AS codempresa ,
            'FILIAL 005' AS empresa,
                        COMPRA.NUMERO AS NUMERO,
                        FORNECED.NOME AS NOME,
                        CO_ITEN.VALOR AS VALOR,
                        COMPRA.DT_EMISSAO AS DT
                    FROM COMPRA_004 COMPRA
                    LEFT JOIN ENTIDADE_001 FORNECED ON (COMPRA.CODFOR = FORNECED.CODCLI)
                    LEFT JOIN SOL_COMPRA_001 SOL_COMPRA ON (COMPRA.SOLICITACAO = SOL_COMPRA.NUMERO)
                    LEFT JOIN PESSOAL_004 PESSOAL ON (SOL_COMPRA.SOLICITANTE = PESSOAL.CODIGO)
                    LEFT JOIN GRUPO_CLI_001 GRUPO_CLI ON (FORNECED.GRUPO = GRUPO_CLI.CODIGO)
                    LEFT JOIN COLECAO_001 COLECAO ON (COMPRA.COLECAO = COLECAO.CODIGO)
                    INNER JOIN
                    (SELECT CI.NUMERO,
                            SUM(CI.QTDE) QTDE,
                            SUM(CI.QTDE * CI.PRECO) VALOR,
                            SUM(CI.QTDE_B) QTDE_B,
                            SUM((CI.QTDE - CI.QTDE_B) * CI.PRECO) AS VAL_SALDO,
                            CASE
                                WHEN MIN(COALESCE(CI.SITUACAO, 'A')) <> 'B' THEN 'A'
                                ELSE 'B'
                            END AS SITUACAO
                    FROM CO_ITEN_004 CI
                    LEFT JOIN MATERIAL_001 MATERIAL ON (CI.CODIGO = MATERIAL.CODIGO)
                    WHERE 1 = 1
                    GROUP BY CI.NUMERO) AS CO_ITEN ON (COMPRA.NUMERO = CO_ITEN.NUMERO)
                    LEFT JOIN MENSAGEM_001 MOTIVO ON (COMPRA.MOTIVO = MOTIVO.CODMEN)
                    LEFT JOIN CADCEP_001 CADCEP ON (FORNECED.CEP = CADCEP.CEP)
                    LEFT JOIN CIDADE_001 CIDADE ON (CADCEP.COD_CID = CIDADE.COD_CID)
                    WHERE COMPRA.DT_ENTREGA >= '2021-7-1'
                    AND COMPRA.DT_ENTREGA <= '2027-7-31'
                    AND COMPRA.DT_EMISSAO >= '1980-1-1'
                    AND COMPRA.DT_EMISSAO <= '2050-12-31'
                    AND COMPRA.numero = '$ordem'
                    ";



    $con = $bd->getQueryPostgres($query);



    if ($con) {

        while ($row = pg_fetch_assoc($con)) {
            $ar[] = array(
                "codempresa" => $row['codempresa'],
                "empresa" => $row['empresa'],
                "numero" => $row['numero'],
                "fornecedor" => $row['nome'],
                "valor" => number_format($row['valor'], 2, ',', '.'),
                "data" => (new DateTime($row['dt']))->format('d/m/Y')
            );
        }

        return $ar;
    }
}
