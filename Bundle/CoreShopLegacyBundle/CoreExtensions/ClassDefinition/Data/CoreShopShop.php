<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreShopLegacyBundle\CoreExtensions\ClassDefinition\Data;

use CoreShop\Bundle\CoreShopLegacyBundle\Model\Object\ClassDefinition\Data\Select as CoreShopSelect;

/**
 * Class CoreShopShop
 * @package CoreShop\Bundle\CoreShopLegacyBundle\CoreExtensions\ClassDefinition\Data
 */
class CoreShopShop extends CoreShopSelect
{
    /**
     * Static type of this element.
     *
     * @var string
     */
    public $fieldtype = 'coreShopShop';

    /**
     * Type for the generated phpdoc.
     *
     * @var string
     */
    public $phpdocType = '\\CoreShop\Bundle\CoreShopLegacyBundle\\Model\\Shop';
}
