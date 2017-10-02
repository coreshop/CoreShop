# CoreShop Customer Context

CoreShop Customer Security uses [Symfony Firewall](https://symfony.com/doc/current/components/security/firewall.html) to handle authentication.

CoreShop implemented a Context based Wrapper around that to be more flexible. Currently CoreShop implements these Contexts for Customer determination:

 - [Security Token Based](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Customer/Context/RequestBased/TokenBasedRequestResolver.php)


## Create a Custom Resolver

A Store Context needs to implement the interface ```CoreShop\Component\Customer\Context\CustomerContextInterface```. This interface
consists of one function called "getCustomer" which returns a ```CoreShop\Component\Customer\Model\CustomerInterface``` or throws an ```CoreShop\Component\Customer\Context\CustomerNotFoundException```

To register your context, you need to use the tag: ```coreshop.context.customer``` with an optional ```priority``` attribute.

The use case of changing this is quite rare. But if you need to, you can create a Custom Resolver if you wish.