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

$metodo = $ger->get_metodo();

switch ($metodo) {
    case 'GET':
        $type = isset($_GET['type']) ? $_GET['type'] : '';
        if (!empty($type)) {
            switch ($type) {
                case 'imp':
                    $ped = isset($_GET['ped']) ? $_GET['ped'] : '';
                    $json = get_ped($ped, "$empresa_geral");
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



$data = json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);



$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost/wms2/app/report/rptEspelhoPed.php"); // Atualize para o domínio/caminho correto
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo "Erro no cURL: " . curl_error($ch);
} else {
    echo $response; 
}

curl_close($ch);


function get_ped($ped, $empresa)
{
    global $bd;



    $query = "SELECT DISTINCT
                pi2.codigo AS codigo,
                p.descricao AS descricao,
                pi3.tam AS tam,
                c.descricao AS cor_descricao,
                CASE 
                    WHEN pi3.barra28 IS NOT NULL THEN pi3.barra28
                    WHEN pi3.barra IS NOT NULL THEN pi3.barra
                    WHEN pi3.barracli IS NOT NULL THEN pi3.barracli 
                    ELSE NULL
                END AS barra,
                (pi2.qtde + pi2.qtde_f) AS quantidade,
                pe.numero AS pedido,
                e.nome AS cliente,
                e.cnpj AS cpf,
                pe.dt_emissao AS emissao
            FROM 
                ped_iten_$empresa pi2
            INNER JOIN produto_001 p 
                ON p.codigo = pi2.codigo
            INNER JOIN pa_iten_001 pi3 
                ON pi3.codigo = pi2.codigo 
                AND pi3.cor = pi2.cor 
                AND pi3.tam = pi2.tam
            INNER JOIN pedido_$empresa pe
                ON pe.numero = pi2.numero 
            INNER JOIN entidade_001 e 
                ON e.codcli = pe.codcli 
            INNER JOIN cadcor_001 c 
                ON c.cor = pi2.cor
            WHERE 
                pi2.numero = '$ped';";


    $con = $bd->getQueryPostgres($query);

    if ($con) {
        $pedido_data = null;
        $itens = [];

        $ped_ped = null;

        while ($reg = pg_fetch_assoc($con)) {
            // Montar os dados do pedido/cliente na primeira iteração
            if ($pedido_data === null) {
                $pedido_data = [
                    "pedido" => $reg['pedido'],
                    "cpf" => $reg['cpf'],
                    "cliente" => mb_convert_encoding($reg['cliente'], 'UTF-8', 'ISO-8859-1'),
                    "data" => $reg['emissao']
                ];
            }
            $ped_ped = $reg['pedido'];

            // Adicionar itens ao array
            $itens[] = [
                "codigo" => $reg['codigo'],
                "nome" => mb_convert_encoding($reg['descricao'], 'UTF-8', 'ISO-8859-1'),
                "tamanho" => $reg['tam'],
                "cor" => $reg['cor_descricao'],
                "quantidade" => (int)$reg['quantidade'],
                "barra" => $reg['barra'],
                "local" => rtr_local($reg['barra'])
            ];
        }

        // Estrutura final
        $result = [
            "pedido" => $pedido_data,
            "itens" => $itens
        ];

        $query2 = "UPDATE pedido_$empresa SET IMPRESSO = 1 WHERE NUMERO = '$ped_ped' ";
        $con2 = $bd->getQueryPostgres($query2);

        return $result;
    } else {
        return [];
    }
}



function rtr_local($item)
{
    global $bd;

    $query = "SELECT
                BIP.CTG AS CTG,
                COUNT(BIP.CTG) AS CONTADOR
            FROM
                txc_tb_prod_2 PROD
                INNER JOIN txc_tb_bip BIP ON (
                    BIP.ITEM = PROD.barra28 OR 
                    BIP.ITEM = PROD.barracli OR 
                    BIP.ITEM = PROD.EAN13
                ) 
                AND BIP.separado <> 1 and BIP.empresa = '004' AND (BIP.CTG LIKE 'CX%' OR BIP.CTG LIKE 'R%')
            WHERE
                PROD.barra28 = '$item' OR 
                PROD.barracli = '$item' OR 
                PROD.EAN13 = '$item'
            GROUP BY
                BIP.CTG;"; // Ajustei o GROUP BY para focar apenas na coluna CTG

    $con = $bd->getQueryMysql($query);

    if ($con) {
        $locais = [];

        while ($reg = mysqli_fetch_assoc($con)) {
            // Montar cada local no formato "CTG (CONTADOR)"
            $locais[] = $reg['CTG'] . " (" . $reg['CONTADOR'] . ")";
        }

        // Retorna os locais como uma string concatenada
        return implode(", ", $locais);
    }


    return "";
}
