<?php
if (!function_exists('formatCpf')) {
    function formatCpf($value)
    {
        $cnpj_cpf = preg_replace("/\D/", '', $value);

        if (strlen($cnpj_cpf) === 11) {
            return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cnpj_cpf);
        }

        return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpj_cpf);
    }
}

if (!function_exists('formatReal')) {
    function formatMoedaReal($valor, $mascara = false)
    {
        $value = number_format((float) $valor, 2, ",", ".");

        return $mascara ? 'R$ ' . $value : $value;
    }
}
if (!function_exists('realToFloat')) {
    function realToFloat($valor)
    {
        return (float) str_replace(',', '.', str_replace('.', '', $valor));
    }
}
if (!function_exists('formatCep')) {
    function formatCep($value)
    {
        return preg_replace("/(\d{5})(\d{3})/", "\$1-\$2", $value);
    }
}


// convert dd/mm/yyyy to yyyy-mm-dd
if (!function_exists('formatDate')) {
    function formatDate($date): string
    {
        $date = str_replace('/', '-', $date);

        return date('Y-m-d', strtotime($date));
    }
}
// convert yyyy-mm-dd to dd/mm/yyyy
if (!function_exists('formatDateToBr')) {
    function formatDateToBr($date): string
    {
        $date = str_replace('-', '/', $date);

        return date('d/m/Y', strtotime($date));
    }
}


if (!function_exists('getMonths')) {
    /**
     * @param int $type
     *
     * @return array
     */
    function getMonths($showKeys = false)
    {
        if ($showKeys) {
            return [
                '01' => 'Janeiro',
                '02' => 'Fevereiro',
                '03' => 'Março',
                '04' => 'Abril',
                '05' => 'Maio',
                '06' => 'Junho',
                '07' => 'Julho',
                '08' => 'Agosto',
                '09' => 'Setembro',
                '10' => 'Outubro',
                '11' => 'Novembro',
                '12' => 'Dezembro'
            ];
        }

        return [
            'Janeiro',
            'Fevereiro',
            'Março',
            'Abril',
            'Maio',
            'Junho',
            'Julho',
            'Agosto',
            'Setembro',
            'Outubro',
            'Novembro',
            'Dezembro'
        ];
    }
}



