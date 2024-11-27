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
                    <h4><i class="fa fa-angle-right"></i> Sugestões de peças em furo de estoque nos pedidos.</h4>
                    <div class="form-panel">
                    <table id = "tabelarfid" class="table table-striped table-advance table-hover">
                    <thead>
                      <tr>
                        <th><i class="fa fa-clock-o"></i> Data Inicial</th>
                        <th><i class="fa fa-clock-o"></i> Data Final</th>
                        <th><i class=" fa fa-edit"></i></th>
                        <th></th>
                      </tr>
                    </thead>
                    <tbody>
                       <tr>
                        <form id="pesquisaSugestao"  role="form" class="form-horizontal style-form" action="javascript:void(0);">
                        <th><input type="date" name = "de" class="form-control" required></input></th>
                        <th><input type="date" name = "ate" class="form-control" required></input></th>
                        <th><button class="btn btn-success btn-lg" type="submit">Buscar</button></th>
                        </form>
                        <th></th>
                      </tr>
                    </tbody>
                  </table>
                    </div>
                </div>
            </div>
        </section>
    </section>
');



$ger->imprimir('
<section id="main-content">
    <section class="wrapper">
        <div class="row mt">
            <div class="col-lg-12">
                <div class="form-panel">
                <table id = "tabelaSugestao" class="table table-striped table-advance table-hover">
                <thead>
                  <tr>
                    <th> Quebra</th>
                    <th> -></th>
                    <th>Sugestão</th>
                  </tr>
                </thead>
                <tbody>
                  
                </tbody>
              </table>
                </div>
            </div>
        </div>
    </section>
</section>
');



$theme->construct_footer();

$ger->imprimir('</section>');
$ger->get_js("js/sugestao.js");
$ger->imprimir('</body>');
