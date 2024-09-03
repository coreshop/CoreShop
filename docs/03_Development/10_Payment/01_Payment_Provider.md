# Payment Providers

A Payment Provider in CoreShop represents a method your customer uses to pay during the checkout process. It links to a
specific gateway with custom configurations. Each payment method is configured separately using the payment method form
in the admin panel.

## Payment Gateway Configuration

### Payment Gateways with CoreShop Bridges

To configure a payment gateway that already has a CoreShop bridge:

1. **Create a Configuration Form Type**: Look at the existing configuration form types for gateways like Paypal and
   Sofort for reference.

2. **Register the Configuration Form Type**: Use the `coreshop.gateway_configuration_type` tag to make the gateway
   available in the admin panel's dropdown.

   > For guidance on configuration form types, refer to the [Payum documentation](https://github.com/Payum/Payum).

### Integrating Other Payment Gateways

To learn more about integrating other payment gateways, consult the [Payum docs](https://github.com/Payum/Payum).

You may also need to add configuration in `app/config/config.yml` for the gateway’s factory:

```yaml
payum:
  gateways:
    yourgateway:
      factory: yourgateway
```

Example: Adding Sofort as a Payment Gateway Factory

To add Sofort as a new gateway configuration, create the following files:

1. **Form Type for Configuration Values**:

 ```php
 namespace App\CoreShop\Form\Type;

 use Symfony\Component\Form\AbstractType;
 // ... other use statements ...

 final class SofortGatewayConfigurationType extends AbstractType
 {
     public function buildForm(FormBuilderInterface $builder, array $options)
     {
         // Form build logic...
     }
 }
 ```

Then, register the FormType in the service container:

```yaml
services:
   App\CoreShop\Form\Type\SofortGatewayConfigurationType:
      tags:
         - { name: coreshop.gateway_configuration_type, type: sofort }
         - { name: form.type }
```

2. **ExtJs Form for Sofort**:

   Create a JavaScript file for the ExtJs Form:

 ```js
// public/coreshop/js/sofort.js
pimcore.registerNS('coreshop.provider.gateways.sofort');
coreshop.provider.gateways.sofort = Class.create(coreshop.provider.gateways.abstract, {
  getLayout: function (config) {
      // Form layout logic...
  }
});
 ```

Register the new JavaScript file:

```yaml
core_shop_payment:
  pimcore_admin:
    js:
      sofort_gateway: '/coreshop/js/sofort.js'
```

After reloading Pimcore, you should see the new Factory available.

