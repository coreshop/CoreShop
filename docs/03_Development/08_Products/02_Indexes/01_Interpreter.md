# Interpreter

To prepare your index and transform data, you use one of the existing Interpreter or create one yourself.

CoreShop currently has following Interpreters:

 - **Object**: converts an object or and object array to relations. It saves the values to the relations inex
 - **ObjectId**: converts an object to its ID
 - **ObjectIdSum**: calculates the sum of all IDs. (Could be used for similar products)
 - **ObjectProperty**: calls a getter method of the value
 - **Soundex**: calls PHP soundex function (Could be used for similar products)

[You can also add your own Interpreter](./02_Create_Interpreter.md)