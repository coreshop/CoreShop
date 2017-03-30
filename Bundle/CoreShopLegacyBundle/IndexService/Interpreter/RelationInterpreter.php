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

namespace CoreShop\Bundle\CoreShopLegacyBundle\IndexService\Interpreter;

use CoreShop\Bundle\CoreShopLegacyBundle\Exception\UnsupportedException;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\Index\Config\Column;

/**
 * Class RelationInterpreter
 * @package CoreShop\Bundle\CoreShopLegacyBundle\IndexService\Interpreter
 */
abstract class RelationInterpreter extends AbstractInterpreter
{
    /**
     * interpret value.
     *
     * @param mixed $value
     * @param Column $config
     *
     * @return mixed
     *
     * @throws UnsupportedException
     */
    public function interpret($value, $config = null)
    {
        throw new UnsupportedException('Not implemented in abstract');
    }
}
