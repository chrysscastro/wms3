<?php

use app\database\connect;

    include_once(__DIR__ . "/../../database/database.php");
    include_once(__DIR__ . "/../config/config_system.php");
    include_once(__DIR__ . "/../../public/gerais.php");

    // Configuração do tempo limite e buffer de saída
    set_time_limit(0);
    ob_implicit_flush(true);
    ob_end_flush();


    use app\database;
    use app\public_\gerais;
    use app\config\setting;

    $bd = new connect();
    $setting = new setting();

    @$referencia = $_POST['ref'];

    

    // Consulta SQL no PostgreSQL
    $pgsql_query = "
    SELECT pr.codigo AS artigo,
    pr.descricao AS descricao,
    ROUND(coalesce(tba.preco_00, 0), 2)::numeric AS atacado,
    ROUND(coalesce(tbv.preco_00, 0), 2)::numeric AS varejo,
    CASE pr.ativo
        WHEN 'S' THEN 'sim'
        WHEN 'N' THEN 'não'
    END AS ativo,
    pa.barra28 AS barra28,
    pa.barracli AS barracli,
    pa.barra AS ean13,
    pa.tam AS tam,
    cr.cor AS cor,
    cr.descricao AS desccor,
    gp.codigo AS grupo,
    gp.descricao AS descgrupo,
    c.codigo as codcolecao, 
    c.descricao as colecao
    FROM produto_001 pr
    INNER JOIN pa_iten_001 pa ON (pr.codigo = pa.codigo)
    INNER JOIN colecao_001 cl ON (cl.codigo = pr.colecao)
    INNER JOIN cadcor_001 cr ON (cr.cor = pa.cor)
    INNER JOIN tablin_001 lnn ON (lnn.codigo = pr.linha)
    INNER JOIN grupo_pa_001 gp ON (gp.codigo = pr.grupo)
    INNER JOIN colecao_001 c ON (c.codigo = pr.colecao)
    LEFT JOIN tabpreco_001 tba ON (tba.codigo = pr.codigo AND tba.regiao = '001')
    LEFT JOIN tabpreco_001 tbv ON (tbv.codigo = pr.codigo AND tbv.regiao = '002')
    LEFT JOIN produto_ecom_002 ec ON (ec.codigo = pr.codigo2)
    WHERE 
    pr.ativo = 'S'
    AND pr.descricao like '%$referencia%'
    GROUP BY ec.descricao, ec.desc_detalhada,lnn.descricao,pr.ativo,pr.colecao,pa.barra28,pa.barra,
    pr.codigo, pr.descricao, tba.preco_00, tbv.preco_00, cr.descricao, pa.tam,pr.descricao2,cr.cor,cr.pantone,pa.barracli,gp.codigo,gp.descricao, c.codigo, c.descricao 
    ORDER BY pr.descricao, cr.descricao, pa.tam DESC;
    ";

    

    $pgsql_result = $bd->getQueryPostgres($pgsql_query);
    if (!$pgsql_result) {
        die("Erro ao executar a consulta no PostgreSQL: " . pg_last_error());
    }


    $total_records = $bd->getCountPostgres($pgsql_result);
    $processed_records = 0;

    $mensagem = null;

    //mail("suporte.txc@gmail.com","Inicio Sincronização de Produtos WMS","Inicio da Sincronização de Produtos WMS");

    while ($row = pg_fetch_assoc($pgsql_result)) {
        $artigo = $row['artigo'];
        $descricao = utf8_encode($row['descricao']);
        $atacado = number_format($row['atacado'],2);
        $varejo = number_format($row['varejo'],2);
        $ativo = $row['ativo'];
        $barra28 = $row['barra28'];
        $barracli = $row['barracli'];
        $ean13 = $row['ean13'];
        $tam = $row['tam'];
        $cor = $row['cor'];
        $desccor = $row['desccor'];
        $grupo = $row['grupo'];
        $descgrupo = $row['descgrupo'];
        $codcolecao = $row['codcolecao'];
        $colecao = $row['colecao'];

        // Verificar se o registro existe no MySQL
        $mysql_query = "
            SELECT 1 FROM txc_tb_prod_2
            WHERE artigo = '$artigo' AND cor = '$cor' AND tam = '$tam'
        ";
        

        $stmt = $bd->getQueryMysql($mysql_query);

        if ($stmt->num_rows > 0) {
            
            $mensagem = "(Alterando) $descricao - $tam $cor | Barra28: $barra28 | BarraCli: $barracli | EAN: $ean13 \n";

            $update_query = "
                UPDATE " . $setting::PREFIX_TABELAS . "prod_2
                SET descricao = '$descricao', desccor = '$desccor', barra28 = '$barra28', barracli = '$barracli', ean13 = '$ean13', grupo = '$grupo', 
                descgrupo = '$descgrupo', preco = '$varejo', atacado = '$atacado', colecao = '$colecao', codcolecao = '$codcolecao'
                WHERE artigo = '$artigo' AND cor = '$cor' AND tam = '$tam'
            ";
            try {
                $update = $bd->getQueryMysql($update_query);
            } catch (Exception $e) {
                $mensagem = "(Erro ao alterar) $descricao - $tam $cor | Barra28: $barra28 | BarraCli: $barracli | EAN: $ean13 \n";
            }
        } else {
            
            $mensagem =  "(Inserindo) $descricao - $tam $cor | Barra28: $barra28 | BarraCli: $barracli | EAN: $ean13 \n";
            $insert_query = "
                INSERT INTO " . $setting::PREFIX_TABELAS . "prod_2 (artigo, descricao, cor, desccor, tam, barra28, barracli, ean13, grupo, descgrupo, preco, atacado,colecao,codcolecao)
                VALUES ('$artigo','$descricao','$cor','$desccor','$tam','$barra28','$barracli','$ean13','$grupo','$descgrupo','$varejo','$atacado','$colecao','$codcolecao')
            ";
            try {
                $insert = $bd->getQueryMysql($insert_query);
            } catch (Exception $e) {
                $mensagem =  "(Erro ao inserir) $descricao - $tam $cor | Barra28: $barra28 | BarraCli: $barracli | EAN: $ean13 \n";
            }
            
        }

        $stmt->close();

        //mail("suporte.txc@gmail.com","Fim Sincronização de Produtos WMS","Fim da Sincronização de Produtos WMS");
        $processed_records++;
        $progress = ($processed_records / $total_records) * 100;
        file_put_contents('progress.json', json_encode(['progress' => $progress]));
        file_put_contents('mensagem.txt', $mensagem);
    }

    pg_free_result($pgsql_result);
    pg_close($conn);
    file_put_contents('progress.json', json_encode(['progress' => '0']));
    file_put_contents('mensagem.txt', "Sincronização Finalizada ás " . date('d/M/y h:i:s'));
    $conexao->close();


?>