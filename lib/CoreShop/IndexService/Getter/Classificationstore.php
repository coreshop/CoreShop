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

namespace CoreShop\IndexService\Getter;

use CoreShop\Exception\UnsupportedException;
use CoreShop\Model\Index\Config\Column;
use CoreShop\Model\Product;

/**
 * Class Classificationstore
 * @package CoreShop\IndexService\Getter
 */
class Classificationstore extends AbstractGetter
{
    /**
     * @var string
     */
    public static $type = 'classificationstore';

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
        if ($config instanceof Column\Classificationstore) {
            $classificationStore = $config->getGetterConfig()['classificationStoreField'];
            $classificationStoreGetter = 'get' . ucfirst($classificationStore);

            if (method_exists($object, $classificationStoreGetter)) {
                $classificationStore = $object->$classificationStoreGetter();

                if ($classificationStore instanceof \Pimcore\Model\Object\Classificationstore) {
                    return $classificationStore->getLocalizedKeyValue($config->getGroupConfigId(), $config->getKeyConfigId());
                }
            }
        }

        return null;
    }
}
