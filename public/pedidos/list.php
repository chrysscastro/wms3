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
<div id="modalCarregamento" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: #fff; padding: 20px; text-align: center; border-radius: 8px;">
        <p id="statusCarregamento">Iniciando...</p>
        <p>Aguarde enquanto os pedidos estão sendo processados.</p>
    </div>
</div>
');

$ger->imprimir('
 <section id="main-content">
   <section class="wrapper">
      <div class="row mt">
         <div class="col-lg-12">
            <form id = "formPesquisa" class="form-horizontal form-material">
               <h4><i class="fa fa-angle-right"></i> Separação de Pedidos</h4>
               <div class="form-panel">
                  <div class="form-group has-success">
                     <label class="col-lg-2 control-label">Pedido</label>
                     <div class="col-lg-10">
                        <input type="text"  name = "chave" class="form-control">
                     </div>
                  </div>
                  <div class="form-group has-success">
                     <label class="col-lg-2 control-label">Data de Inicio</label>
                     <div class="col-lg-10">
                        <input type="date" id = "dtinicio" name = "dtinicio" class="form-control">
                     </div>
                  </div>
                  <div class="form-group has-success">
                     <label class="col-lg-2 control-label">Data Final</label>
                     <div class="col-lg-10">
                        <input type="date" id = "dtfim" name = "dtfim" class="form-control">
                     </div>
                  </div>
                  <div class="form-group has-success">
                     <label class="col-lg-2 control-label">Impressos</label>
                     <div class="col-lg-10">
                        <select type="text" placeholder="" id="f-name" name = "imp" class="form-control">
                              <option value = "1">Sim</option>
                              <option value = "0">Não</option>
                         </select>
                     </div>
                  </div>
                  <button class="btn btn-theme">Buscar</button>
            </form>
            <button type="button" onclick = "imprimirMassa();" class="btn btn-theme">Imprimir Pedidos</button>
            <label>Ao clicar em separar o pedido ou Imprimir Pedidos, caso ele não tenha o status não impresso no Excia, ele ficara com o Status Impresso.</label>
            <table id = "tabelaPedidos" class="table table-striped table-advance table-hover">
            <thead>
            <tr>
            <th><i class=" fa fa-edit"></i> Representante</th>
            <th class="hidden-phone"><i class="fa fa-clock-o"></i> Data/Hora</th>
            <th><i class="fa fa-key"></i> Pedido</th>
            <th><i class=" fa fa-edit"></i></th>
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

$ger->get_js("js/ped.js");

$ger->imprimir('</body>');
