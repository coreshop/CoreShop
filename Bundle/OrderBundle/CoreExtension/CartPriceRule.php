<?php

namespace CoreShop\Bundle\OrderBundle\CoreExtension;

use CoreShop\Bundle\ResourceBundle\CoreExtension\Select;

class CartPriceRule extends Select
{
    /**
     * Static type of this element.
     *
     * @var string
     */
    public $fieldtype = 'coreShopCartPriceRule';

    /**
     * Type for the generated phpdoc.
     *
     * @var string
     */
    public $phpdocType = \CoreShop\Component\Order\Model\CartPriceRule::class;

    /**
     * {@inheritdoc}
     */
    protected function getRepository()
    {
        return \Pimcore::getContainer()->get('coreshop.repository.cart_price_rule');
    }

    /**
     * {@inheritdoc}
     */
    protected function getModel()
    {
        return \Pimcore::getContainer()->getParameter('coreshop.model.cart_price_rule.class');
    }
}
