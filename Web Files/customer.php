<html><head>
    <title>Pickle's Customer Page</title>
</head></html>

<html><head><style>
</style></head></html>
<?php
     include('secrets.php');
     include('group-project-style-sheet.html');
    try { // if something goes wrong, an exception is thrown
        $dsn = "mysql:host=courses;dbname=z1923374";
        $pdo = new PDO($dsn, $username, $password);


        if (isset($_POST['Email'])) // if the first email form was submitted, save it as hidden variable
        {
            $_POST['hiddenEmail'] = $_POST['Email'];
        }
        else // get email address 
        {
            if (!isset($_POST['hiddenEmail']))
            {
                echo "<div class=\"background-main\">"; //sets the background image to a sour roll
                    echo "<div class=\"center\">"; //centers the entire first page
                   
                        echo "<h1> Pickle's Sweet Shop</h1>"; //header for main page
            
                        echo "<form action=\"?\" method=\"POST\">"; //beginning of form
                        echo "<p>Enter Email: <input type=\"email\" name=\"Email\" value=\"\"></p>"; //enter the email from user input
                            echo "<input type=\"submit\" name=\"email-login\" value=\"Submit\">"; //submit button
                        echo "</form>"; //end of form
                        echo "</div>";

                        echo "<div class=\"footer\">"; //creator credits at bottom of page
                            echo "Created By <br>Brad Pohlman, Daniel Wilczynski, Kenneth Hetherington";
                    echo "</div>";



            }
        }
        $returning_user = false;

        if (isset($_POST['hiddenEmail'])) // after email address is stored
        {
            echo "<h1> Pickle's Sweet Shop</h1>";
            
            $sql = 'SELECT * FROM USER WHERE EMAIL = \''.$_POST['hiddenEmail'] .'\';';
            $prepared = $pdo->prepare($sql);
            $success = $prepared->execute();
                
            $rows = $prepared->fetchALL(PDO::FETCH_ASSOC);
            foreach($rows as $row)
            {
                echo "Welcome back ". $row['EMAIL']."!<br/>" ;
                $returning_user = true;
                $id = $row['USERID']; 
            }
            
            // if email address is new, add it to the table
            if (!$returning_user)
            {
                echo "Welcome to our store ".$_POST['hiddenEmail'] . "!<br/>";
                
                $sql = "INSERT INTO USER(EMAIL) VALUES ('". $_POST['hiddenEmail'] ."');";
                $prepared = $pdo->prepare($sql);
                $success = $prepared->execute();
                if(isset($rows['USERID']))
                   $id = $rows['USERID'];
            }
            echo "<h3>What would you like to do?</h3>";

            echo "<form action=\"?\" method=\"POST\">";
                echo "<input type=\"radio\" name=\"customerChoice\" value=\"viewCart\"> View Shopping Cart<br/>";
                echo "<input type=\"radio\" name=\"customerChoice\" value=\"addCart\"> Add to Cart<br/>";
                echo "<input type=\"radio\" name=\"customerChoice\" value=\"checkout\"> Checkout<br/>";
                echo "<input type=\"radio\" name=\"customerChoice\" value=\"viewOrders\"> View Orders<br/><br/>";
                echo "<input type=\"submit\" name=\"submitEmployeeChoice\" value=\"Submit\"><br/>";
                echo "<input type=\"hidden\" name=hiddenEmail value = \"".$_POST['hiddenEmail']."\">";
            echo "</form>";
            echo "___________________________________________________________________________________________________<br/>";

/**************************************************************************
*
* Add to Cart/updating the database
*
***************************************************************************/
            if (isset($_POST['submitAddCart']))
            {
                $total = 0;

                $sql = "SELECT * FROM PRODUCT;";
                $prepared = $pdo->prepare($sql);
                $success = $prepared->execute();
                
                $rows = $prepared->fetchALL(PDO::FETCH_ASSOC);
                       

                foreach($rows as $row)
                {
                    if (isset($_POST[$row["PRODUCTID"]  ."qty"]) && $_POST[$row["PRODUCTID"]  ."qty"] > 0)
                    {
                        $addProductID = $row["PRODUCTID"];
                        $addQTY = $_POST[$row["PRODUCTID"]  ."qty"];
                        $addProdName = $row["NAME"]; 

                        $sql = "SELECT ORDERCART.ORDERNO FROM ORDERCART, USER, CART WHERE USER.EMAIL='".$_POST['hiddenEmail']."' AND USER.USERID = ORDERCART.USERID AND CART.ORDERNO = ORDERCART.ORDERNO AND CART.STATUS ='CART';";
                        $prepared = $pdo->prepare($sql);
                        $success = $prepared->execute();

                        $rows = $prepared->fetchALL(PDO::FETCH_ASSOC);
                        
                        foreach($rows as $row)
                        {
                            $addOrderNO = $row['ORDERNO'];
                        }

                        //check if productid is already in cart 
                        $sql = "SELECT QUANTITY FROM FILLCART  WHERE PRODUCTID = ".$addProductID." AND ORDERNO = ".$addOrderNO.";";
                        
                        $prepared = $pdo->prepare($sql);
                        $success = $prepared->execute();

                        $rows = $prepared->fetchALL(PDO::FETCH_ASSOC);

                        foreach($rows as $row)
                        {
                            $addPrevQty = $row['QUANTITY'];
                        }

                        if (isset($row['QUANTITY']))// if the product is already in the cart 
                        { 
                            $sql = "SELECT QUANTITY FROM PRODUCT  WHERE PRODUCTID = ".$addProductID.";";

                            $prepared = $pdo->prepare($sql);
                            $success = $prepared->execute();

                            $rows = $prepared->fetchALL(PDO::FETCH_ASSOC);

                            foreach($rows as $row)
                            {
                                $addStockQty = $row['QUANTITY'];
                            }
                            
                            if(($addQTY + $addPrevQty) > $addStockQty )// if the qty requested exceeds our qty in stock
                            {
                                $addQTY = ($addStockQty - $addPrevQty);
                                $sql = "UPDATE FILLCART SET QUANTITY = ".$addStockQty." WHERE PRODUCTID = ".$addProductID." AND ORDERNO = ". $addOrderNO.";";
                                $prepared = $pdo->prepare($sql);
                                $success = $prepared->execute();

                                echo "We only have " . $addStockQty. " in stock, adding " . $addQTY. " ". $addProdName ."(s) to your cart!<br/>";
                            }
                            else
                            {
                                $addPrevQty = ($addQTY+ $addPrevQty);
                                $sql = "UPDATE FILLCART SET QUANTITY = ".$addQTY." WHERE PRODUCTID = ".$addProductID." AND ORDERNO = ". $addOrderNO.";";
                                $prepared = $pdo->prepare($sql);
                                $success = $prepared->execute();
                                echo "Adding " . $addQTY. " ". $addProdName ."(s) to your cart!<br/>";
                            }
    
                        }// end of adding existing product to cart 
                        
                        else
                        {
                            $sql = "INSERT INTO FILLCART VALUES(".$addProductID.",".$addOrderNO.",".$addQTY.");";

                            $prepared = $pdo->prepare($sql);
                            $success = $prepared->execute();
                            echo "Adding " . $addQTY. " ". $addProdName ."(s) to your cart!<br/>";
                        }// end of adding new product to cart 

                    }
                }
                echo "<br/>";
            }

/** CART DETAIL PAGE
 * This script is part of the addCart page, where if a user selects details on any one specific product
 * Then a Product description of the product selected displays the description, how much of said product is in stock
 * and the price for each unit of that product, the user is also able to add an amount of this specific product from this page
 * by entering an amount and submitting using the "Add to Cart" button
 */
            $sql = "SELECT PRODUCTID FROM PRODUCT";
            $prepared = $pdo->prepare($sql);
            $success = $prepared->execute();

            $rows = $prepared->fetchALL(PDO::FETCH_ASSOC);
            echo "<form action=\"?\" method=\"POST\">";

            // go through each product id and check if view more details was submitted 
            foreach($rows as $row)
            {
                if (isset($_POST[$row["PRODUCTID"]."details"]))
                {
                    $sql = "SELECT * FROM PRODUCT WHERE PRODUCTID = \"".$row["PRODUCTID"]."\";";
                    $prepared = $pdo->prepare($sql);
                    $success = $prepared->execute();
                    $prodDetails = $prepared->fetchALL(PDO::FETCH_ASSOC);

                    foreach($prodDetails as $prodDetail)
                    {
                        echo "<h4>Product description: </h4>".$prodDetail['DESCR']."<br/>";
                        echo $prodDetail['QUANTITY']." " . $prodDetail['NAME'] ."(s) in stock for $".$prodDetail['PRICE']." each.<br/><br/>";

                        echo "<input type=\"number\" name=\"" . $prodDetail["PRODUCTID"]  . "qty\" min=\"0\" max=\"". $prodDetail["QUANTITY"]."\"> ";
                        echo "<input type=\"submit\" name=\"submitAddCart\" value=\"Add To Cart\"><br/>";
                        echo "<input type=\"hidden\" name=hiddenEmail value = \"".$_POST['hiddenEmail']."\">"; 
                    }
                }// end of details isset() statement 
            }// end of foreach looping through each productID 
            echo "</form>";

/**CREATING A CART FOR NEW USERS
 * For a user that has not entered an email that has already been entered we create a new cart for that new user. 
 * If the user enters an email not previsouly entered they do not have a cart and as such $hasCart is false, 
 * 
 */
            $sql = "SELECT * FROM ORDERCART, USER, CART WHERE USER.USERID = ORDERCART.USERID AND CART.ORDERNO = ORDERCART.ORDERNO AND USER.EMAIL = '" .$_POST['hiddenEmail'] . "' AND CART.STATUS = 'CART';"; 
            $prepared = $pdo->prepare($sql);
            $success = $prepared->execute();

            $rows = $prepared->fetchALL(PDO::FETCH_ASSOC);
            $hasCart = false;

            foreach($rows as $row)
            {
                $hasCart = true;
            }
            if (!$hasCart)
            {
                // create cart with status =  cart
                $sql = "INSERT INTO CART (STATUS) VALUES('CART');";
                $prepared = $pdo->prepare($sql);
                $success = $prepared->execute();

                // get the orderno from that new guy 
                $sql = "SELECT MAX(ORDERNO) FROM CART;";
                $prepared = $pdo->prepare($sql);
                $success = $prepared->execute();

                $rows = $prepared->fetchALL(PDO::FETCH_ASSOC);

                foreach($rows as $row)
                {
                    $orderNO = $row['MAX(ORDERNO)']; 
                }

                // get the userid
                $sql = "SELECT USERID FROM USER WHERE EMAIL='" .$_POST['hiddenEmail']  ."';";
                $prepared = $pdo->prepare($sql);
                $success = $prepared->execute();

                $rows = $prepared->fetchALL(PDO::FETCH_ASSOC);

                foreach($rows as $row)
                {
                    $userID = $row['USERID'];
                }

                // create ordercart with userid and orderno 
                $sql = "INSERT INTO ORDERCART (USERID, ORDERNO) VALUES(". $userID .",". $orderNO .")";
                $prepared = $pdo->prepare($sql);
                $success = $prepared->execute();

                $rows = $prepared->fetchALL(PDO::FETCH_ASSOC);

                foreach($rows as $row)
                {
                    $userID = $row['USERID'];
                }
                
            }// end of cart and user creation

/** CONFIRM CHECKOUT
 * If all the appropriate information has been entered for the checkout portion of this project then ORDERCART and CART are updated with the correct
 * information that the user has input. After the appropriate tables are updated echo out a purchase complete statement for the customer
 * 
 */
            if (isset($_POST['ccNum']) or isset($_POST['ccCvv'])  or isset($_POST['ccName'])  or isset($_POST['ccAddress']))
            {
                if ($_POST['ccNum']>0 and $_POST['ccCvv']>0  and $_POST['ccName']!=""  and $_POST['ccAddress'] !="")
                {
                    $sql = "SELECT ORDERCART.ORDERNO FROM ORDERCART, USER, CART WHERE USER.EMAIL='".$_POST['hiddenEmail']."' AND USER.USERID = ORDERCART.USERID AND CART.ORDERNO = ORDERCART.ORDERNO AND CART.STATUS ='CART';";
                    $prepared = $pdo->prepare($sql);
                    $success = $prepared->execute();
                    $rows = $prepared->fetchALL(PDO::FETCH_ASSOC);

                    foreach($rows as $row)
                    {
                        $checkoutOrderNO = $row['ORDERNO'];
                    }

                    $sql = "UPDATE ORDERCART SET PURCHASED = 1 WHERE ORDERNO = ".$checkoutOrderNO.";"; 
                    $prepared = $pdo->prepare($sql);
                    $success = $prepared->execute();

                    $sql = "UPDATE CART SET STATUS = \"Processing\" where ORDERNO = ".$checkoutOrderNO.";";
                    $prepared = $pdo->prepare($sql);
                    $success = $prepared->execute();

                    $sql = "SELECT PRODUCTID, QUANTITY FROM FILLCART WHERE ORDERNO =".$checkoutOrderNO.";";
                    $prepared = $pdo->prepare($sql);
                    $success = $prepared->execute();
                    $rows = $prepared->fetchALL(PDO::FETCH_ASSOC);
                    
                    foreach($rows as $row)
                    {
                        $sql = "UPDATE PRODUCT SET QUANTITY=(QUANTITY-". $row['QUANTITY'] .") WHERE PRODUCTID=". $row['PRODUCTID'] .";";
                        $prepared = $pdo->prepare($sql);
                        $success = $prepared->execute();
                    }



                    echo "Purchase Complete!<br/>";
                }
                else
                {
                    echo "Please fill out all fields.<br/>";
                    $_POST['customerChoice'] = "checkout";
                }
            }

/**UPDATE TO FILL CART
 * This updates the fill cart quantity when the quantity submit is chosen in the view cart, if the quantity updated is set to '0'
 * then that product and its relevenant contents are deleted from the table, removing it from view of the customer
 */
if(isset($_POST['quantitySubmit']))
{
    $sql = 'UPDATE FILLCART, ORDERCART SET FILLCART.QUANTITY = :Q WHERE FILLCART.PRODUCTID = :P  AND FILLCART.ORDERNO = ORDERCART.ORDERNO AND ORDERCART.USERID = :userid';
    $prepared = $pdo->prepare($sql);
    $exe = $prepared->execute(['Q' => $_POST['quantityInput'], 'P' => $_POST['updateEntry'], 'userid' => $id]);

    //remove if quantity is 0
    if($_POST['quantityInput'] == 0)
    {
        $sql = 'DELETE FROM FILLCART WHERE QUANTITY = 0';
        $prepared = $pdo->prepare($sql);
        $prepared->execute();
    }
    $_POST['customerChoice'] = "viewCart";
}

/** VIEW CART
 * If customer choice is "viewCart" a simple welcome statement is echoed out to the customer,
 * The customer then will see a complete list of all the products they added to their cart from the product
 * list found in another section of this program.
 * 
 * The product name, product ID initial price of the product, quantity of the product the customer ordered, and the 
 * accumulated price for each individual product will be listed the customer in a neat table
 * 
 * The sum total of the products will be listed at the bottom. The customer has the option of updating their cart by 
 * selecting the product name and adjusting the quantity as such. Once the submit button "update quantity" is selected the 
 * customer will see their products in their shopping cart updated as well as all relevant information.
 */
            if (isset($_POST['customerChoice'])){
                if ($_POST['customerChoice'] == "viewCart")
                {
                    //initial welcome statement for the shopping cart
                    echo "<h2>Welcome to your shopping cart!</h2>";

                    //for getting the total of the shopping cart 
                    $total = 0;
                
                    //select statement to get all the necessary information for printing data in the shopping cart
                    $sql = "SELECT PRODUCT.NAME, PRODUCT.PRICE, FILLCART.QUANTITY, PRODUCT.PRODUCTID FROM FILLCART, PRODUCT, CART, ORDERCART WHERE FILLCART.PRODUCTID = PRODUCT.PRODUCTID AND CART.ORDERNO = FILLCART.ORDERNO AND ORDERCART.USERID = " . $id . " AND ORDERCART.ORDERNO = FILLCART.ORDERNO AND CART.STATUS = 'CART'";
                    $stmt = $pdo->prepare($sql);
                    $success = $stmt->execute();
                    $rows = $stmt->fetchALL(PDO::FETCH_ASSOC); //for getting all the necessary data
            
                    //table border containing the appropriate headers for the customer
                    echo "Here are the Current contents of your shopping cart.<br/><br/>";
                    echo    "<table border=1 cellspacing=1>";
                        echo    "<tr>";
                        echo    "<th>Candy Name</th>"; 
                        echo    "<th>Product ID</th>";
                        echo    "<th>Price of Product</th>";
                        echo    "<th>Quantity of Product</th>";
                        echo    "<th>Accumulated Price</th>";
                        echo    "</tr>";
                    foreach($rows as $row)//displaying the selected data from the "addCart" page here in table format
                    {
                        echo "<tr>";
                        echo "<td>" .$row["NAME"]. "</td><td>" . $row["PRODUCTID"] . "</td><td>$" . $row["PRICE"] . "</td><td>" . $row['QUANTITY'] . "</td><td> $" . $row['PRICE'] * $row['QUANTITY'] . "</td>";
                        echo "</tr>";
                        $total += $row['PRICE'] * $row['QUANTITY'];
                    }
                    echo "</table>"; //end of displaying the table data

                    //Form to update the quantity of each product one by one
                    echo "<form action=\"?\" method=\"POST\">";
                        echo "<label for=\"UpdateOrder\"<br/><br/>Choose entry to Update: </label>";
                        echo "<select name=\"updateEntry\" id=\"update\">";    //drop down menu
                        echo "<option value=\"default\">None</option>";
                        foreach($rows as $row)
                        {
                            echo "<option value=\"" . $row['PRODUCTID'] . "\"> " . $row['NAME'] . "</option>";
                        }
                    echo "</select>";
                    echo "<input type=\"number\" min=\"0\" name=\"quantityInput\" style=\"width:60px\">";

                    echo " ";
                    echo "<input type=\"submit\" name=\"quantitySubmit\" value=\"Update Quantity\">";
                    echo "<input type=\"hidden\" name=\"hiddenEmail\" value = \"".$_POST['hiddenEmail']."\">";
                    echo "</form>"; //end of update cart quantity form

                    echo "</br>" . "Cart Total: $" . $total . "</br></br>";//echo the sum total of all the products
                }
/** ADD TO CART
 * Add to cart will allow the user to browse a list of products available in this case candy.
 * There will be a table containing the list of products displaying their name, price for each 
 * individual unit, quantity available for each product, a user input text box where the user can choose how much
 * of each product they would like to add to their cart, and a detail coloumn that list how much of said product is available,
 * the price of the product and a brief description of the product.
 * 
 * After the user enters the amount of products they would like for each row, the user then selects the "Add to Cart" form at the bottom and a list
 * describing which products were added to their cart will appear. This will then update the table "fillcart" which is relevant to the "View shopping cart"
 * section of this project
 */
                else if ($_POST['customerChoice'] == "addCart")
                {
                    echo "<h2>Browse Candy</h2>";
                    echo "<form action=\"?\" method=\"POST\">";
                        $sql = "SELECT * FROM PRODUCT;"; //sql statement that list all the contents of the product table for the customer to view
                        $prepared = $pdo->prepare($sql);
                        $success = $prepared->execute();

                        $rows = $prepared->fetchALL(PDO::FETCH_ASSOC);
                        echo "<br/><table border=1 cellspacing=1>"; //table border formatting
                        echo "<tr>";
                            echo "<td>Item Name </td><td> Price </td><td> Quantity Available </td><td> Add to Cart </td><td> More Details </td>"; //table formatting for easy readability
                        echo "</tr>";

                        //displays all the relevant information for each table header for each product in stock
                        /*displays the name,price, quantity available, gets user input for how much they would like to add to their cart, and a final coloumn that displays 
                        the description of each product*/
                        foreach($rows as $row)
                        {
                            echo "<tr>";
                                echo "<td> ".$row["NAME"]." </td><td>$".$row["PRICE"]."</td><td>".$row["QUANTITY"]."</td>
                                <td> <input type=\"number\" name=\"" . $row["PRODUCTID"]  . "qty\" style=\"width:75px\" min=\"0\" max=\"". $row["QUANTITY"]."\"></td>
                                <td> <input type=\"submit\" name=\"".$row["PRODUCTID"]."details\" style=\"width:85px\" value=\"view\"></td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                        
                        echo "<br/><input type=\"submit\" name=\"submitAddCart\" value=\"Add To Cart\"><br/>";
                        echo "<input type=\"hidden\" name=hiddenEmail value = \"".$_POST['hiddenEmail']."\">";
                    echo "</form>";
                }
/** CHECKOUT
 * This page Ask the user to enter their credit cart information, CVV number
 * Credit Card holder information, and the billing address. After entering all relevant information
 * the user should the select "Purchase Order" which will then update the view orders database and see
 * all orders that have been sent to processing as requested by the project requirements
 */
                else if ($_POST['customerChoice'] == "checkout")
                {
                    echo "<h3> Checkout </h3><br/>"; //header statement
                    

                    $sql = "SELECT PRODUCT.NAME, PRODUCT.PRICE, FILLCART.QUANTITY, PRODUCT.PRODUCTID FROM FILLCART, PRODUCT, CART, ORDERCART WHERE FILLCART.PRODUCTID = PRODUCT.PRODUCTID AND CART.ORDERNO = FILLCART.ORDERNO AND ORDERCART.USERID = " . $id . " AND ORDERCART.ORDERNO = FILLCART.ORDERNO AND CART.STATUS = 'CART'";                
                    $stmt = $pdo->prepare($sql);
                    $success = $stmt->execute();
                    $rows = $stmt->fetchALL(PDO::FETCH_ASSOC); //for getting all the necessary data
                    
                    $total = 0;
                    foreach($rows as $row)//displaying the selected data from the "addCart" page here in table format
                    {   
                        $total += $row['PRICE'] * $row['QUANTITY'];
                    }
                    
                    echo "Your total is $" .$total. "<br/><br/>";



                    echo "<form action=\"?\" method=\"POST\">"; //form for submitting necessary information from the customer to process the orders
                    
                        echo "Credit Card Number <input type=\"number\" name=\"ccNum\" min=\"0\" max=\"9999999999999999\"><br/>";
                        echo "CVV  <input type=\"number\" name=\"ccCvv\" min=\"0\" max=\"999\"><br/>";
                        echo "Credit Card Holder <input type=\"text\" name=\"ccName\"><br/>";
                        echo "Billing Address <input type=\"text\" name=\"ccAddress\"><br/>";
                        echo "<br/><input type=\"submit\" name=\"submitAddCart\" value=\"Purchase Order\"><br/>";
                        echo "<input type=\"hidden\" name=hiddenEmail value = \"".$_POST['hiddenEmail']."\">";

                    echo "</form>";
                }
/** VIEW ORDERS
 * This section shows the previous orders that a customer has filled out and completed all previous steps. (Add to cart, and checkout)
 * This portion will show a table that list the product ID, Product name, Order No. Quantity of product ordered, The dollar amount
 * that is the sum amount of the product ordered and the order status (dependant on whether the customer checked out their product) 
 * 
 * There will then be statements below listing the order number's total dollar amount and a grand total for the order that was processed
 */
                else
                {
                    echo "<h2>Previous Orders</h2>" ;

                    $sql = 'SELECT DISTINCT FILLCART.PRODUCTID, PRODUCT.NAME, FILLCART.ORDERNO, FILLCART.QUANTITY, PRODUCT.PRICE*FILLCART.QUANTITY, CART.STATUS FROM ORDERCART, CART, FILLCART, PRODUCT WHERE ORDERCART.USERID = :userid AND ORDERCART.ORDERNO = FILLCART.ORDERNO AND FILLCART.PRODUCTID = PRODUCT.PRODUCTID AND CART.ORDERNO = FILLCART.ORDERNO';
                    $exe = $pdo->prepare($sql);
                    if(!isset($id))
                        $id = 0;
                    $exe->execute(['userid' => $id]);
                    
                    $hasPrevOrders = false; //If no user has entered any information for an order then their are no previous orders

                    foreach($exe as $row)
                    {
                        $hasPrevOrders = true;
                    }
                    
                    $sql = 'SELECT DISTINCT FILLCART.PRODUCTID, PRODUCT.NAME, FILLCART.ORDERNO, FILLCART.QUANTITY, PRODUCT.PRICE*FILLCART.QUANTITY, CART.STATUS FROM ORDERCART, CART, FILLCART, PRODUCT WHERE ORDERCART.USERID = :userid AND ORDERCART.ORDERNO = FILLCART.ORDERNO AND FILLCART.PRODUCTID = PRODUCT.PRODUCTID AND CART.ORDERNO = FILLCART.ORDERNO';
                    $exe = $pdo->prepare($sql);
                    if(!isset($id))
                        $id = 0;
                    $exe->execute(['userid' => $id]);

                    if (!$hasPrevOrders)
                        echo "You don't have any previous orders yet!";
                    else{
                        echo "<table border=1 cellspacing=1>";
                        echo "<th style=\"width:80px\">" . "Product ID" . "</th><th style=\"width:175px\">" . "Product Name" . "</th><th style=\"width:80px\">" . "Tracking No." . "</th><th style=\"width:80px\">" . "Quanitity" . "</th><th style=\"width:60px\">" . "Total" . "</th><th style=\"width:90px\">" . "Order Status" . "</th>";
                        echo "</table>";
                        $total = 0;
                        $orderTotal = 0;
                        $temp = 0;
                        foreach($exe as $row)
                        {
                            if($temp == $row[2] || $temp == 0)
                            {
                                $orderTotal += $row[4];
                                $temp = $row[2];
                            }
                            else
                            {
                                echo "   " . $temp . "'s Order total: $" . $orderTotal . "</br></br>";
                                $orderTotal = $row[4];
                                $temp = $row[2];
                            }
                            $total += $row[4];
                            echo "<table border=1 cellspacing=1>";
                                echo "<tr>";
                                    echo "<td style=\"width:80px\">" . $row[0] . "</td><td style=\"width:175px\">" . $row[1] . "</td><td style=\"width:80px\">" . $row[2] . "</td><td style=\"width:80px\">". $row[3] . "</td><td style=\"width:60px\">" . "$" . $row[4] . "</td><td style=\"width:90px\">" . $row[5] . "</td>";
                                echo "</tr>";
                            echo "</table>";
                        }
                        if($temp != 0)
                            echo "   " . $temp . "'s Order total: $" . $orderTotal;
                        if($total != 0)
                            echo "</br>" . "Total: $" . $total;
                   } // end of $$hasPrevOrders == true
                } // end of customer choice == View Previous Orders
            } // end of isset(customer choice)
        } // end of isset(hidden email)
    }// end of try 

    catch(PDOexception $e)//Handle that exception
    { 
        echo "Connection to database failed: " . $e->getMessage();
    }
?><!--end of php scripting -->
