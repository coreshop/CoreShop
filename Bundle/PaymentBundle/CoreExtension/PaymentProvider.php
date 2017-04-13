<?php

namespace CoreShop\Bundle\PaymentBundle\CoreExtension;

use CoreShop\Bundle\ResourceBundle\CoreExtension\Select;

class PaymentProvider extends Select
{
    /**
     * Static type of this element.
     *
     * @var string
     */
    public $fieldtype = 'coreShopPaymentProvider';

    /**
     * Type for the generated phpdoc.
     *
     * @var string
     */
    public $phpdocType = \CoreShop\Component\Payment\Model\PaymentProvider::class;

    /**
     * {@inheritdoc}
     */
    protected function getRepository()
    {
        return \Pimcore::getContainer()->get('coreshop.repository.payment_provider');
    }

    /**
     * {@inheritdoc}
     */
    protected function getModel()
    {
        return \Pimcore::getContainer()->getParameter('coreshop.model.payment_provider.class');
    }
}
