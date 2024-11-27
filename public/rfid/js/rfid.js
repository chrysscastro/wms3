$(document).ready(function () {
    retornaDados();
    setInterval(retornaDados,1000);
});
function retornaDados(){


    var status = $('select[name="status"]').val();
    var chave = $('input[name="chave"]').val();
    var tipo = $('select[name="tipo"]').val();
    var limit = $('input[name="quantidade"').val();

    $.ajax({
        url: '../../app/src/rfid/rfid.php?funcao=fila',
        type: 'POST',
        data: {status:status,chave:chave,tipo:tipo,limit:limit},
        async: true,
        success: function(data){
            if (data.status === "success") {
                var items = data.retorno;
                var tbody = $('#tabelarfid tbody');
                tbody.empty(); 
                
                var disable = '';


                items.forEach(function(item) {
                    if (item.status === 'FALHA')
                    {
                        disable = '';
                    }else
                    {
                        disable = 'disabled';
                    }
                    var row = `<tr>
                        <td>${item.tipo}</td>
                        <td class="hidden-phone">${item.data}</td>
                        <td>${item.chave}</td>
                        <td>${item.serie}</td>
                        <td>${item.status}</td>
                        <td>${item.usuario}</td>
                        <td>   
                        <button onclick="get_json(${item.id},'${item.chave}')"
                         class="btn btn-success btn-xs"  data-toggle="tooltip"
                          data-placement="top" title="Baixar Json (${item.id})"><i class="fa fa-cloud-download "></i></button>   
                        <button ${disable} onclick="refresh(${item.id})" class="btn btn-primary btn-xs"><i class="fa fa-refresh"></i></button>
                        <button ${disable} onclick="get_error(${item.id})" class="btn btn-danger btn-xs"><i class="fa fa-exclamation-triangle"></i></button>
                        </td>
                        <td></td>
                    </tr>`;
                    tbody.append(row);
                });
            
            }
        },error: function(jqXHR, textStatus, errorThrown)
        {
            var errorMessage = jqXHR.responseText || 'Falha ao atualizar fila do JOB!';
            mensagem('error', errorMessage);
        }
    });
    
}

function refresh(id)
{
    $.ajax({
        url: '../../app/src/rfid/rfid.php?funcao=refresh',
        type: 'POST',
        data: {id: id},
        beforeSend: function()
        {
            Swal.fire({
                title: 'Aguarde...',
                html: 'Enviando Documento.',
                allowOutsideClick: false,
                onBeforeOpen: () => {
                  Swal.showLoading(); 
                },
                showConfirmButton: false,
              });
        },
        success: function(data)
        {
            Swal.close();

            mensagem(data.status,data.mensagem);
            retornaDados();
        },
        error: function()
        {
            mensagem('error','Falha ao enviar documento para a Fila de Impressão!');
        }

    });
}


function delete_or(id)
{
    $.ajax({
        url: '../../app/src/rfid/rfid.php?funcao=delete',
        type: 'POST',
        data: {id: id},
        beforeSend: function()
        {
            Swal.fire({
                title: 'Aguarde...',
                html: 'Enviando Documento.',
                allowOutsideClick: false,
                onBeforeOpen: () => {
                  Swal.showLoading(); 
                },
                showConfirmButton: false,
              });
        },
        success: function(data)
        {
            Swal.close();

            mensagem(data.status,data.mensagem);
            retornaDados();
        },
        error: function()
        {
            mensagem('error','Falha ao enviar documento para a Fila de Impressão!');
        }

    });
}

function get_json(id,chave)
{
    $.ajax({
        url: '../../app/src/rfid/rfid.php?funcao=json',
        type: 'POST',
        data: {id: id},
        beforeSend: function()
        {
            Swal.fire({
                title: 'Aguarde...',
                html: 'Baixando arquivo.',
                allowOutsideClick: false,
                onBeforeOpen: () => {
                  Swal.showLoading(); 
                },
                showConfirmButton: false,
              });
        },
        success: function(data)
        {
            Swal.close();

            var jsonStr = JSON.stringify(data);
            console.log(jsonStr);


            var blob = new Blob([jsonStr], { type: 'application/json' });

            var link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = `documento_contare_${chave}(${id}).json`;
            document.body.appendChild(link);

            link.click();

            
            document.body.removeChild(link);

            mensagem('success', "Arquivo baixado com sucesso!");

        },
        error: function()
        {
            mensagem('error','Falha ao Baixar o Arquivo Json!');
        }

    });
}

function get_error(id)
{
    $.ajax({
        url: '../../app/src/rfid/rfid.php?funcao=return',
        type: 'POST',
        data: {id: id},
        beforeSend: function()
        {
            Swal.fire({
                title: 'Aguarde...',
                html: '',
                allowOutsideClick: false,
                onBeforeOpen: () => {
                  Swal.showLoading(); 
                },
                showConfirmButton: false,
              });
        },
        success: function(data)
        {
            var jsonStr = JSON.stringify(data);
            mensagem("success", jsonStr);

        },
        error: function()
        {
            mensagem('error','Falha ao Baixar o Arquivo Json!');
        }

    });
}




//${item.id}