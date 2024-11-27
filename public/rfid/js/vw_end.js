$('#formPesquisa').submit(function (e) {
    e.preventDefault();

    var valor = $('input[name="valor"]').val();
    $.ajax({
        type: 'POST',
        url: '../../app/src/enderecamento/vw_end.php',
        data: {
            endereco: valor
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
});