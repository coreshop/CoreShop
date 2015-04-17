# pimcore-coreshop
Onlineshop Plugin for Pimcore

# Demo
Take a look at [coreshop.lineofcode.at](http://coreshop.lineofcode.at)

(Still under Development - Pimcore Login will follow)

You can setup your own example:
[https://github.com/dpfaffenbauer/pimcore-coreshop-demo](https://github.com/dpfaffenbauer/pimcore-coreshop-demo)

___

# Features
* Productmanagement
* Variants of Products
* Countries with Currencies
* Currency conversion
* Country Taxes (not yet implemented)
* Categorymanagement
* Abstract Payment
* Cart Rules for Vouchers/Discounts
* Different Delivery Providers

____

# Plugins
Coreshop was designed to make use of other Pimcore-Plugins. 

Every Payment Provider or Delivery Providier is a Pimcore-Plugin that integrates with Coreshop.

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