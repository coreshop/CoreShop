<?php

namespace CoreShop\Bundle\IndexBundle\CoreExtension;

use CoreShop\Bundle\ResourceBundle\CoreExtension\Select;

class Filter extends Select
{
    /**
     * Static type of this element.
     *
     * @var string
     */
    public $fieldtype = 'coreShopFilter';

    /**
     * Type for the generated phpdoc.
     *
     * @var string
     */
    public $phpdocType = \CoreShop\Component\Index\Model\Filter::class;

    /**
     * {@inheritdoc}
     */
    protected function getRepository()
    {
        return \Pimcore::getContainer()->get('coreshop.repository.filter');
    }

    /**
     * {@inheritdoc}
     */
    protected function getModel()
    {
        return \Pimcore::getContainer()->getParameter('coreshop.model.filter.class');
    }
}
