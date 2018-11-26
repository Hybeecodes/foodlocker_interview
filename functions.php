<?php
/**
 * Created by PhpStorm.
 * User: Megacodes
 * Date: 11/26/2018
 * Time: 10:41 AM
 */


function insertData($array, $table,$db_conn)
{

    try {

        $fields = array_keys($array);
        $values = array_values($array);
        $fieldlist = implode(',', $fields);
        $qs = str_repeat("?,", count($fields) - 1);
        $firstfield = true;

        $sql = "INSERT INTO `$table` SET";

        for ($i = 0; $i < count($fields); $i++) {
            if (!$firstfield) {
                $sql .= ", ";
            }
            $sql .= " " . $fields[$i] . "=?";
            $firstfield = false;
        }

        $sth = $db_conn->prepare($sql);
        return $sth->execute($values);

        return $sth;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}

function getAllData($data, $table,$db_conn, $where = '')
{
    $settings_data=array();
    try {
        if ($data != '*') {
            $selections = implode(', ', $data);
        } else {
            $selections = '*';
        }

        $stmt = $db_conn->prepare("SELECT {$selections} FROM `$table` " . $where);
        $stmt->execute();
        $settings_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($stmt->rowCount() > 0) {
            return $settings_data;
        }
    } catch (PDOException $e) {
        return $settings_data;
    }
}
function getData($data, $table,$db_conn, $where = '')
{
    try {
        if ($data != '*') {
            $selections = implode(', ', $data);
        } else {
            $selections = '*';
        }

        $stmt = $db_conn->prepare("SELECT {$selections} FROM `$table` " . $where . " LIMIT 1");
        // echo "SELECT {$selections} FROM `$table` " . $where . " LIMIT 1";
        $stmt->execute();
        $settings_data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($stmt->rowCount() > 0) {
            return $settings_data;
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}



function updateData($array, $table,$db_conn, $where = '')
{

    try {

        $fields = array_keys($array);
        $values = array_values($array);
        $fieldlist = implode(',', $fields);
        $qs = str_repeat("?, ", count($fields) - 1);
        $firstfield = true;

        $sql = "UPDATE `$table` SET";

        for ($i = 0; $i < count($fields); $i++) {
            if (!$firstfield) {
                $sql .= ", ";
            }
            $sql .= " " . $fields[$i] . "= ? ";
            $firstfield = false;
        }
        if (!empty($where)) {
            $sql .= $where;
        }
        $sth = $db_conn->prepare($sql);
        return $sth->execute($values);

        return $sth;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}

function get_unit_cost($product_id,$db_conn){
    $data = array("unit_cost");
    $table = "products";
    $where = "WHERE id = $product_id";
    $res = getData($data,$table,$db_conn,$where);
    $unit_cost = !empty($res) ? $res['unit_cost'] : "";
    return $unit_cost;
}

function get_unit($product_id,$db_conn){
    $data = array("units");
    $table = "products";
    $where = "WHERE id = $product_id";
    $res = getData($data,$table,$db_conn,$where);
    $units = !empty($res) ? $res['units'] : "";
    return $units;
}

function get_relation($product_id,$db_conn){
    $data = array("relation");
    $table = "products";
    $where = "WHERE id = $product_id";
    $res = getData($data,$table,$db_conn,$where);
    $relation = !empty($res) ? $res['relation'] : "";
    return $relation;
}
function get_available_quantity($product_id,$db_conn){
    $data = array("quantity");
    $table = "products";
    $where = "WHERE id = $product_id";
    $res = getData($data,$table,$db_conn,$where);
    $quantity = !empty($res) ? $res['quantity'] : "";
    return $quantity;
}



function calculate_delivery_cost($volume,$location_distance,$quantity){
    // assume 1km cost #100 for motocycle  and #70 for cab
    $total_volume = $volume * $quantity;
    // assume that the volume of space available in a motocycle is 500
    $motocycle_vol = 500;
    // assume that the volume of space available in a motocycle is 1000
    $cab_volume = 1000;
    if($total_volume <= $motocycle_vol){
        $means = 1; // 1 for motocycle ans 2 for cab
    }elseif ($total_volume <= $cab_volume && $total_volume > $motocycle_vol){
        $means = 2;
    }
    $cost_per_dist = ($means == 1)? 100 : 70;
    $total_cost = $cost_per_dist * $location_distance; // assume location distance is in km
    return $total_cost;
}