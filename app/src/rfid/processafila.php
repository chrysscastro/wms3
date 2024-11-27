<?php
$servername = "apl.txc.com.br";
$username = "txc";
$password = "Ti@2015!*";
$dbname = "txc_db_wms";
$port = 3306;

$url = "http://txc.factory.contare.app/api/v1/documento";
$token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJkb21haW4iOiJ0eGMiLCJpc3MiOiJDb250YXJlIEZhY3RvcnkiLCJ0ZW5hbnRJZCI6NjEsInVzZXJJZCI6ODh9.trwqNrpZIoF2-jrnzfdfsVRAodfSLu-MhEU2SZT2zG0";

// Conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $dbname, $port);
$conn2 = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error || $conn2->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Consulta para obter os dados
$sql = "SELECT job.id AS id, mov.json AS arquivo, job.chave as chave
        FROM txc_tb_job_serv job 
        INNER JOIN txc_tb_job_mov mov ON mov.movimento = job.id
        WHERE job.status = 'PENDENTE'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $codigo = $row['id'];
        $jsonenvia = $row['arquivo'];

        // Atualizar status para 'ENVIANDO'
        $update = "UPDATE txc_tb_job_serv SET status = 'ENVIANDO' WHERE id = '$codigo'";
        $conn2->query($update);

        // Configurar e enviar requisição POST
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $token",
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonenvia);

        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);

        $resposta = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Verificar o resultado da requisição
        if ($httpCode == 200) {
            $update = "UPDATE txc_tb_job_serv SET status = 'ENVIADO' WHERE id = '$codigo'";
        } else {
            $update = "UPDATE txc_tb_job_serv SET status = 'FALHA' WHERE id = '$codigo'";
        }
        $conn2->query($update);

        // Atualizar a tabela txc_tb_job_mov com o retorno
        $update = "UPDATE txc_tb_job_mov SET retorno = '$resposta' WHERE movimento = '$codigo'";
        $conn2->query($update);
    }
} else {
    echo "Nenhum registro pendente encontrado.";
}

// Fechar conexões
$conn->close();
$conn2->close();
