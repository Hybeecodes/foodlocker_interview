<?php
include 'init.php';

if(isset($_POST['add_product'])){
//    exit(var_dump($_POST));
    $product_name = $_POST['product_name'];
    $units = $_POST['units'];
    $received_unit = $_POST['unit_recieved'];
    $relation = $_POST['relation'];
    if($received_unit == 'bag'){
        $quantity = $_POST['product_quantity'] * $relation;
    }else{
        $quantity = $_POST['product_quantity'];
    }
    $prices = $_POST['price'];
    $costs = $_POST['cost'];
    $unit_price = [];
    $unit_cost = [];
    if(count($units) == count($prices)){
        for ($i=0;$i<count($units);$i++){
            $unit = $units[$i];
            $price = $prices[$i];
            $cost = $costs[$i];
            array_push($unit_price,[$unit => $price]);
            array_push($unit_cost,[$unit => $cost]);
        }
    }
    $units = json_encode($units);
    $unit_price = json_encode($unit_price);
    $unit_cost = json_encode($unit_cost);
//    exit($product_name);
    $data = array(
        "product_name"=>$product_name,
        "units"=>$units,
        "quantity"=>$quantity,
        "relation"=>$relation,
        "unit_price"=>$unit_price,
        "unit_cost"=>$unit_cost
    );
    $table = "products";
    $res = insertData($data,$table,$db_conn);
}

if(isset($_POST['buy_product'])){
//    exit(var_dump($_POST));
    $product_id = $_POST['product'];
    $unit = $_POST['unit'];
    $quantity = $_POST['quantity'];
    $relation = get_relation($product_id,$db_conn);
    $available_quanity = get_available_quantity($product_id,$db_conn);
    // get unit cost
    $unit_cost = json_decode(get_unit_cost($product_id,$db_conn),true);
//    exit(var_dump($unit_cost));
    // calculate amaount
    $unit_amount = 0;
    $first_units = [];
    foreach ($unit_cost as $un){
        foreach ($un as $uni => $cost){
            if($unit == $uni){
                $unit_amount = $cost;
            }

        }
    }
    foreach ($unit_cost as $un){
        foreach ($un as $uni => $cost){
            array_push($first_units,$uni);
            break;


        }
    }

//    exit(var_dump($unit));
    $total_amount = $unit_amount * $quantity;
    if($unit == $first_units[0]){
        $quantity_to_remove = $quantity;
    }else{
        $quantity_to_remove = $relation * $quantity;
    }
    if($available_quanity > $quantity_to_remove){
        $quantity_to_left = $available_quanity - $quantity_to_remove;
    }else{
        exit(json_encode(["status"=>0,"message"=>"Invalid Quantity"]));
    }
//exit(var_dump($quantity_to_left));

    // save transaction
    $data = array(
        "product_id"=>$product_id,
        "unit"=>$unit,
        "quantity"=>$quantity,
        "amount"=>$total_amount
    );
    $table = "transaction";
    $res = insertData($data,$table,$db_conn);
    // update product details
    $data = array(
        "quantity"=>$quantity_to_left
    );
    $table = "products";
    $where = "WHERE id = $product_id";
    $res = updateData($data,$table,$db_conn,$where);
    exit(json_encode(["status"=>1,"message"=>"Transaction Successful"]));
}

if(isset($_GET['get_units'])){
    $product = $_GET['product_id'];
    $units = get_unit($product,$db_conn);
    exit($units);
}