<?php
/**
 * Created by PhpStorm.
 * User: Megacodes
 * Date: 11/26/2018
 * Time: 2:09 PM
 */

function calculate_delivery_cost($volume,$location_distance,$quantity){
    // assume 1km cost #100 for motocycle  and #70 for cab and #50 for bus
    $total_volume = $volume * $quantity;
    // assume that the volume of space available in a motocycle is 500
    $motocycle_vol = 500;
    // assume that the volume of space available in a car is 1000
    $cab_volume = 1000;
    // assume that the volume of space available in a bus is 1000
    $bus_volume = 2500;
//    return $total_volume
    $means = 0;
    $mns = "Bus";
    if($total_volume <= $motocycle_vol){
        $means = 1; // 1 for motocycle , 2 for cab and 0 for bus
        $mns = "Motocycle";
    }elseif ($total_volume <= $cab_volume && $total_volume > $motocycle_vol){
        $means = 2;
        $mns = "Cab ";
    }
    $cost_per_dist = ($means == 1)? 100 : 70;
    if($means ==0) $cost_per_dist = 50;
    $total_cost = $cost_per_dist * $location_distance; // assume location distance is in km
    return "#$total_cost if $mns is used";
}

$volume = 200;
$location_distance = 50;
$quantity = 50;

$cost = calculate_delivery_cost($volume,$location_distance,$quantity);
echo $cost;