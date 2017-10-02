# CoreShop Payum Ominpay Bridge

[Here](https://github.com/thephpleague/omnipay#payment-gateways) is a list of all available Omnipay Payment Providers.

> As of now, Omnipay is not compatible with Symfony 3. They are already working on it, but will need some time to be finished.
> But: You can use the dev-master Version Omnipay to add the Bridge

To add the Omnipay Bridge, do following:

```bash
$ composer require payum/omnipay-v3-bridge:dev-master
```

This will install the Bridge for you.

Now you still need to create your Gateway Configuration as described [here](./01_Payment_Provider.md).

## Example of Omnipay Gateway Configuration

As example we add omnipay-worldpay:

> Worldpay is currently in PR and I don't know when it is getting merged

**1**: add FormType for Worldpay Configuration:

```php
<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\NotBlank;

final class WorldpayType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('serviceKey', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'groups' => 'coreshop',
                    ]),
                ],
            ])
            ->add('clientKey', PasswordType::class, [
                'constraints' => [
                    new NotBlank([
                        'groups' => 'coreshop',
                    ]),
                ],
            ])
            ->add('merchantId', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'groups' => 'coreshop',
                    ]),
                ],
            ])
            ->add('factory', TextType::class, [
                'data' => 'omnipay',
                'empty_data' => 'omnipay'
            ])
            ->add('type', TextType::class, [
                'data' => 'WorldPay\\Json',
                'empty_data' => 'WorldPay\\Json'
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $data = $event->getData();

                $data['payum.http_client'] = '@coreshop.payum.http_client';
            })
        ;
    }
}

```

Register into the container:

```yaml
services:
  app.form.type.gateway_configuration.worldpay:
    class: AppBundle\Form\Type\WorldpayType
    tags:
      - { name: coreshop.gateway_configuration_type, type: omnipay_worldpay }
      - { name: form.type }
```

> Its important that it starts with ```omnipay_``` here

**2**: Add ExtJs Form:

```javascript
pimcore.registerNS('coreshop.provider.gateways.omnipay_worldpay');
coreshop.provider.gateways.omnipay_worldpay = Class.create(coreshop.provider.gateways.abstract, {

    getLayout: function (config) {
        return [
            {
                xtype: 'textfield',
                fieldLabel: t('coreshop_worldpay_service_key'),
                name: 'gatewayConfig.config.serviceKey',
                length: 255,
                value: config.serviceKey ? config.serviceKey : ""
            },
            {
                xtype: 'textfield',
                fieldLabel: t('coreshop_worldpay_client_key'),
                name: 'gatewayConfig.config.clientKey',
                length: 255,
                value: config.clientKey
            },
            {
                xtype: 'textfield',
                fieldLabel: t('coreshop_worldpay_merchant_id'),
                name: 'gatewayConfig.config.merchantId',
                length: 255,
                value: config.merchantId
            }
        ];
    }

});
```

Register JS File for CoreShop to be loaded:

```yaml
core_shop_payment:
    pimcore_admin:
      js:
        worldpay: /bundles/app/pimcore/static/js/payment/provider/worldpay.js
```

Thats it, now you can create a new Payment Provider in the Backend and use Omnipay WorldPay as Payment Provider.