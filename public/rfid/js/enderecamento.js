$(document).ready(function () {
    $('#formPesquisa').submit(function (e) {
        e.preventDefault();

        var item = $('input[name="valor"]').val();
        var endereco = $('input[name="endereco"]').val();

        $.ajax({
            type: 'POST',
            url: '../../app/src/enderecamento/enderecamento.php?funcao=inseri',
            data: {
                endereco: endereco,
                item: item
            },
            success: function (data) {
                if(data.status == "error") {
                    Swal.fire({
                        title: 'Ops!',
                        text: data.mensagem,
                        icon: data.status,
                        confirmButtonText: 'Ok'
                    });
                } else {
                    retornaReg();
                    $('input[name="valor"]').focus().val('');
                }
            },
            error: function () {
                Swal.fire({
                    title: 'Ops!',
                    text: 'Erro genérico!',
                    icon: 'error',
                    confirmButtonText: 'Ok'
                });
                $('input[name="valor"]').focus();
            }
        });
    });
    $(document).on('click', '.swal2-confirm', function() {
        $('input[name="valor"]').val('').focus();
    });
});



retornaReg();


function imprimirTabela() {
    var tabela = document.getElementById("table_endereco");
    if (tabela) {
        var janelaImpressao = window.open('', '', 'height=1280,width=800');
        janelaImpressao.document.write('<html><head><title>Relatório de Endereço WMS</title></head><body>');
        janelaImpressao.document.write('<h1>Relatório de Endereço WMS</h1>');
        janelaImpressao.document.write(tabela.outerHTML);
        janelaImpressao.document.write('</body></html>');
        janelaImpressao.document.close();
        janelaImpressao.print();
    } else {
        console.error('Tabela não encontrada.');
    }
}

function retornaReg(){

    var endereco = $('input[name="endereco"]').val();

    $.ajax({
        type: 'POST',
        url: '../../app/src/enderecamento/enderecamento.php?funcao=contador',
        data: {
            endereco: endereco
        },
        beforeSend: function () {
            $('#contador').html('Carregando...');
        },
        success: function (data) {
            $('#contador').html(data);
        },
        error: function () {
            alert('Erro ao buscar dados.');
        }
    });

    $.ajax({
        type: 'POST',
        url: '../../app/src/enderecamento/enderecamento.php?funcao=endereco',
        data: {
            endereco: endereco
        },
        beforeSend: function () {
            $('#table_endereco tbody').html('<tr><td colspan="11">Carregando...</td></tr>');
        },
        success: function (data) {
            $('#table_endereco tbody').html(data);
        },
        error: function () {
            alert('Erro ao buscar dados.');
        }
    });

}