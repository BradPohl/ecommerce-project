# ecommerce-project
project for 466

Customer Page: https://students.cs.niu.edu/~z1923374/customer.php?
Employee Page: https://students.cs.niu.edu/~z1923374/employee.php?



This was a group project we wrote for my databases class where we created
a basic ecommerce website from scratch while following a guideline of goals
to meet when we turn it in.


ER DIAGRAM EXPLANATION BELOW:

PRODUCT ENTITY:
The first entity to look at is the Product, which has the attributes price,
Quantity, a description, and name of the products and we give each product a 
ProductID to have a primary key.

CART - PRODUCT RELATIONSHIP:
Cart has a 1 to many relationship with Product called FILLCART
because one cart can hold multiple products in the shopping cart.
There is an attribute called quanitity that holds the amount of
product in the cart.

Cart ENTITY:
The Cart entity has a status attribute and a NOTE attribute and an OrderNo primary 
key to allow for easy access to track orders as they are in the shopping cart, 
processing, shipping, or delivered.

USER ENTITY:
The User entity has a UserID primary key to allow for differentiation between
users and an email attribute to allow for customer differentiation.

USER - CART RELATIONSHIP:
User then has a one-to-many relationship with Cart called ORDERCART that takes
the billing address, shipping address, ccnumber, ccname, cvv, and holds a boolean
attribute that gets flipped to true when the order is purchased.
