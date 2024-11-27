$(document).ready(function () {

    retornaDados()

    $('#formPesquisa').submit(function (e) {
        e.preventDefault();

        const params = getQueryParams();

        console.log(params);

        var id = params.id;

        var empresa = $('select[name="empresa"]').val();

        selecionaEmpresa(id,empresa);

    })

function selecionaEmpresa(id,empresa)
{

    var url = '../../app/src/empresa/empresa.php'

    $.ajax({
        url: url,
        type: 'POST',
        data:{
            id: id,
            empresa: empresa
        },
        async: true,
        success: function(data){
            if (data.code === 200) {
                
                mensagem('success',data.mensagem);
                //console.log(data)
                window.location.href = "../home/index.php";

            }else{

                mensagem('error',data.mensagem);

            }
        },error: function(jqXHR, textStatus, errorThrown)
        {
            var errorMessage = jqXHR.responseText || 'Falha ao capturar empresas!';
            mensagem('error', errorMessage);
        }
    });
    

}


function retornaDados(){


    const params = getQueryParams();

    console.log(params);

    var id = params.id;

    if (typeof id === "undefined" || id === "") {
        window.location.href = "index.php";
    }
    

    var url = '../../app/src/empresa/empresa.php?id=' + id

    $.ajax({
            url: url,
            type: 'GET',
            async: true,
            success: function(data){
                if (data.code === 200) {
                    var items = data.data;

                    var select = $('#empresa');

                    select.empty();

                    select.append('<option value="">Selecione uma empresa desejada</option>');

                    items.forEach(function(item) {
                        var option = $('<option></option>') 
                        .attr('value', item.codemp)     
                        .text(item.razao);             
        
                        select.append(option);
                    });
                
                }
            },errvor: function(jqXHR, textStatus, errorThrown)
            {
                var errorMessage = jqXHR.responseText || 'Falha ao capturar empresas!';
                mensagem('error', errorMessage);
            }
        });
        
    }


});