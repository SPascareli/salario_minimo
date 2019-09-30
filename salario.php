<?php
require 'vendor/autoload.php';

use GuzzleHttp\Client;

function main() {
    // pega os dados da url usando Guzzle Framework
    $client = new Client();
    $res = $client->request('GET', 'http://www.guiatrabalhista.com.br/guia/salario_minimo.htm');

    // processa o HTML usando a api DOMDocument padrão do PHP
    $html = $res->getBody();
    $doc = new DOMDocument();
    @$doc->loadHTML($html); // o DOMDocument reclama muito por causa de erros no HTML, então ignoro eles

    // busca a tabela principal com os dados
    $tables = @$doc->getElementById('content')->getElementsByTagName('table');
    $table = $tables->item(0);

    // pega todas as linhas da tabela (tr)
    $lines = $table->getElementsByTagName('tr');

    // processa todas as linhas e bote o resultado na variável $data
    $data = [];
    $headerData = [];
    for ($i = 0; $i < $lines->count(); $i++) {
        $line = $lines->item($i);
        // se for a primeira linha, então é o header
        if ($i == 0) {
            $headerData = getData($line);  
        } else {
            $data[] = array_combine($headerData, getData($line));
        }
    }

    // printa os dados encontrados
    print_r($data);
}

function getData($headerNode) {
    $returnData = [];
    foreach ($headerNode->getElementsByTagName('td') as $data) {
        $returnData[] = trim($data->textContent);
    }
    return $returnData;
}

main();
