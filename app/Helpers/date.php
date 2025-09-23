<?php

use Carbon\Carbon;

function getWeekName($day = null)
{
    $days = [
        0 => 'Domingo',
        1 => 'Segunda-feira',
        2 => 'Terça-feira',
        3 => 'Quarta-feira',
        4 => 'Quinta-feira',
        5 => 'Sexta-feira',
        6 => 'Sábado'
    ];

    return $day ? $days[$day] : $days;
}
function diffForHumansCustom(Carbon $dateTime)
{
    $now = Carbon::now();
    $diff = $dateTime->diff($now);

    $parts = [];

    if ($diff->days > 0) {
        $parts[] = $diff->days . ' ' . ($diff->days === 1 ? 'dia' : 'dias');
    }
    if ($diff->h > 0) {
        $parts[] = $diff->h . ' ' . ($diff->h === 1 ? 'hora' : 'horas');
    }
    if ($diff->i > 0) {
        $parts[] = $diff->i . ' ' . 'min';
    }

    $count = count($parts);

    if ($count === 0) {
        return 'agora mesmo';
    } elseif ($count === 1) {
        return $parts[0] . ' atrás';
    } elseif ($count === 2) {
        return $parts[0] . ' e ' . $parts[1] . ' atrás';
    } else { // Mais de 2 partes (dias, horas, mins)
        $last = array_pop($parts);
        return implode(', ', $parts) . ' e ' . $last . ' atrás';
    }
}
