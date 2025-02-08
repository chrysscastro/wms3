$(document).ready(function () {

    var usuario = getParameterByName('val');
    retornaNivel(usuario);


    $('#formPesquisa').submit(function(e) {
        e.preventDefault();

       gravarInfo(usuario)
    });  



});

function addEmp(){
    var id = getParameterByName('val');
    var selectedValue = $('select[name="empresa"]').val();
    if (!selectedValue) {
        mensagem('error','Por favor, selecione uma empresa.');
        return;
    }
    console.log('ID:', id);
    console.log('Empresa Selecionada:', selectedValue);

    insEmp(id, selectedValue);

}
function retornaDados(nome){

    

    $.ajax({
        url: "../../app/src/users/users.php?tipo=showUserI",
        type: "POST",
        data:{
            dados: nome
        },
        async: true,
        success: function(data){
            if (data.code === 200)
            {
                var items = data.dados;

                items.forEach(function(item) {

                    $('input[name="usuario"]').val(item.usuario);
                    $('input[name="nome"]').val(item.nome);
                    $('input[name="senha"]').val(item.token);
                    $('#tipo').val(item.permissao);

                });

                
                var url = '../../app/src/empresa/empresa.php?id=' + nome

                $.ajax({
                        url: url,
                        type: 'GET',
                        async: true,
                        success: function(data){
                            if (data.code === 200) {
                                var items = data.data;

                                var tbody = $('#tabelaEmpresa tbody');

                                tbody.empty();
                                

                                items.forEach(function(item) {


                                    var row = `<tr>
                                        <td>${item.codemp}</td>
                                        <td class="hidden-phone">${item.razao}</td>
                                        <td>${item.cnpj}</td>
                                        <td>
                                            <button 
                                            type="button" 
                                            class="btn btn-danger btn-xs" 
                                            onclick="delEmp(${nome},'${item.codemp}')"
                                            title="Deletar Empresa"
                                            >
                                            <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                        <td></td>
                                    </tr>`;

                                    tbody.append(row);
                                });
                            
                            }
                        },errvor: function(jqXHR, textStatus, errorThrown)
                        {
                            var errorMessage = jqXHR.responseText || 'Falha ao capturar empresas!';
                            mensagem('error', errorMessage);
                        }
                    });
                
            }
        },error: function(jqXHR, textStatus, errorThrown)
        {
            console.error("Erro na requisição AJAX:", textStatus, errorThrown);
            console.error("Resposta do servidor:", jqXHR.responseText);
        }


    });
}


function insEmp(usuario,cod)
{

    $.ajax({
        url: "../../app/src/users/users.php?tipo=insEmp",
        type: "POST",
        data:{
            dados: usuario,
            emp:cod
        },
        async: true,
        success: function(data){
         
                console.log(data);
                mensagem(data.type,data.mensagem);
            
        },error: function(jqXHR, textStatus, errorThrown)
        {
            console.error("Erro na requisição AJAX:", textStatus, errorThrown);
            console.error("Resposta do servidor:", jqXHR.responseText);
        }


    });

    retornaNivel(usuario)
    retornaDados(usuario)

}

function delEmp(usuario,cod)
{

    $.ajax({
        url: "../../app/src/users/users.php?tipo=delEmp",
        type: "POST",
        data:{
            dados: usuario,
            emp:cod
        },
        async: true,
        success: function(data){
         
                console.log(data);
                mensagem(data.type,data.mensagem);
            
        },error: function(jqXHR, textStatus, errorThrown)
        {
            console.error("Erro na requisição AJAX:", textStatus, errorThrown);
            console.error("Resposta do servidor:", jqXHR.responseText);
        }


    });

    retornaDados(usuario)

}

function gravarInfo(idUser)
{

     // Obter os valores dos campos de entrada
     const usuarioInput = document.querySelector('input[name="usuario"]');
     const senhaInput = document.querySelector('input[name="senha"]');
     const nomeInput = document.querySelector('input[name="nome"]');
     const tipoSelect = document.querySelector('select[name="tipo"]');
 
     if (!usuarioInput || !senhaInput || !nomeInput || !tipoSelect) {
         console.error("Um ou mais elementos de entrada estão faltando no HTML.");
         return;
     }
 
     const usuario = usuarioInput.value;
     const senha = senhaInput.value;
     const nome = nomeInput.value;
     const tipo = parseInt(tipoSelect.value, 10); // Converter para número
 
     // Obter todos os checkboxes dentro da div com id "permissao"
     const permissaoDiv = document.getElementById('permissao');
     if (!permissaoDiv) {
         console.error("A div com id 'permissao' não foi encontrada.");
         return;
     }
 
     const checkboxes = permissaoDiv.querySelectorAll('input[type="checkbox"]');
 
     if (checkboxes.length === 0) {
         console.warn("Nenhum checkbox encontrado dentro da div 'permissao'.");
     }
 
     const permissao = Array.from(checkboxes).map(checkbox => {
         
         const nivel = parseInt(checkbox.value, 10);
         if (isNaN(nivel)) {
             console.warn(`O valor do checkbox não é um número válido: ${checkbox.value}`);
             return null; 
         }
 
         return {
             nivel: nivel,
             liberacao: checkbox.checked ? -1 : 0
         };
     }).filter(item => item !== null); 
 

     const usuarioInfo = {
         id: idUser,
         usuario: usuario,
         senha: senha,
         nome: nome,
         tipo: tipo,
         permissao: permissao
     };
 
     jsonEnvia = JSON.stringify(usuarioInfo, null, 2);



     $.ajax({
        url: "../../app/src/users/users.php?tipo=gravar",
        type: "POST",
        data: jsonEnvia,
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: true,
        success: function(data) {
            //console.log(data);
            mensagem(data.type, data.mensagem);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error("Erro na requisição AJAX:", textStatus, errorThrown);
            console.error("Resposta do servidor:", jqXHR.responseText);
        }
    });
 

}


function retornaNivel(usuario)
{

    if(usuario != 0 && usuario !== undefined && usuario !== null){
        
        var urlEmp = "../../app/src/empresa/empresa.php?tipo=emp&id=" + usuario

        console.log(urlEmp);

        $.ajax({
            url: urlEmp,
            type: "GET",
            async: true,
            success: function(data) {
                console.log(data)
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

            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("Erro na requisição AJAX:", textStatus, errorThrown);
                console.error("Resposta do servidor:", jqXHR.responseText);
            }
        });
        retornaDados(usuario);
        document.getElementById("usuario").disabled = true;

    }else{

         document.getElementById("userOk").style.display  = "none";

    }

    $.ajax({
        url: "../../app/src/users/users.php?tipo=show_lib",
        type: "POST",
        data: {
            dados: usuario
        },
        async: true,
        success: function(data) {
            $('#permissao').html(data);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error("Erro na requisição AJAX:", textStatus, errorThrown);
            console.error("Resposta do servidor:", jqXHR.responseText);
        }
    });

    
}
function getParameterByName(name, url = window.location.href) {
    name = name.replace(/[\[\]]/g, '\\$&');
    let regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
}