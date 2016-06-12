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
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */
namespace CoreShop\IndexService\Getter;

use CoreShop\Exception\UnsupportedException;
use CoreShop\Model\Index\Config\Column\Objectbricks;
use CoreShop\Model\Product;

class Brick extends AbstractGetter
{
    /**
     * get value.
     *
     * @param $object
     * @param Objectbricks $config
     *
     * @return mixed
     *
     * @throws UnsupportedException
     */
    public function get(Product $object, Objectbricks $config = null)
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
