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

namespace CoreShop\Model;

use Pimcore\Model\Object;
use Pimcore\Model\Asset\Image;
use CoreShop\Exception\ObjectUnsupportedException;

/**
 * Class Manufacturer
 * @package CoreShop\Model
 *
 * @method static Object\Listing\Concrete getByName ($value, $limit = 0)
 */
class Manufacturer extends Base
{
    /**
     * Pimcore Object Class.
     *
     * @var string
     */
    public static $pimcoreClass = 'Pimcore\\Model\\Object\\CoreShopManufacturer';

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
     * @return Image|null
     *
     * @throws ObjectUnsupportedException
     */
    public function getImage()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param Image $image
     *
     * @throws ObjectUnsupportedException
     */
    public function setImage($image)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }
}
