<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

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
