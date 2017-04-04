<?php

namespace CoreShop\Bundle\TaxationBundle\CoreExtension;

use CoreShop\Bundle\ResourceBundle\CoreExtension\Select;
use CoreShop\Component\Taxation\Model\TaxRuleGroupInterface;

class TaxRuleGroup extends Select
{
    /**
     * Static type of this element.
     *
     * @var string
     */
    public $fieldtype = 'coreShopTaxRuleGroup';

    /**
     * Type for the generated phpdoc.
     *
     * @var string
     */
    public $phpdocType = TaxRuleGroupInterface::class;

    /**
     * {@inheritdoc}
     */
    protected function getRepository()
    {
        return \Pimcore::getContainer()->get('coreshop.repository.tax_rule_group');
    }

    /**
     * {@inheritdoc}
     */
    protected function getModel()
    {
        return \Pimcore::getContainer()->getParameter('coreshop.model.tax_rule_group.class');
    }
}
