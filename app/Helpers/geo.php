<?php
/**
 * Implements the Haversine formula to calculate distance in km
 */
function calcularDistanciaHaversine($lat1, $lon1, $lat2, $lon2)
{
    $raio = 6371; // Earth's radius in km

    // Convert degrees to radians
    $lat1 = deg2rad($lat1);
    $lon1 = deg2rad($lon1);
    $lat2 = deg2rad($lat2);
    $lon2 = deg2rad($lon2);

    // Coordinate differences
    $dlat = $lat2 - $lat1;
    $dlon = $lon2 - $lon1;

    // Haversine formula
    $a = sin($dlat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($dlon / 2) ** 2;
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    return $raio * $c; // Distance in km
}