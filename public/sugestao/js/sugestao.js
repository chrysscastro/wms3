
function sugerirItem(pedido, item, cor, tam, quebra) {
    abrirPopup();
}

const popup = document.getElementById("modalOverlay");
const overlay = document.getElementById("overlay");

function fecharPopup() {
    overlay.style.display = "none";
    popup.style.display = "none";
}

function abrirPopup() {
    overlay.style.display = "block";
    popup.style.display = "block";
}


$(document).ready(function () {

    $('#pesquisaSugestao').submit(function (e) {
        e.preventDefault();

        var de = $('input[name="de"]').val();
        var ate = $('input[name="ate"]').val();

        $.ajax({
            type: 'POST',
            url: '../../app/src/sugestao/sugestao.php?funcao=rtr_quebra',
            data: {
                datai: de,
                dataf: ate
            },
            beforeSend: function(){
                Swal.fire({
                    title: 'Aguarde...',
                    html: 'Estamos carregando as quebras.',
                    allowOutsideClick: false,
                    onBeforeOpen: () => {
                      Swal.showLoading(); 
                    },
                    showConfirmButton: false,
                  });
            },
            success: function (data) {
                Swal.close();
                if (data.code === 200){
                    var tbody = $('#tabelaSugestao tbody');

                    tbody.empty(); 

                    var items = data.data;

                    Swal.fire({
                        title: 'Aguarde...',
                        html: 'Carregando itens.',
                        allowOutsideClick: false,
                        onBeforeOpen: () => {
                          Swal.showLoading(); 
                        },
                        showConfirmButton: false,
                      });
                    items.forEach(function(item) {
                        var row = `<tr>
                                        <th>
                                            <img 
                                            style="width: 200px;"
                                            src = "${item.url_img}">
                                        </th>
                                        <th><label style="display: block;">Pedido: ${item.pedido}</label>
                                            <label style="display: block;">Produto: ${item.descricao}</label>
                                            <label style="display: block;">Quebra: ${item.quebra}</label>
                                        </th>
                                        <th><button onclick="sugerirItem('${item.pedido}','${item.codigo}','${item.cor}','${item.tam}',${item.quebra})" class="btn btn-success btn-lg" type="submit">Sugerir</button></th>
                                </tr>`;
                        tbody.append(row);
                    });
                    Swal.close();

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


    



    fecharPopupBtn.addEventListener("click", fecharPopup);

});
