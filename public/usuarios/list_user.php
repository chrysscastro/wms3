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
                        <h4><i class="fa fa-angle-right"></i> Usuários do sistema</h4>
                        <div class="form-panel">
                        <form method="post" name = "formPesquisa" id = "formPesquisa">
                        <div class="form-group has-success">
                            <label class="col-lg-2 control-label">Nome Completo</label>
                            <div class="col-lg-10">
                            <input type="text" placeholder="" id="f-name" name = "chave" class="form-control">
                            </div>
                        </div>
                        <br>
                        <button type="submit" class="btn btn-primary">Pesquisar</button>
                        </form>
                        <br>
                        <br>
                        <a href = "user.php?val=0" class = "btn btn-success">Novo</a>
                        </div>
                        <table id = "tabelaUsuarios" class="table table-striped table-advance table-hover">
                        <thead>
                          <tr>
                            <th><i class="fa fa-code"></i> Código</th>
                            <th class="hidden-phone"><i class="fa fa-user"></i> Usuário</th>
                            <th><i class="fa fa-id-badge"></i> Nome Completo</th>
                            <th><i class="fa fa-key"></i> Permissão</th>
                            <th><i class="fa fa-calendar-times-o"></i> Último evento</th>
                            <th><i class="fa fa-info-circle"></i> Ativo</th>
                            <th><i class="fa fa-cog"></i> Painel</th>
                            <th></th>
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

$ger->get_js("js/list_user.js");

$ger->imprimir('</body>');
