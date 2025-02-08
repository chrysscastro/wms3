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
                        <h4><i class="fa fa-angle-right"></i> Fila de Impressão RFID</h4>
                        <div class="form-panel">
                        <div class="form-group has-success">
                            <label class="col-lg-2 control-label">Chave</label>
                            <div class="col-lg-10">
                            <input type="text" placeholder="" id="f-name" name = "chave" class="form-control">
                            </div>
                        </div>
                        <br>
                        <br>
                        <div class="form-group has-success">
                            <label class="col-lg-2 control-label">Status</label>
                            <div class="col-lg-10">
                            <select type="text" placeholder="" id="f-name" name = "status" class="form-control">
                              <option value = "">Todos...</option>
                              <option value = "FALHA">Em falha</option>
                              <option value = "ENVIANDO">Enviando</option>
                              <option value = "ENVIADOS">Enviados</option>
                            </select>
                            </div>
                        </div>
                        <br>
                        <br>
                        <div class="form-group has-success">
                            <label class="col-lg-2 control-label">Tipo de Documento</label>
                            <div class="col-lg-10">
                            <select type="text" placeholder="" id="f-name" name = "tipo" class="form-control">
                              <option value = "">Todos...</option>
                              <option value = "LEGADO">Legado</option>
                              <option value = "LICENCIADOS">Licenciados</option>
                              <option value = "OC">Ordem de Compra</option>
                              <option value = "entrada-nf">XML NF-E</option>
                            </select>
                            </div>
                        </div>
                        <br><br>
                        <div class="form-group has-success">
                            <label for="quantidade" class="col-lg-2 control-label">Retorno</label>
                            <div class="col-lg-10">
                                <div class="d-flex align-items-center">
                                    <input type="range" class="form-control-range mr-3" id="quantidade" name="quantidade" min="10" max="1000" value="100" oninput="quantidadeOutput.value = quantidade.value">
                                    <output id="quantidadeOutput" style="width: 50px; text-align: center;">100</output>
                                </div>
                            </div>
                        </div>
                        <br><br>
                        <label>Todos os documentos que estão em falha ficam disponíveis no prazo de 4 horas! Após este prazo ele é automaticamente excluído.</label>
                        <table id = "tabelarfid" class="table table-striped table-advance table-hover">
                        <thead>
                          <tr>
                            <th><i class="fa fa-bullhorn"></i> Tipo</th>
                            <th class="hidden-phone"><i class="fa fa-clock-o"></i> Data/Hora</th>
                            <th><i class="fa fa-key"></i> Chave</th>
                            <th><i class=" fa fa-edit"></i> Série</th>
                            <th><i class=" fa fa-signal"></i> Status</th>
                            <th><i class=" fa fa-user"></i> Usuário</th>
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

    $ger->get_js("js/rfid.js");

    $ger->imprimir('</body>');



?>