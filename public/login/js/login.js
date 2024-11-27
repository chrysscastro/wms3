$('#formPesquisa').submit(function (e) {
    e.preventDefault();

    var usuario = $('input[name="usuario"]').val();
    var senha = $('input[name="senha"]').val();
    
    $.ajax({
        type: 'POST',
        url: '../../app/src/access/check_login.php',
        data: {
            usuario: usuario,
            senha: senha
        },
        success: function (data) {
            console.log(data);
            if(data.status == "falha")
            {
                mensagem("error",data.mensagem);
            }else
            {
                mensagem("success","Logado com sucesso!");
                window.location.href = "emp.php?id=" + data.id;
            }
        },
        error: function () {
            alert('Erro ao buscar dados.');
        }
    });
});