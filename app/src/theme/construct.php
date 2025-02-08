<?php

namespace app\src\theme;

include_once(__DIR__ . "/../../public/gerais.php");
include_once(__DIR__ . "/../../database/database.php");
include_once(__DIR__ . "/../config/config_system.php");

use app\public_\gerais;
use app\database\connect;
use app\config\setting;
use \app\public_\seguranca;

$tem = null;

class construct_theme
{

  

  public function construct_head($login = false)
  {




    $ger = new gerais();

    $path = "../../assets/";

    $ger->imprimir('<head>');
    $ger->imprimir('<meta name="viewport" content="width=device-width, initial-scale=1.0">');
    $ger->imprimir('<meta name="description" content="Sistema XISTECH WMS - Desenvolvimento Interno TXC">');
    $ger->imprimir('<meta name="author" content="Chrystopher Castro">');
    $ger->imprimir('<meta name="keyword" content="">');
    $ger->imprimir('<title>LogiControl - WMS</title>');

    //Icones

    $ger->imprimir('<link href="' . $path . 'img/favicon.png" rel="icon">');
    $ger->imprimir('<link href="' . $path . 'img/apple-touch-icon.png" rel="apple-touch-icon">');

    //Importação de CSS 
    $ger->imprimir('<link href="' . $path . 'lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">');
    $ger->imprimir('<link href="' . $path . 'lib/font-awesome/css/font-awesome.css" rel="stylesheet">');
    $ger->imprimir('<link rel="stylesheet" type="text/css" href="' . $path . 'css/zabuto_calendar.css">');
    $ger->imprimir('<link rel="stylesheet" type="text/css" href="' . $path . 'lib/gritter/css/jquery.gritter.css" />');
    $ger->imprimir('<link href="' . $path . 'css/style.css" rel="stylesheet">');
    $ger->imprimir('<link href="' . $path . 'css/style-responsive.css" rel="stylesheet">');


    //JAVA SCRIPT
    $ger->imprimir('<script src="' . $path . 'lib/chart-master/Chart.js"></script>');
    $ger->imprimir('<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>');
    $ger->imprimir('<script src="' . $path . 'functions.js"></script>');
    $ger->imprimir('</head>');
  }


  public function construct_menu()
  {

    $ger = new gerais();
    $sec = new seguranca();

    $path = "../../assets/";

    include_once(__DIR__ . "/../access/check_access.php");

    $usuario = $_COOKIE['iduser_ck'];

    $sec->gravarLog($usuario);

    //HEADER DE CIMA
    $ger->imprimir('<header class="header black-bg">');
    $ger->imprimir('    <div class="sidebar-toggle-box">');
    $ger->imprimir('        <div class="fa fa-bars tooltips" data-placement="right" data-original-title="Toggle Navigation"></div>');
    $ger->imprimir('    </div>');
    $ger->imprimir('    <a href="../home/index.php" class="logo"><b>Logi<span>Control</span></b></a>');
    $ger->imprimir('    <div class="top-menu">');
    $ger->imprimir('        <ul class="nav pull-right top-menu">');
    $ger->imprimir('            <li><a class="logout" href="../login/emp.php?id=' . $usuario . '">Trocar Empresa</a></li>');
    $ger->imprimir('        </ul>');
    $ger->imprimir('        <ul class="nav pull-right top-menu">');
    $ger->imprimir('            <li><a class="logout" href="../login/">Logout</a></li>');
    $ger->imprimir('        </ul>');
    $ger->imprimir('    </div>');
    $ger->imprimir('</header>');

    if (empty($empresa_geral)) {
      header("Location: ../login/");
    }

    global $tem; 
    //CARREGA MENU DINAMICO

    $ger->imprimir('<aside>');
    $ger->imprimir('    <div id="sidebar" class="nav-collapse ">');
    $ger->imprimir('        <p class="name_nome centered"><a href="#"><img src="' . $path . 'img/user_profile.png" class="img-circle" width="80"></a></p>');
    $ger->imprimir('        <h5 class="centered">' . $nome_geral . '</h5>');
    $ger->imprimir('        <p class="centered">Empresa: ' . $empresa_geral . '</p>');
    $ger->imprimir('        <p class="centered" style="margin-bottom: -52px;font-size: 14px;">' . $sec->get_Name_Empresa($empresa_geral) . '</p>');
    $ger->imprimir('        <ul class="sidebar-menu" id="nav-accordion">');

    $tem = $tema_usuario;
    //CARREGA MENUS

    $this->construct_menu_dinamico();


    $ger->imprimir('        </ul>');
    $ger->imprimir('    </div>');
    $ger->imprimir('</aside>');
  }

  function construct_menu_dinamico()
  {
    $ger = new gerais();
    $bd = new connect();

    $usuario = $_COOKIE['iduser_ck'];



    $query_menu = "SELECT men.principal AS principal, men.subnivel AS subnivel, men.href AS href, men.id AS idmst FROM 
            txc_tb_acesso a
            INNER JOIN txc_tb_nivel_acesso nv ON (nv.usuario = a.ID AND nv.liberado = -1)
            INNER JOIN txc_tb_menu men ON (men.id = nv.nivel)
            WHERE a.ID = $usuario AND men.prcp = -1
            ORDER BY men.principal, men.subnivel";

    $query_submenu = null;

    $conn = $bd->getQueryMysql($query_menu);

    if ($conn) {
      $menu_label = null;

      while ($row = $conn->fetch_assoc()) {

        $menu_label = $row['principal'];

        $ger->imprimir('<li class="sub-menu">');
        $ger->imprimir('    <a href="javascript:;">');
        $ger->imprimir('        <i class="fa fa-angle-right"></i>');
        $ger->imprimir('        <span>' . $menu_label . '</span>');
        $ger->imprimir('    </a>');
        $ger->imprimir('        <ul class="sub">');

        $query_submenu = "SELECT men.principal AS principal,
                              men.subnivel AS subnivel,
                              men.href AS href
                        FROM txc_tb_nivel_acesso nv
                        INNER JOIN txc_tb_menu men ON (men.id = nv.nivel)
                        WHERE nv.usuario = '$usuario'
                          AND nv.liberado = -1
                          AND men.principal = '$menu_label' AND men.subnivel <> '' ORDER BY men.subnivel;";



        $conn_sub_menu = $bd->getQueryMysql($query_submenu);

        while ($row_sub = $conn_sub_menu->fetch_assoc()) {
          $subnivel = $row_sub['subnivel'];
          $href = $row_sub['href'];

          $ger->imprimir("            <li><a href='$href'>$subnivel</a></li>");
        }
        $ger->imprimir('        </ul>');
        $ger->imprimir('</li>');
      }
    } else {
      $ger->imprimir('<h5 class="centered"></h5>');
    }
  }


  public function construct_footer()
  {

    $path = "../../assets/";

    

    $setting = new setting();
    $ger = new gerais();

    $ger->imprimir('<footer class="site-footer">');
    $ger->imprimir('    <div class="text-center">');
    $ger->imprimir('        <p>&copy; Copyrights <strong>Textile Xtra Company</strong>. All Rights Reserved | Versão: ' . setting::VERSION_WMS . ' </p>');
    $ger->imprimir('    </div>');
    $ger->imprimir('</footer>');

    //CHAMA JAVASCRIPT
    $ger->imprimir('<script src="' . $path . 'lib/jquery/jquery.min.js"></script>');
    $ger->imprimir('<script src="' . $path . 'lib/bootstrap/js/bootstrap.min.js"></script>');
    $ger->imprimir('<script class="include" type="text/javascript" src="' . $path . 'lib/jquery.dcjqaccordion.2.7.js"></script>');
    $ger->imprimir('<script src="' . $path . 'lib/jquery.scrollTo.min.js"></script>');
    $ger->imprimir('<script src="' . $path . 'lib/jquery.nicescroll.js" type="text/javascript"></script>');
    $ger->imprimir('<script src="' . $path . 'lib/jquery.sparkline.js"></script>');
    $ger->imprimir('<script src="' . $path . 'lib/common-scripts.js"></script>');
    $ger->imprimir('<script type="text/javascript" src="' . $path . 'lib/gritter/js/jquery.gritter.js"></script>');
    $ger->imprimir('<script type="text/javascript" src="' . $path . 'lib/gritter-conf.js"></script>');
    $ger->imprimir('<script src="' . $path . 'lib/sparkline-chart.js"></script>');
    $ger->imprimir('<script src="' . $path . 'lib/zabuto_calendar.js"></script>');

    global $tem; 
    $ger->imprimir("<script>aplicarTema('$tem');</script>");

    $ger->imprimir("

            <script type='application/javascript'> 
            
            
            $(document).ready(function() {
                $('#date-popover').popover({
                  html: true,
                  trigger: 'manual'
                });
                $('#date-popover').hide();
                $('#date-popover').click(function(e) {
                  $(this).hide();
                });
              
                $('#my-calendar').zabuto_calendar({
                  action: function() {
                    return myDateFunction(this.id, false);
                  },
                  action_nav: function() {
                    return myNavFunction(this.id);
                  },
                  ajax: {
                    url: 'show_data.php?action=1',
                    modal: true
                  },
                  legend: [{
                      type: 'text',
                      label: 'Special event',
                      badge: '00'
                    },
                    {
                      type: 'block',
                      label: 'Regular event',
                    }
                  ]
                });
              });


            function myNavFunction(id) {
                $('#date-popover').hide();
                var nav = $('#' + id).data('navigation');
                var to = $('#' + id).data('to');
                console.log('nav ' + nav + ' to: ' + to.month + '/' + to.year);
              }
              </script>
              ");
  }
}
