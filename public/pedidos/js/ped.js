function detona_data(){
    // Obtém a data atual
var dataAtual = new Date();

// Define a primeira data do mês
var primeiroDia = new Date(dataAtual.getFullYear(), dataAtual.getMonth(), 1);

// Formata a data para o formato "YYYY-MM-DD" necessário pelo input type="date"
var primeiroDiaFormatado = primeiroDia.toISOString().split('T')[0];

// Define o valor padrão para o campo "Emissão (De)" como o primeiro dia do mês atual
document.getElementById('dtinicio').value = primeiroDiaFormatado;

// Define o valor padrão para o campo "Emissão (Até)" como a data atual
var dataAtualFormatada = dataAtual.toISOString().split('T')[0];
document.getElementById('dtfim').value = dataAtualFormatada;

}

$(document).ready(function () {

    detona_data();




    $('#formPesquisa').submit(function (e) {
        e.preventDefault(); 

        var pedido = $('input[name="chave"]').val();
        var dtinicio = $('input[name="dtinicio"]').val();
        var dtfim = $('input[name="dtfim"]').val();
        var imp = $('select[name="imp"]').val();

        console.log("Pedido:", pedido);
        console.log("Data de Início:", dtinicio);
        console.log("Data Final:", dtfim);
        console.log("Impresso:", imp);

       
        var url = '../../app/src/ped/ped.php?type=get_ped&dtini=' + dtinicio + '&dtfim=' + dtfim + 
        '&dtfim=' + dtfim + '&imp=' + imp 

        console.log(url);

        $.ajax({
            url: url,
            type: 'GET',
            async: true,
            beforeSend: function () {
                Swal.fire({
                    title: 'Aguarde...',
                    html: 'Estamos carregando os pedidos.',
                    allowOutsideClick: false,
                    onBeforeOpen: () => {
                        Swal.showLoading();
                    },
                    showConfirmButton: false,
                });
            },
            success: function(data){
                Swal.close();
                if (data.code === 200) {
                    
                    mensagem('success',data.mensagem);

                    var items = data.data;
                    var tbody = $('#tabelaPedidos tbody');
                    tbody.empty(); 
                    
                    var disable = '';


                    items.forEach(function(item) {
                        var row = `<tr>
                            <td>${item.rep}</td>
                            <td class="hidden-phone">${item.data}</td>
                            <td>${item.pedido}</td>
                            <td>
                            <form action="javascript:void(0)" method="post">
                                <button  class="btn btn-success btn-xs"><i class="fa fa-exclamation-triangle"></i> Separar </button>
                            </form>
                            <button onclick="imprimirPedido('${item.pedido}')" class="btn btn-primary btn-xs"><i class="fa fa-print"></i> Imprimir </button>
                            </td>
                        </tr>`;
                        tbody.append(row);
                    })

    
                }else{
    
                    mensagem('error',data.mensagem);
    
                }
            },error: function(jqXHR, textStatus, errorThrown)
            {
                var errorMessage = jqXHR.responseText || 'Falha ao capturar empresas!';
                mensagem('error', errorMessage);
            }
        });

    });




});


async function imprimirMassa(){
    const pedidos = [];
    const rows = document.querySelectorAll("#tabelaPedidos tbody tr");

    rows.forEach((row) => {
        const pedido = row.cells[2].innerText.trim();
        if (pedido) {
            pedidos.push(pedido);
        }
    });

    const modal = document.getElementById("modalCarregamento");
    const statusCarregamento = document.getElementById("statusCarregamento");
    modal.style.display = "block";

    for (let i = 0; i < pedidos.length; i++) {
        const pedidoAtual = pedidos[i];
        statusCarregamento.innerText = `Imprimindo pedido ${pedidoAtual} (${i + 1} de ${pedidos.length})...`;

        try {
            await imprimirPedido(pedidoAtual);
        } catch (error) {
            console.error(`Erro ao imprimir pedido ${pedidoAtual}:`, error);
        }
    }

    statusCarregamento.innerText = "Todos os pedidos foram processados!";
    setTimeout(() => {
        modal.style.display = "none";
    }, 3000); // Fecha o modal após 3 segundos
}

function imprimirPedido(pedido) {
    var url = '../../app/src/logi_report/rpot_espelho.php?type=imp&ped=' + pedido;
    console.log("Chamando URL de impressão:", url);

    $.ajax({
        url: url,
        type: 'GET',
        async: true,
        beforeSend: function () {
            Swal.fire({
                title: 'Aguarde...',
                html: 'Estamos realizando sua impressão.',
                allowOutsideClick: false,
                onBeforeOpen: () => {
                    Swal.showLoading();
                },
                showConfirmButton: false,
            });
        },
        success: function (data) {
            var printWindow = window.open('', '', 'width=800,height=600');
            printWindow.document.open();
            printWindow.document.write(data);
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
            printWindow.close();
            mensagem('success', "Impressão realizada com sucesso!");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            var errorMessage = jqXHR.responseText || 'Falha ao capturar empresas!';
            console.error("Erro ao imprimir:", errorMessage);
            mensagem('error', errorMessage);
        }
    });
}




function abreped($ped){

}
