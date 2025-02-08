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
    $datainicio = isset($_POST['datai']) ? $_POST['datai'] : null;
    $datafim = isset($_POST['dataf']) ? $_POST['dataf'] : null;

    switch ($funcao) {

        case 'rtr_quebra':
            $json = rtr_quebra($datainicio, $datafim);
            break;
        default:
            $json = array("status" => "error", "mensagem" => "Função não desconhecida!");
            break;
    }
} else {
    $json = array("status" => "error", "mensagem" => "Função não informada!");
}

$ger->imprimir(json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));





function rtr_quebra($datainicio, $datafim)
{


    global $bd;

    $query = "with tab as( 
SELECT p.numero AS pedido,
       pi2.tam as tam,
       pi2.cor as cor,
       pi2.codigo AS codigo,
       CONCAT(p1.descricao, ' ',c.descricao, ' ', pi2.tam) as  descricao,
       coalesce(sum(QTD_EXP.quantidade), 0) AS exp,
       sum(pi2.qtde) AS qtdepediten,
       sum(pi3.quantidade) AS qtdepa,
       (sum(pi3.quantidade) + coalesce(sum(QTD_EXP.quantidade), 0) - sum(pi2.qtde)) as quebra
FROM ped_iten_003 pi2
INNER JOIN pedido_003 p ON p.numero = pi2.numero
inner join produto_001 p1 on p1.codigo = pi2.codigo
inner join cadcor_001 c on c.cor = pi2.cor
INNER JOIN pa_iten_001 pi3 ON (pi3.codigo = pi2.codigo
                               AND pi3.cor = pi2.cor
                               AND pi3.tam = pi2.tam
                               AND pi3.deposito IN ('0022',
                                                    '0005'))
LEFT JOIN
  (SELECT EXPEDICAO.codigo,
          EXPEDICAO.tam,
          EXPEDICAO.cor,
          SUM(EXPEDICAO.quantidade) AS quantidade
   FROM
     (SELECT PED_ITEN.codigo,
             PED_ITEN.tam,
             PED_ITEN.cor,
             COALESCE(PEDIDO3.qtde, 0) AS quantidade
      FROM PED_ITEN_003 PED_ITEN
      LEFT JOIN PEDIDO3_003 PEDIDO3 ON PEDIDO3.numero = PED_ITEN.numero
      AND PEDIDO3.codigo = PED_ITEN.codigo
      AND PEDIDO3.cor = PED_ITEN.cor
      AND PEDIDO3.tam = PED_ITEN.tam
      AND PEDIDO3.ordem = PED_ITEN.ordem
      WHERE PED_ITEN.qtde > 0) EXPEDICAO
   GROUP BY EXPEDICAO.codigo,
            EXPEDICAO.tam,
            EXPEDICAO.cor) AS QTD_EXP
ON QTD_EXP.codigo = pi3.codigo
AND QTD_EXP.cor = pi3.cor
AND QTD_EXP.tam = pi3.tam
WHERE p.dt_emissao >= '$datainicio'
  AND p.dt_emissao <= '$datafim'
GROUP BY p.numero,
         pi2.codigo,
         pi2.tam,
         pi2.cor,
         p1.descricao,
         c.descricao
)
select * from tab where (exp + qtdepa) < qtdepediten;";

    $con = $bd->getQueryPostgres($query);

    if ($con) {
        $iten = array();

        while ($reg = pg_fetch_assoc($con)) {
            $iten[] = array(
                "pedido" => $reg['pedido'],
                "codigo" => $reg['codigo'],
                "cor" => $reg['cor'],
                "tam" => $reg['tam'],
                "exp" => $reg['exp'],
                "qtdepediten" => $reg['qtdepediten'],
                "qtdepa" => $reg['qtdepa'],
                "descricao" => $reg['descricao'],
                "quebra" => intval($reg['quebra']),
                "url_img" => "https://ti.txc.com.br/apl/v1/barramento/produto/" . $reg['codigo'] . ".jpg"
            );
        }

        $json = array("code" => 200, "mensagem" => "Retorno concluído com sucesso!", "data" => $iten);
    } else {
        $json = array("code" => 404, "mensagem" => "Retorno não concluído com sucesso!");
    }

    return $json;
}
