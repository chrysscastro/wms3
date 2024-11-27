<!DOCTYPE html>
<?php

include_once(__DIR__ . "/../../app/src/theme/construct.php");
include_once(__DIR__ . "/../../app/public/gerais.php");


use app\src\theme\construct_theme;
use app\public_\gerais;

$ger = new gerais();
$theme = new construct_theme();
$theme->construct_head(true);

?>


<body>

    <div id="login-page">
        <div class="container">
            <form class="form-login" id="formPesquisa" action="javascript:void(0);">
                <h2 class="form-login-heading">Selecione a Empresa desejada!</h2>
                <div class="login-wrap">
                    <select type="text" class="form-control" id="empresa" name="empresa" placeholder="Selecione uma empresa desejada" autofocus required>

                    </select>
                    <br>
                    <button class="btn btn-theme btn-block" type="submit"><i class="fa fa-continue"></i> Continue</button>
                    <hr>

                </div>

            </form>
        </div>
    </div>
    <!-- js placed at the end of the document so the pages load faster -->
    <script src="../../assets/lib/jquery/jquery.min.js"></script>
    <script src="../../assets/lib/bootstrap/js/bootstrap.min.js"></script>

    <!--BACKSTRETCH-->
    <!-- You can use an image of whatever size. This script will stretch to fit in any screen size.-->
    <script type="text/javascript" src="../../assets/lib/jquery.backstretch.min.js"></script>
    <script type="text/javascript" src="js/emp.js"></script>
    <script>
        $(document).ready(function() {
            var unique_id = $.gritter.add({
                // (string | mandatory) the heading of the notification
                title: 'Bem vindo a nova versão do WMS!',
                // (string | mandatory) the text inside the notification
                text: 'Versão 3.0.',
                // (string | optional) the image to display on the left
                image: 'https://i.ibb.co/c15Mht4/logo.png',
                // (bool | optional) if you want it to fade out on its own or just sit there
                sticky: false,
                // (int | optional) the time you want it to be alive for before fading out
                time: 8000,
                // (string | optional) the class name you want to apply to that specific message
                class_name: 'my-sticky-class'
            });

            return false;
        });
        $.backstretch("../../assets/img/wms.jpg", {
            speed: 1000
        });
    </script>
</body>

</html>