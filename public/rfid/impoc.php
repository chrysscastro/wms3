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




$theme->construct_menu();


$ger->imprimir('
        <section id="main-content">
            <section class="wrapper">
                <div class="row mt">
                    <div class="col-lg-12">
                        <h4><i class="fa fa-angle-right"></i> Impressão via Ordem de Compra (Excia)</h4>
                        <form id="arqxml">
                            <div class="form-panel">
                                <div class="form-group has-success">
                                    <label class="col-lg-2 control-label">Nº da Ordem de compra</label>
                                    <div class="col-lg-10">
                                        <input type="text" placeholder="" id="f-name" name="valor" class="form-control" required autofocus>
                                    </div>
                                </div>
                                <button class="btn btn-success btn-lg" type="submit">Buscar</button>
                            </div>
                        </form>
                        <br>
                        <br>
                        <table name = "tabelaOc" id="tabelaOc" class="table table-striped table-advance table-hover">
                            <thead>
                                <tr>
                                    <th><i class="fa fa-building"></i> Empresas</th>
                                    <th><i class="fa fa-hashtag"></i> Número</th>
                                    <th><i class="fa fa-id-badge"></i> Nome</th>
                                    <th><i class="fa fa-dollar-sign"></i> Valor</th>
                                    <th><i class="fa fa-calendar-alt"></i> Data</th>
                                    <th><i class="fa fa-cogs"></i> Ação</th>
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

$ger->get_js("js/impoc.js");

$ger->imprimir('</body>');
