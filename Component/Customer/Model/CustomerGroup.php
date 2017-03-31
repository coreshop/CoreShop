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

namespace CoreShop\Component\Customer\Model;

use CoreShop\Component\Core\Exception\ObjectUnsupportedException;
use CoreShop\Component\Core\Model\AbstractObject;

class CustomerGroup extends AbstractObject implements CustomerGroupInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * {@inheritdoc}
     */
    public function getShops()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * {@inheritdoc}
     */
    public function setShops($shops)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

}
