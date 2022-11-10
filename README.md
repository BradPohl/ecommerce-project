# ecommerce-project
project for 466


ER DIAGRAM EXPLANATION BELOW:

PRODUCT ENTITY:
The first entity to look at is the Product, which has the attributes price,
Quantity, and name of the products and we give each product a ProductID to
have a primary key.

USER - PRODUCT RELATIONSHIP:
Product then has a 1 to many relationship with user called ShoppingCart
because one user can put multiple products in the shopping cart.

ORDER - PRODUCT RELATIONSHIP:
The ShoppingCart relationship is meant to pull product entered by the user
then allow the order to track the status going from "shopping cart" to
"processing" to "shipped" to "delivered".
Order has a 1 to many relationship with Product because only one Order will
be fulfilled at a time. We don't have very many employees yet, we're a new
business.

SHOPPINGCART RELATIONSHIP:
The ShoppingCart relationship will track information and give 
access to the Product entity, Order entity, and User entity through 
foreign keys. The ShoppingCart relationship shows the Price, Quantity of 
items, item names so the user knows what they are buying and so the order 
can be put through.

ORDER ENTITY:
The Order entity has a status attribute and an OrderNo primary key to allow
for easy access to track orders as they are in the shopping cart, processing
for purchase, shipping, or delivered.

USER ENTITY:
The User entity has a UserID primary key to allow for differentiation between
users and a ShippingAddress attribute to allow for easy shipping.

USER - PRODUCT RELATIONSHIP:
User has a 1 to many relationship with product because only 1 shopping cart
can be made per user.

USER - ORDER RELATIONSHIP:
User then has a many to many relationship with Order to allow for multiple
purchases and orders to be fulfilled.

BILLINGINFO ENTITY:
The BillingInfo entity has a BillingNo primary key to track billing and
a Billing Address attribute.

USER - BILLINGINFO RELATIONSHIP:
There is a relationship between User and BillingInfo that tracks a CCNumber
and CCName because this is private information that should not be stored long.
This is a 1 to many relationship because there could be multiple billing
informations saved on one user's account
