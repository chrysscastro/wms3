<?php

ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);
set_time_limit(600);

@$descricao = $_GET["descricao"];
@$cor = $_GET["cor"];
@$tamanho = $_GET["tamanho"];
@$grupo = $_GET["grupo"];
@$colecao = $_GET["colecao"];
@$desccolecao = $_GET["desccolecao"];


$referencia = null;



$retorno = null;
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $tokenRequisicao = isset(apache_request_headers()["Authorization"])
        ? trim(
            str_replace("Bearer", "", apache_request_headers()["Authorization"])
        )
        : "";

    if ($tokenRequisicao) {
        $conexao = mysqli_connect(
            "apl.txc.com.br",
            "txc",
            "Ti@2015!*",
            "DW",
            3306
        );


        $conexaowms = mysqli_connect(
            "apl.txc.com.br",
            "txc",
            "Ti@2015!*",
            "txc_db_wms",
            3306
        );

        if (!$conexao) {
            die("Erro de conexão: " . mysqli_connect_error());
        }

        $query = "SELECT * FROM USUARIOS where token = '$tokenRequisicao'";
        $result = mysqli_query($conexao, $query);
        if (mysqli_num_rows($result) > 0) {
            $query = "SELECT * FROM txc_tb_prod_2 WHERE descricao LIKE '%$descricao%' and cor LIKE '%$cor%' 
             AND tam LIKE '%$tamanho%' AND  grupo LIKE '%$grupo%' AND codcolecao LIKE '%$colecao%' AND colecao LIKE '%$desccolecao%';";
            $result = mysqli_query($conexaowms, $query);

            $itens = [];

            if ($result) {
                $con_num = mysqli_num_rows($result);

                if ($con_num > 0) {
                    date_default_timezone_set("America/Sao_Paulo");
                    $retorno = [
                        "code" => 201,
                        "mensagem" => "Consulta realizada com sucesso!",
                        "count" => $con_num,
                        "retorno" => [],
                    ];
                    while ($registro = mysqli_fetch_assoc($result)) {
                        $artigo = $registro["artigo"];
                        $descricao = $registro["descricao"];
                        $cor = $registro["cor"];
                        $desccor = $registro["desccor"];
                        $tam = $registro["tam"];
                        $barra28 = $registro["barra28"];
                        $barracli = $registro["barracli"];
                        $ean13 = $registro["ean13"];
                        $grupo = $registro["grupo"];
                        $descgrupo = $registro["descgrupo"];
                        $preco = $registro["preco"];
                        @$colecao = $registro["colecao"];
                        @$codcolecao = $registro["codcolecao"];

                        $pl = explode(' ', $descricao);
                        $referencia = end($pl);
                        $imagem = "http://ti.txc.com.br/apl/v1/barramento/produto/$artigo.jpg";
                        $imagemteceo = "http://ti.txc.com.br/teceo/" . $artigo . "/" . $referencia . "_" . $cor . "_001.jpg";


                        $item = [
                            "artigo" => "$artigo",
                            "descricao" => "$descricao",
                            "cor" => "$cor",
                            "desccor" => "$desccor",
                            "referencia" => "$referencia",
                            "tam" => "$tam",
                            "barra28" => "$barra28",
                            "barracli" => "$barracli",
                            "ean13" => "$ean13",
                            "grupo" => "$grupo",
                            "descgrupo" => "$descgrupo",
                            "preco" => "$preco",
                            "colecao" => "$codcolecao",
                            "desccolecao" => "$colecao",
                            "imagem" => "$imagem",
                            "imagem_teceo" => "$imagemteceo",
                        ];
                        $itens[] = $item;
                    }

                    $retorno["retorno"] = $itens;
                } else {
                    $retorno = [
                        "code" => 400,
                        "mensagem" => "Nenhum registro encontrado!",
                    ];
                }

                mysqli_free_result($result);
            } else {
                $retorno = [
                    "code" => 500,
                    "mensagem" => "Erro na consulta SQL",
                    "conteudo" => ["erro" => mysqli_error($conexao)],
                ];
            }

            mysqli_close($conexao);
        } else {
            $retorno = ["code" => 401, "mensagem" => "Token Inválido!"];
        }
    } else {
        // Token ausente
        http_response_code(401);
        $retorno = ["code" => 401, "mensagem" => "Token ausente"];
    }
} else {
    http_response_code(400);
    $retorno = ["code" => 400, "mensagem" => "Requisição inválida!"];
}
$retorno = json_encode($retorno, JSON_UNESCAPED_SLASHES);
header("Content-type: application/json");
echo $retorno;
