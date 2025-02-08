$('#formPesquisa').submit(function (e) {
    e.preventDefault();

    var valor = $('input[name="valor"]').val();
    
    $.ajax({
        type: 'POST',
        url: '../../app/src/enderecamento/tabLocal.php',
        data:{
            endereco: valor
        },
        success: function (data) {
            mensagem(data.type,data.mensagem);
            pesquisar();
            $('input[name="valor"]').val('');
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            mensagem('error',"Erro na requisição AJAX:", textStatus, errorThrown);
            mensagem('error',"Resposta do servidor:", jqXHR.responseText);
        }
    });


});



function  pesquisar() {

    $.ajax({
        type: 'GET',
        url: '../../app/src/enderecamento/tabLocal.php',
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
