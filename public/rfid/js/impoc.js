$(document).ready(function () {


    $('#arqxml').submit(function(e) {
        e.preventDefault();

        var formData = new FormData();
        formData.append('chave', $('input[name="valor"]').val()); // Adiciona o valor do input 'valor'

        // Envio usando o FormData
        var formData = new FormData();
        formData.append('chave', $('input[name="valor"]').val()); // Adiciona o valor do input 'valor'

        // Envio usando o FormData
        $.ajax({
            type: 'POST',
            url: '../../app/src/rfid/rfidoc.php?funcao=return',
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function()
            {
                Swal.fire({
                    title: 'Aguarde...',
                    html: 'Buscando ordens de compras.',
                    allowOutsideClick: false,
                    onBeforeOpen: () => {
                      Swal.showLoading(); 
                    },
                    showConfirmButton: false,
                  });
            },success: function (data) {
                
                Swal.close();

                var items = data;
            
                var tbody = $('#tabelaOc tbody'); 
                tbody.empty();
            
                // Adiciona os itens na tabela
                items.forEach(function(item) {
                    var row = `
                        <tr>
                            <td>${item.empresa}</td>
                            <td>${item.numero}</td>
                            <td>${item.fornecedor}</td>
                            <td>${item.valor}</td>
                            <td>${item.data}</td>
                            <td>
                                <form method = "post"  action = "rfidoc.php">
                                    <input type="hidden" name="numero" value = "${item.numero}" />
                                    <input type="hidden" name="codempresa" value = "${item.codempresa}"/>
                                    <input type="hidden" name="empresa" value = "${item.empresa}"/>
                                    <button class="btn btn-primary btn-xs">
                                        <i class="fa fa-print"></i> Imprimir Ordem de Compra
                                    </button>
                                </form>
                            </td>
                        </tr>`;
                    tbody.append(row);
                });
            },error: function () {
                mensagem('error', 'Erro ao buscar dados.');
            }
        });



    })

    


});