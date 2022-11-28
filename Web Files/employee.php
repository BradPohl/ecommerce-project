<html><head>
    <title>Pickle's Employee Page</title>
</head></html>

<?php
    include('secrets.php');
    try { // if something goes wrong, an exception is thrown
        $dsn = "mysql:host=courses;dbname=z1923374";
        $pdo = new PDO($dsn, $username, $password);
        $var = "nothing";
        
        echo "<h1> Pickle's Sweet Shop</h1>";
        echo "<h3>Employee View</h3>";
        //let employees view inventory/orders
        echo "<form action=\"?\" method=\"POST\">";
            echo "<input type=\"radio\" name=\"employeeChoice\" value=\"CART\"> View Orders<br/>";
            echo "<input type=\"radio\" name=\"employeeChoice\" value=\"PRODUCT\"> View Inventory<br/>";
            echo "<input type=\"radio\" name=\"employeeChoice\" value=\"UPDATE\"> Update Orders<br/><br/>";
            echo "<input type=\"submit\" name=\"submitEmployeeChoice\" value=\"Submit\">";
        echo "</form>";
        
        echo '__________________________________________________________________________________________________________________';
        echo "<br/>";
        



        if(isset($_POST['submitEmployeeChoice'])){
            //create a variable to store choice
            if(isset($_POST['employeeChoice']))
                $var = $_POST['employeeChoice'];

            //View inventory    
            if($var == "PRODUCT"){
                echo "<h2>Showing All Products</h2>";
                $sql = 'SELECT * FROM ' . $_POST['employeeChoice'];
                $result = $pdo->query($sql);
                echo "<table border=1 cellspacing=1>";
                    echo "<th style=\"width:80px\">" . "Product ID" . "</th><th style=\"width:175px\">" . "Name" . "</th><th style=\"width:55px\">" . "Price" . "</th><th style=\"width:80px\">" . "Quanitity" . "</th><th style=\"width:300px\">" . "Description" . "</th>";
                    echo "</table>";
                foreach($result as $result){
                    echo "<table border=1 cellspacing=1>";
                        echo "<tr>";
                            echo "<td style=\"width:80px\">" . $result["PRODUCTID"] . "</td><td style=\"width:175px\">" . $result["NAME"] . "</td><td style=\"width:55px\">" . $result["PRICE"] . "</td><td style=\"width:80px\">" . $result["QUANTITY"] . "</td><td style=\"width:300px\">" . $result["DESCR"] . "</td>";
                        echo "</tr>";
                    echo "</table>";
                }
            }
            //View Orders
            else if($var == "CART"){ //$_POST['employeeChoice']
                echo "<h2>Showing Orders</h2>";
                

                //get all info needed for table
                $sql = 'SELECT DISTINCT FILLCART.PRODUCTID, PRODUCT.NAME, FILLCART.ORDERNO, FILLCART.QUANTITY, PRODUCT.PRICE*FILLCART.QUANTITY, CART.STATUS, CART.NOTE FROM ORDERCART, CART, FILLCART, PRODUCT WHERE ORDERCART.ORDERNO = FILLCART.ORDERNO AND FILLCART.PRODUCTID = PRODUCT.PRODUCTID AND CART.ORDERNO = FILLCART.ORDERNO';
                $exe = $pdo->prepare($sql);
                $exe->execute();

                echo "<table border=1 cellspacing=1>";
                echo "<th style=\"width:80px\">" . "Product ID" . "</th><th style=\"width:175px\">" . "Product Name" . "</th><th style=\"width:80px\">" . "OrderNo." . "</th><th style=\"width:80px\">" . "Quanitity" . "</th><th style=\"width:60px\">" . "Total" . "</th><th style=\"width:90px\">" . "Order Status" . "</th>";
                echo "</table>";
                $total = 0;
                $orderTotal = 0;
                $temp = 0;
                foreach($exe as $row)
                {   //do math then print table row just in case new table is started
                    if($temp == $row[2] || $temp == 0)
                    {
                        $note = $row[6];
                        $orderTotal += $row[4];
                        $temp = $row[2];
                    }
                    else
                    {
                        echo "Notes: " . $note;
                        echo "</br></br>";
                        $orderTotal = $row[4];
                        $temp = $row[2];
                        $note = NULL;
                    }
                    $total += $row[4];
                    echo "<table border=1 cellspacing=1>";
                        echo "<tr>";
                            echo "<td style=\"width:80px\">" . $row[0] . "</td><td style=\"width:175px\">" . $row[1] . "</td><td style=\"width:80px\">" . $row[2] . "</td><td style=\"width:80px\">". $row[3] . "</td><td style=\"width:60px\">" . "$" . $row[4] . "</td><td style=\"width:90px\">" . $row[5] . "</td>";
                        echo "</tr>";
                    echo "</table>";
                }
                echo "Notes: " . $note;
                $note = NULL;
            }
            //Update Orders
            else if ($var == "UPDATE"){
                echo "<h2>Update an Order</h2>";
                echo "<form action=\"?\" method=\"POST\">";
                echo "<label for=\"UpdateOrder\">Choose Order to update: </label>";
                echo "<select name=\"order\" id=\"allOrders\">";    //drop down menu
                echo "<option value=\"default\">None</option>";
                $sql = 'SELECT * FROM CART'; //get all info needed
                $result = $pdo->query($sql);

                $result = $result->fetchAll(PDO::FETCH_ASSOC);
                foreach($result as $result){ //print info requested
                    echo "<option value=\"" . $result['ORDERNO'] . "\"> " . $result['ORDERNO'] . "</option>";
                }

                echo "</select>";

                echo "</br></br>";  //radio input to update order status
                echo "<input type=\"radio\" name=\"updateStatus\" value=\"In Cart\"> In Cart";
                echo "</br>";
                echo "<input type=\"radio\" name=\"updateStatus\" value=\"Processing\"> Processing Order";
                echo "</br>";
                echo "<input type=\"radio\" name=\"updateStatus\" value=\"Shipping\"> Shipping";
                echo "</br>";
                echo "<input type=\"radio\" name=\"updateStatus\" value=\"Delivered\"> Delivered";
    
                echo "</br></br>";


                echo "<label for=\"UpdateOrder\">Choose Order Number to update Notes: </label>";
                echo "<select name=\"orderNotes\" id=\"noteOrders\">";    //drop down menu
                echo "<option value=\"default\">None</option>";
                $sql = 'SELECT * FROM CART'; //get info
                $result = $pdo->query($sql);

                $result = $result->fetchAll(PDO::FETCH_ASSOC);
                foreach($result as $result){ //print dropdown menu
                    echo "<option value=\"" . $result['ORDERNO'] . "\"> " . $result['ORDERNO'] . "</option>";
                }
                echo "</select>";

                echo "</br>";
                echo "Notes: " . "<input type=\"text\" id=\"note\" name=\"notes\" placeholder=\"Note Here\">";



                echo "</br></br>";
                echo "<input type=\"submit\" name=\"submitStatusUpdate\" value=\"Submit\">";
                echo "</form>"; 
            }
            else{
                echo "Please select a choice.";
            }
        }

        //put at end to check after complete submission
        if(isset($_POST['submitStatusUpdate'])){
            if(isset($_POST['updateStatus'])){  //update status
                $sql = 'UPDATE CART SET STATUS = :S WHERE ORDERNO = :O';
                $exe = $pdo->prepare($sql);
                $exe->execute(['S' => $_POST['updateStatus'], 'O' => $_POST['order']]);
            }

           if(isset($_POST['notes'])){
                //update notes
                $sql = 'UPDATE CART SET NOTE = :notes WHERE ORDERNO = :order';
                $prepared = $pdo->prepare($sql);
                $prepared->execute(['notes' => $_POST['notes'], 'order' => $_POST['orderNotes']]);
            }
        }   
    }
    catch(PDOexception $e) { // handle that exception
        echo "Connection to database failed: " . $e->getMessage();
    }


?>
