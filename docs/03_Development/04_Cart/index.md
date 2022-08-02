# CoreShop Cart

This guide should lead you through how CoreShop handles the Cart.

 - [Create, Read, Update, Delete](./01_CRUD.md)
 - [Cart Manager](./02_Cart_Manager.md)
 - [Cart Modifier](./03_Cart_Modifier.md)
 - [Cart Processor](./04_Cart_Processor.md)
 - [Commands](./05_Commands.md)
 - [Cart Context](./06_Context.md)

## Introduction

The CoreShop Cart is stateless. Which means that every change on the cart triggers the [Cart Processor](./04_Cart_Processor.md) which
then calculates all necessary prices.