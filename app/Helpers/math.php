<?php

function formatMoneyToDecimal($valor)
{
    // Remove o ponto (usado como separador de milhar)
    $valor = str_replace('.', '', $valor);
    // Substitui a vírgula (usada como separador decimal) por ponto
    $valor = str_replace(',', '.', $valor);

    // Converte para float
    return (float) $valor;
}