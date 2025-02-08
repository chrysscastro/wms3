<?php
include_once(__DIR__ . "/../src/access/check_access.php");
include_once(__DIR__ . "/../public/gerais.php");

use \app\public_\seguranca;

$sec = new seguranca();
// Verifica se a requisição é POST e se há dados JSON
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty(file_get_contents('php://input'))) {
    // Recebe e decodifica o JSON enviado na requisição
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);

    // Verifica se o JSON é válido
    if (!$data || !isset($data['pedido'], $data['itens'])) {
        die("Dados inválidos ou ausentes no JSON!");
    }

    // Extrai os dados do JSON
    $pedido = $data['pedido'];
    $itens = $data['itens'];
} else {
    die("Requisição inválida! Esta página aceita apenas POST com dados JSON.");
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Impressão Espelho do Pedido <?= htmlspecialchars($pedido['pedido']); ?></title>
    <style>
        /* Definições gerais */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .invoice {
            max-width: 800px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        /* Cabeçalho */
        .invoice-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 4px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .invoice-header img {
            max-height: 80px;
        }

        .invoice-header h2 {
            color: #333;
            font-size: 24px;
            margin: 0;
        }

        /* Detalhes do pedido */
        .invoice-details {
            margin-bottom: 20px;
            padding: 10px 0;
            border-bottom: 2px dashed #ddd;
        }

        .invoice-details p {
            margin: 5px 0;
            font-size: 14px;
        }

        .invoice-details p strong {
            color: #007bff;
        }

        /* Tabela */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }

        table th {
            background-color: #007bff;
            color: #fff;
            text-transform: uppercase;
        }

        table tbody tr:nth-child(odd) {
            background-color: #f9f9f9;
        }

        table tbody tr:hover {
            background-color: #f1f1f1;
        }

        /* Rodapé */
        .invoice-footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }

        .invoice-footer p {
            margin: 0;
        }
    </style>
</head>

<body>
    <div class="invoice">
        <!-- Cabeçalho -->
        <div class="invoice-header">
            <img src="http://172.16.0.106/wms2/assets/img/logo_report.jpg" alt="Logo da Empresa">
            <h2>Textile Xtra Company</h2>
        </div>

        <!-- Detalhes do Pedido -->
        <div class="invoice-details">
            <p><strong>Pedido:</strong> <?= htmlspecialchars($pedido['pedido']); ?></p>
            <p><strong>Cliente:</strong> <?= htmlspecialchars($pedido['cliente']); ?></p>
            <p><strong>CPF/CNPJ:</strong> <?= htmlspecialchars($pedido['cpf']); ?></p>
            <p><strong>Data:</strong> <?= htmlspecialchars(date('d/m/Y', strtotime($pedido['data']))); ?></p>
        </div>

        <!-- Tabela de Produtos -->
        <table>
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Cor</th>
                    <th>Tamanho</th>
                    <th>Quantidade</th>
                    <th>Locais</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($itens as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['nome']); ?></td>
                        <td><?= htmlspecialchars($item['cor']); ?></td>
                        <td><?= htmlspecialchars($item['tamanho']); ?></td>
                        <td><?= htmlspecialchars($item['quantidade']); ?></td>
                        <td><?= htmlspecialchars($item['local']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Rodapé -->
        <div class="invoice-footer">
            <p>Relatório gerado pelo sistema LogiControl WMS!</p>
            <p>© Textile Xtra Company - Todos os direitos reservados</p>
        </div>
    </div>
</body>

</html>