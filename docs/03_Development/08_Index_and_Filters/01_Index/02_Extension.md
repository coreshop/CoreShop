# CoreShop Index Extension

In order to make the index more flexible, it is possible for you to write extensions. Extensions allow you to do following things:

    - Add more "default" columns and corresponding data
    - Pre Filter an mysql index

To create a new extension, you need to implement either the interface
[```CoreShop\Component\Index\Extension\IndexColumnsExtensionInterface```](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Index/Extension/IndexColumnsExtensionInterface.php)
for column extensions or the interface
[```CoreShop\Bundle\IndexBundle\Extension\MysqlIndexQueryExtensionInterface```](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/IndexBundle/Extension/MysqlIndexQueryExtensionInterface.php)
for mysql query extensions.

You then need to register your service using the tag ```coreshop.index.extension```