$(document).ready(function () {

    retorna_iten()

    function retorna_iten()
    {

        // Envio usando o FormData
        var formData = new FormData();
        formData.append('chave', $('input[name="numero"]').val()); 
        formData.append('empresa', $('input[name="empresa"]').val());

        // Envio usando o FormData
        $.ajax({
            type: 'POST',
            url: '../../app/src/rfid/rfidoc.php?funcao=return_item_oc',
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function()
            {
                Swal.fire({
                    title: 'Aguarde...',
                    html: 'Buscando itens ordens de compras.',
                    allowOutsideClick: false,
                    onBeforeOpen: () => {
                    Swal.showLoading(); 
                    },
                    showConfirmButton: false,
                });
            },success: function(data){

                Swal.close();

                console.log(data.dados);

                var items = data.dados;
                    var tbody = $('#tabelaOc tbody');
                    tbody.empty(); 
                    
                    var disable = '';
    
    
                    items.forEach(function(item) {
                        var row = `<tr>
                            <td>${item.codigo}</td>
                            <td>${item.descricao}</td>
                            <td>${item.tam}</td>
                            <td>${item.desc_cor}</td>
                            <td>${item.ean13}</td>
                            <td>${item.qtde}</td>
                        </tr>`;
                        tbody.append(row);
                    });
                


            },error: function () {
                mensagem('error', 'Erro ao buscar dados.');
            }
        });
    }

    


});