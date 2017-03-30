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

namespace CoreShop\Bundle\CoreShopLegacyBundle\IndexService\Getter;

use CoreShop\Bundle\CoreShopLegacyBundle\Exception\UnsupportedException;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\Index\Config\Column;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\Product;

/**
 * Class Brick
 * @package CoreShop\Bundle\CoreShopLegacyBundle\IndexService\Getter
 */
class Brick extends AbstractGetter
{
    /**
     * @var string
     */
    public static $type = 'brick';

    /**
     * get value.
     *
     * @param $object
     * @param Column $config
     *
     * @return mixed
     *
     * @throws UnsupportedException
     */
    public function get(Product $object, Column $config)
    {
        if ($config instanceof Column\Objectbricks)
        {
            $brickField = $config->getGetterConfig()['brickField'];

            $brickContainerGetter = 'get'.ucfirst($brickField);
            $brickContainer = $object->$brickContainerGetter();
            $brickGetter = 'get' . ucfirst($config->getClassName());

            if ($brickContainer) {
                $brick = $brickContainer->$brickGetter();

                if ($brick) {
                    $fieldGetter = 'get' . ucfirst($config->getKey());

                    return $brick->$fieldGetter();
                }
            }
        }

        return null;
    }
}
