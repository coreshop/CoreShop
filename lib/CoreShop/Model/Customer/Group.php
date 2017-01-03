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

namespace CoreShop\Model\Customer;

use CoreShop\Exception\ObjectUnsupportedException;
use CoreShop\Model\Base;
use Pimcore\Model\Object;

/**
 * Class Customer
 * @package CoreShop\Model\Customer
 *
 * @method static Object\Listing\Concrete getByName ($value, $limit = 0)
 */
class Group extends Base
{
    /**
     * Pimcore Object Class.
     *
     * @var string
     */
    public static $pimcoreClass = 'Pimcore\\Model\\Object\\CoreShopCustomerGroup';

    /**
     * @return string
     *
     * @throws ObjectUnsupportedException
     */
    public function getName()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param string $name
     *
     * @throws ObjectUnsupportedException
     */
    public function setName($name)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return int[]
     *
     * @throws ObjectUnsupportedException
     */
    public function getShops()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param int[] $shops
     *
     * @throws ObjectUnsupportedException
     */
    public function setShops($shops)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }
}
