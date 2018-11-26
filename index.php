<?php
include 'init.php';

// get all products
$data = "*";
$table = "products";
$products = getAllData($data,$table,$db_conn);
//$units = get_unit($data,$table,$db_conn);
//var_dump($products);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />

    <title>FoodLocker</title>
</head>
<body>
    <?php

    ?>
    <div class="container-fluid">
    <h1 class="text-center">Foodlocker</h1>
        <div class="row">
            <div class="col offset-1">
            
            <h2>Inventory</h2>

            <h3>Product List</h3>

                <table class="table">
                    <thead>
                    <th>
                        Product Name
                    </th>
                    <th>Units</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Unit Cost</th>
                    </thead>
                    <tbody>
                    <?php
                    if(!empty($products) && count($products)> 0){
                        foreach ($products as $product){ ?>
                            <tr>
                                <td>
                                    <?php echo $product['product_name']; ?>
                                </td>
                                <td>
                                    <?php
                                    $units = json_decode($product['units'],true);
                                    foreach ($units as $unit){
                                        echo "$unit , ";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php echo $product['quantity']." ".$units[0]; ?>
                                </td>
                                <td>
                                    <?php
                                    $unit_price = json_decode($product['unit_price'],true);
                                    foreach ($unit_price as $up){
                                        foreach ($up as $unit => $price){
                                            echo "$unit : #$price <br>";
                                        }
                                    }

                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $unit_cost = json_decode($product['unit_cost'],true);
                                    foreach ($unit_cost as $us){
                                        foreach ($us as $unit => $cost){
                                            echo "$unit : #$cost <br>";
                                        }
                                    }

                                    ?>
                                </td>
                            </tr>
                     <?php   }
                    }
                    ?>

                    </tbody>
                </table>

            <h3>Add New Product</h3>
            <form id="productForm">
                <div class="inputs">
                    <div class="form-group">
                        <label for="product_name">Product Name</label>
                        <input type="text" name="product_name" id="product_name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="product_name">Units of Measurement(in order in increasing value)</label>
                        <select name="units[]" class="form-control" multiple id="units">
                            <option value=""></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="product_name">Product Quanity Recieved</label>
                        <input type="text" name="product_quantity" id="product_quanity" class="form-control">
                        <select name="unit_recieved" id="unit_recieved" placeholder="Unit" class="form-control">

                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary" id="submit" >Add Product</button>
                </div>
            </form>
            
            </div>
            <div class="col offset-1">
                <h2 class="text-center">
                    Welcome to Foodlocker
                </h2>
                <b>Please Fill the Form to buy a product</b>
                <form id="buy_product">
                    <div class="form-group">
                        <label for="product">Select Product</label>
                        <select name="product" class="form-control" id="products">
                            <?php
                            if(!empty($products) && count($products) > 0){
                                foreach($products as $product){?>
                                    <option value="<?php echo $product['id'] ?>"><?php echo $product['product_name']; ?></option>
                              <?php  }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Select Unit to buy</label>
                        <select name="unit" class="form-control" id="unit">

                        </select>
                    </div>
                    <div class="form-group">
                        <label>Quantity to buy</label>
                        <input type="number" name="quantity" id="quantity" class="form-control">
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary" id="buy" >Buy Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="assets/js/jquery-3.3.1.min.js"></script>
    <script src="assets/js/bootstrap.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

    <script>
        $(document).ready(function(){
            $('.new_p').remove();
            let product = $('#products').val();
            $.get(`parser.php?get_units=1&product_id=${product}`,function(data){
                data = JSON.parse(data);
                console.log(data);
                data.forEach(d=>{
                    $('#unit').append(`<option class="new_p" value="${d}">${d}</option>`)
                })
            })
        })
        $('#units').select2({
            tags: true,
            placeholder: "Please Enter Units Of Measurement"
        });
        $('#units').change(function(){
            $('.new_u, .new_up, .new_uc, .new_re').remove();
            let product_name = $('#product_name').val();
            let units = $(this).val();
            let units_recieved = $('#unit_recieved').val();
            units.forEach((unit)=>{
                $('#unit_recieved').append(`<option class="new_u"  value="${unit}">${unit} </option>`)
                let price_form = `
                <div class="form-group new_up">
                    <label>Cost of ${product_name} per ${unit} </label>
                    <input type="number" name="price[]" class=" form-control">
                `;

                let cost_form = `
                <div class="form-group new_uc">
                    <label>Price of ${product_name} per ${unit} </label>
                    <input type="number" name="cost[]"  class="form-control">
                `;
                $('.inputs').append(price_form);
                $('.inputs').append(cost_form);

            })

            if(units.length <2) return false;
            // console.log(units);
            let low = units[0];
            let high = units[1];
                let form = `
                <div class="new_re form-group">
                    <label>How many ${low} makes 1 ${high} </label>
                    <input type="text" name="relation" id="relation" class="form-control">
                `;
                $('.inputs').append(form);

        });

        $('#productForm').submit(function(e){
            e.preventDefault();
            let form = new FormData(this);
            form.append('add_product',"");
            $.ajax({
                type:'POST',
                url: 'parser.php',
                data:form,
                cache:false,
                processData:false,
                contentType:false,
                success: function(data){
                    console.log(data);
                    location.reload(true);
                },
                error: function(xhr){

                    console.log(xhr)
                }
            })
        })

        $('#products').change(function(e){
            $('.new_p').remove();
            let product = $(this).val();
            $.get(`parser.php?get_units=1&product_id=${product}`,function(data){
                data = JSON.parse(data);
                console.log(data);
                data.forEach(d=>{
                    $('#unit').append(`<option class="new_p" value="${d}">${d}</option>`)
                })
            })
        })

        $('#buy_product').submit(function(e){
            e.preventDefault();
            let form = new FormData(this);
            form.append('buy_product','');
            $.ajax({
                type:'POST',
                url: 'parser.php',
                data:form,
                cache:false,
                processData:false,
                contentType:false,
                success: function(data){
                    console.log(data);
                    data = JSON.parse(data);
                    if(data['status'] == 0){
                        alert(data['message']);
                        return;
                    }

                    location.reload(true);
                },
                error: function(xhr){
                    console.log(xhr)
                }
            })
        })
    </script>
</body>
</html>