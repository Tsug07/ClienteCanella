<?php

// Formatação do CNPJ para exibição
function formatarCNPJ($cnpj) {
    $cnpj = preg_replace('/[^0-9]/', '', $cnpj); // Remove caracteres não numéricos
    if (strlen($cnpj) != 14) return $cnpj; // Retorna sem formatação se não tiver 14 dígitos
    return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "$1.$2.$3/$4-$5", $cnpj);
}

// Formatação de data
function formatarData($data) {
    if (!$data) return 'Não disponível';
    $timestamp = strtotime($data);
    return date('d/m/Y H:i', $timestamp);
}

