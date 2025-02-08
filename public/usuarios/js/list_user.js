$(document).ready(function () {
    retornaDados('');
    $('#formPesquisa').submit(function(e) {
        e.preventDefault();

       var nome = $('input[name="chave"]').val();
       retornaDados(nome);
    });  
});





function retornaDados(nome){

    

    $.ajax({
        url: "../../app/src/users/users.php?tipo=show",
        type: "POST",
        data:{
            dados: nome
        },
        async: true,
        success: function(data){
            if (data.code === 200)
            {
                var items = data.dados;
                var tbody = $('#tabelaUsuarios tbody');

                tbody.empty();

                items.forEach(function(item) {

                    if (item.ativo === '-1'){
                        item.ativo = '<i class="fa fa-circle" style="color: green !important;">';
                    }else
                    {
                        item.ativo = '<i class="fa fa-circle" style="color: red !important;">';
                    }

                    if (item.permissao === '1'){
                        item.permissao = 'Administrador';
                    }else
                    {
                        item.permissao = 'Padrão';
                    }


                    var row = `<tr>
                        <td>${item.id}</td>
                        <td class="hidden-phone">${item.usuario}</td>
                        <td>${item.nome}</td>
                        <td>${item.permissao}</td>
                        <td>${item.ultevento}</td>
                        <td>${item.ativo}</td>
                        <td>
                        <a class="btn btn-primary btn-xs" href="user.php?val=${item.id}"><i class="fa fa-edit"></i></a>
                        </td>
                        <td></td>
                    </tr>`;
                    tbody.append(row);
                });

                
            }
        },error: function(jqXHR, textStatus, errorThrown)
        {
            console.error("Erro na requisição AJAX:", textStatus, errorThrown);
            console.error("Resposta do servidor:", jqXHR.responseText);
        }


    });
}