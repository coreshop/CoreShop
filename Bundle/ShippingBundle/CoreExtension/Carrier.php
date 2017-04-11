<?php

namespace CoreShop\Bundle\ShippingBundle\CoreExtension;

use CoreShop\Bundle\ResourceBundle\CoreExtension\Select;
use CoreShop\Component\Shipping\Model\CarrierInterface;

class Carrier extends Select
{
    /**
     * Static type of this element.
     *
     * @var string
     */
    public $fieldtype = 'coreShopCarrier';

    /**
     * Type for the generated phpdoc.
     *
     * @var string
     */
    public $phpdocType = CarrierInterface::class;

    /**
     * {@inheritdoc}
     */
    protected function getRepository()
    {
        return \Pimcore::getContainer()->get('coreshop.repository.carrier');
    }

    /**
     * {@inheritdoc}
     */
    protected function getModel()
    {
        return \Pimcore::getContainer()->getParameter('coreshop.model.carrier.class');
    }
}
