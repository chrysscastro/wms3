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

if (isset($_POST['endereco'])) {



  $endereco = $_POST['endereco'];



  $ger->imprimir('
    <section id="main-content">
      <section class="wrapper">
        <div class="row mt">
            <div class="col-lg-12">
            <h4><i class="fa fa-angle-right"></i> Endereçamento no Endereço: ' . $endereco . ' </h4>
            <div class="form-panel">
                <form id="formPesquisa" role="form" class="form-horizontal style-form" action="javascript:void(0);">
                <div class="form-group has-success">
                    <label class="col-lg-2 control-label">Item</label>
                    <div class="col-lg-10">
                    <input type="text" placeholder="" id = "valor" name = "valor" class="form-control" autofocus>
                    <input type="hidden" placeholder=""  name = "endereco" value = "' . $endereco . '" class="form-control" autofocus required>
                    <p class="help-block">Insira o Item desejado! Formatos aceito: (Barra28 / BarraCli / Ean13)</p>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-lg-offset-2 col-lg-10">
                    <button class="btn btn-theme" type="submit">Inserir</button>
                    <a class="btn btn-theme" href="vw_end.php">Voltar</a>
                    </div>
                </div>
                </form>

                <div class="form-panel">
                    <button class="btn btn-theme" onclick = "imprimirTabela();">Imprimir</button>
                    <h1 id = "contador" > </h1>
                    
                </div>

                <section id="unseen">
                <table id="table_endereco" class="table table-bordered table-striped table-condensed">
                  <thead>
                    <tr>
                      <th class="numeric">Registro</th>
                      <th class="numeric">Cod. Prod</th>
                      <th>Item</th>
                      <th>Tam</th>
                      <th>Cor</th>
                      <th>Local</th>
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
} else {
  $ger->imprimir('<script>window.location.href = "vw_end.php"</script>');
}

$theme->construct_footer();



$ger->imprimir('</section>');

$ger->get_js("js/enderecamento.js");

$ger->imprimir('</body>');
