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
            <h4><i class="fa fa-angle-right"></i> Endereçamento</h4>
            <div class="form-panel">
                <form id="formPesquisa" role="form" class="form-horizontal style-form" action="javascript:void(0);">
                <div class="form-group has-success">
                    <label class="col-lg-2 control-label">Local</label>
                    <div class="col-lg-10">
                    <input type="text" placeholder="" id="f-name" name = "valor" class="form-control">
                    <p class="help-block">Insira o endereço desejado!</p>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-lg-offset-2 col-lg-10">
                    <button class="btn btn-theme" type="submit">Buscar</button>
                    </div>
                </div>
                </form>

                <section id="unseen">
                <table id="table_endereco" class="table table-bordered table-striped table-condensed">
                  <thead>
                    <tr>
                      <th>Endereço</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    
                  </tbody>
                </table>
              </section>

            </div>
            </div>
        
        </div>
       </section>
    </section>
    
    ');



    $theme->construct_footer(); 

    

    $ger->imprimir('</section>');

    $ger->get_js("js/vw_end.js");

    $ger->imprimir('</body>');



?>