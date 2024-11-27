<?php

include_once(__DIR__ . "/../../app/src/theme/construct.php");
include_once(__DIR__ . "/../../app/public/gerais.php");



use app\src\theme\construct_theme;
use app\public_\gerais;

$theme = new construct_theme();

$theme->construct_head();

$ger = new gerais();

$ger->imprimir('<body>');
$ger->imprimir('<section id="container">');



$numero = isset($_POST['numero']) ? $_POST['numero'] : null;
$empresa = isset($_POST['empresa']) ? $_POST['empresa'] : null;
$codempresa = isset($_POST['codempresa']) ? $_POST['codempresa'] : null;


if(is_null($numero) or is_null($empresa) or is_null($codempresa)){
    header("Location: impoc.php");
}


$theme->construct_menu();


$ger->imprimir('
        <section id="main-content">
            <section class="wrapper">
                <div class="row mt">
                    <div class="col-lg-12">
                        <h4><i class="fa fa-angle-right"></i> Impressão Ordem de Compra: ' . $numero . ' | Empresa:  ' . $empresa . '</h4>

                        <form id="arqxml">
                            <div class="form-panel">
                                <div class="form-group has-success">
                                    <label class="col-lg-2 control-label">Impressora: ' . $numero . '</label>
                                    <input type="hidden" name = "numero" value = "' . $numero .'"/>
                                    <input type="hidden" name = "empresa" value = "' . $codempresa .'"/>
                                </div>
                            </div>
                        </form>
                        <br>
                        <br>
                        <table name = "tabelaOc" id="tabelaOc" class="table table-striped table-advance table-hover">
                            <thead>
                                <tr>
                                    <th><i class="fa fa-code"></i> Código</th>
                                    <th><i class="fa fa-info-circle"></i> Descrição</th>
                                    <th><i class="fa fa-arrows-alt"></i> Tamanho</th>
                                    <th><i class="fa fa-paint-brush"></i> Cor</th>
                                    <th><i class="fa fa-barcode"></i> EAN13</th>
                                    <th><i class="fa fa-sort-numeric-up"></i> Quantidade</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Conteúdo da tabela -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </section>

    
    ');



$theme->construct_footer();



$ger->imprimir('</section>');

$ger->get_js("js/rfidoc.js");

$ger->imprimir('</body>');
