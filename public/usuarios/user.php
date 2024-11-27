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
            <h4><i class="fa fa-angle-right"></i> Paínel do Usuário</h4>
            
            <div class="form-panel">
                <form id="formPesquisa" role="form" class="form-horizontal style-form" action="javascript:void(0);">

                <div class="container">
                    <div class="form-group has-success">
                        <label class="col-lg-2 control-label">Nome do Usuário</label>
                        <div class="col-lg-10">
                        <input type="text" placeholder="" id="usuario" name = "usuario" class="form-control">
                        <p class="help-block">Este é o nome de usuário necessário para Login</p>
                        </div>
                    </div>
                </div>

                <div class="container">
                    <div class="form-group has-success">
                        <label class="col-lg-2 control-label">Nome completo</label>
                        <div class="col-lg-10">
                        <input type="text" placeholder="" id="nome" name = "nome" class="form-control" required>
                        <p class="help-block">Este é o nome de Exibição para o usuário</p>
                        </div>
                    </div>
                </div>

                <div class="container">
                    <div class="form-group has-success">
                        <label class="col-lg-2 control-label">Senha</label>
                        <div class="col-lg-10">
                        <input type="password" placeholder="" id="senha" name = "senha" class="form-control" required>
                        <p class="help-block">Senha do usuário</p>
                        </div>
                    </div>
                </div>

                <div id="userOk">


                    <div class="container">
                        <div class="form-group has-success">
                            <label class="col-lg-2 control-label">Selecione uma Empresa</label>
                            <div class="col-lg-10">
                                <select name = "empresa" id = "empresa" class="form-control">

                                </select>
                                <br>
                                <button class="btn btn-theme" onclick="addEmp();">Adicionar</button>
                            </div>
                        </div>
                    </div>

                    <div class="container">
                        <div class="form-group has-success">
                            <div class="col-lg-10">
                                <table id = "tabelaEmpresa" class="table table-striped table-advance table-hover">
                                    <thead>
                                    <tr>
                                        <th><i class="fa fa-code"></i> Código</th>
                                        <th class="hidden-phone"><i class="fa fa-user"></i> Razão Social</th>
                                        <th><i class="fa fa-id-badge"></i> CNPJ</th>
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
               
                </div>

                <div class="container">
                    <div class="form-group has-success">
                        <label class="col-lg-2 control-label">Tipo de Usuário</label>
                        <div class="col-lg-10">
                            <label class="col-lg-2 control-label"></label>
                            <select id = "tipo" name = "tipo" class="form-control">
                                <option value = "0">Padrão</option>
                                <option value = "1">Administrador</option>
                            </select>
                        </div>
                    </div>
                </div>   
                
                <div class="container">
                    <div class="form-group has-success">
                        <label class="col-lg-2 control-label">Permissões</label>
                        <div class="col-lg-10">
                            <div class="container" id="permissao">


                            </div>
                        </div>
                    </div>
                </div>   

                <div class="container">
                    <div class="form-group has-success">
                        <div class="col-lg-10">
                            <button class="btn btn-theme" type="submit">Gravar</button>
                        </div>
                    </div>
                </div>  


                </form>

                

            </div>
            </div>
        
        </div>
       </section>
    </section>
    
    ');



$theme->construct_footer();



$ger->imprimir('</section>');

$ger->get_js("js/user.js");

$ger->imprimir('</body>');
