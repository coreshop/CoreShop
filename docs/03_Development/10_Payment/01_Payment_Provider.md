# CoreShop Payment Providers

A Payment Provider represents a way that your customer pays during the checkout process.
It holds a reference to a specific gateway with custom configuration.
A gateway is configured for each payment method separately using the payment method form.

## Payment Gateway configuration

### Payment Gateways that already have a CoreShop bridge
First you need to create the configuration form type for your gateway.
Have a look at the configuration form types of *Paypal* and *Sofort*.

Then you should register its configuration form type with `coreshop.gateway_configuration_type` tag.
After that it will be available in the admin panel in the gateway choice dropdown.

> If you are not sure how your configuration form type should look like,
> head to [Payum documentation](https://github.com/Payum/Payum).

### Other Payment Gateways

> Learn more about integrating payment gateways in the [Payum docs](https://github.com/Payum/Payum).

> You’ll probably need also this kind of configuration in your `app/config/config.yml` for the gateway’s factory:
> ```yaml
> payum:
>     gateways:
>        yourgateway:
>            factory: yourgateway
>```

As an example, we add *Sofort* as a payment gateway factory.
To add a new gateway configuration you need to add 2 files:

 - A new FormType for configuration values
 - A new Javascript File for ExtJs Form

**1**: Form Type for Configuration Values:

```php

namespace AppBundle\CoreShop\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\NotBlank;

final class SofortGatewayConfigurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('config_key', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'groups' => 'coreshop',
                    ]),
                ],
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $data = $event->getData();

                $data['payum.http_client'] = '@coreshop.payum.http_client';
            })
        ;
    }
}

```

Now we register the FormType into the container

```yaml
services:
  app.coreshop.form.type.gateway_configuration.sofort:
    class: AppBundle\Form\Type\SofortGatewayConfigurationType
    tags:
      - { name: coreshop.gateway_configuration_type, type: sofort }
      - { name: form.type }
```

**2**: Create the corresponding ExtJs Form:

> You need to use the ```type``` attribute as identifier here

```js
pimcore.registerNS('coreshop.provider.gateways.sofort');
coreshop.provider.gateways.sofort = Class.create(coreshop.provider.gateways.abstract, {

    getLayout: function (config) {
        return [
            {
                xtype: 'textfield',
                fieldLabel: t('config_key'),
                name: 'gatewayConfig.config.config_key',
                length: 255,
                value: config.config_key ? config.config_key : ""
            }
        ];
    }

});

```

Next we need to register our new Gateway JS file to be loaded:

```yaml
core_shop_payment:
    pimcore_admin:
        js:
            sofort_gateway: '/bundles/app/pimcore/js/sofort.js'
```

Thats it, now after reloading Pimcore, you'll see a new Factory.