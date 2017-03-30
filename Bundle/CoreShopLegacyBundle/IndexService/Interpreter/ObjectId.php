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
use CoreShop\Bundle\CoreShopLegacyBundle\Model\Index\Config\Column\AbstractColumn;
use Pimcore\Model\Object\AbstractObject;

/**
 * Class ObjectId
 * @package CoreShop\Bundle\CoreShopLegacyBundle\IndexService\Interpreter
 */
class ObjectId extends AbstractInterpreter
{
    /**
     * @var string
     */
    public static $type = 'objectId';

    /**
     * interpret value.
     *
     * @param mixed $value
     * @param AbstractColumn $config
     *
     * @return mixed
     *
     * @throws UnsupportedException
     */
    public function interpret($value, $config = null)
    {
        if ($value instanceof AbstractObject) {
            return $value->getId();
        }

        return null;
    }
}
