# pimcore-coreshop

[![Join the chat at https://gitter.im/dpfaffenbauer/pimcore-coreshop](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/dpfaffenbauer/pimcore-coreshop?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
Onlineshop Plugin for Pimcore

# Demo
Take a look at [coreshop.lineofcode.at](http://coreshop.lineofcode.at) (Currently out of date)

(Still under Development - Pimcore Login will follow)

You can setup your own example:

* Download Plugin and place it in your plugins directory
* Open Extension Manager in Pimcore and enable/install Plugin
* After Installation within Pimcore Extension Manager, you have to reload Pimcore
* Now the CoreShop Icon will appear in the Menu
* You now have to let CoreShop install itself
* finised
* Go To http://yourdomain/en/shop

___

# Features
* Productmanagement
* Variants of Products
* Countries with Currencies
* Currency conversion
* Country Taxes (not yet implemented)
* Category Management
* Abstract Payment
* Price Rules for Vouchers/Discounts
* Different Delivery Providers
* Shipping Management
* Different Payment Providers
* Multiple Themes supported

____

# Themes
Themes are installed within plugins/CoreShop/views/template/[Name]

A Theme is basically a Zend Module with the Namespace CoreShopTheme. All views and controllers specific for this theme are placed inside the theme-folder.

____

# Plugins
Coreshop was designed to make use of other Pimcore-Plugins. 

Every Payment Provider or Delivery Provider is a Pimcore-Plugin that integrates with CoreShop.

## Hooks
CoreShop uses Hooks to call Pimcore-Plugins from template files.

**Hooks are currently not consistently implemented.**

For example on the product-detail template file (in Demo Shop):

```
<?=\CoreShop\Plugin::hook("product-detail-bottom", array("product" => $this->product))?>
```

# Payment
A Payment Provider is implemented using a Pimcore-Plugin.

To implement a new Payment Plugin you need to extend the class CoreShop\Plugin\Payment.

For example take a look at the pimcore-payunity Plugin: [https://github.com/dpfaffenbauer/pimcore-payunity](https://github.com/dpfaffenbauer/pimcore-payunity)

# Delivery Provider
A Delivery Provider is implemented using a Pimcore-Plugin.

To implement a new Delivery Prodiver you need to extend the class CoreShop\Plugin\Delivery.

For example take a look the the coreshop-demo Plugin "CoreShopDeliveryPost";

# GeoIP
CoreShop can use GeoIP to locate visitors countries using IP-Addresses.

To enable GeoIP download [GeoIP.dat](http://geolite.maxmind.com/download/geoip/database/GeoLiteCountry/GeoIP.dat.gz) into the directory 

```
/website/var/config/GeoIP/GeoIP.dat
```