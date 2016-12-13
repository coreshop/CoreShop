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
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\IndexService\Getter;

use CoreShop\Exception\UnsupportedException;
use CoreShop\Model\Index\Config\Column\AbstractColumn;
use CoreShop\Model\Index\Config\Column\Objectbricks;
use CoreShop\Model\Product;

/**
 * Class Brick
 * @package CoreShop\IndexService\Getter
 */
class Brick extends AbstractGetter
{
    /**
     * get value.
     *
     * @param $object
     * @param AbstractColumn $config
     *
     * @return mixed
     *
     * @throws UnsupportedException
     */
    public function get(Product $object, AbstractColumn $config)
    {
        $brickField = $config->getGetterConfig()['brickField'];

        $brickContainerGetter = 'get'.ucfirst($brickField);
        $brickContainer = $object->$brickContainerGetter();

        $brickGetter = 'get'.ucfirst($config->getClassName());

        if ($brickContainer) {
            $brick = $brickContainer->$brickGetter();

            if ($brick) {
                $fieldGetter = 'get' . ucfirst($config->getKey());

                return $brick->$fieldGetter();
            }
        }

        return null;
    }
}
