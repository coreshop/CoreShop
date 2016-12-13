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
use CoreShop\Model\Index\Config\Column\Fieldcollections;
use CoreShop\Model\Product;

/**
 * Class Fieldcollection
 * @package CoreShop\IndexService\Getter
 */
class Fieldcollection extends AbstractGetter
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
        $collectionField = $config->getGetterConfig()['collectionField'];

        $collectionContainerGetter = 'get'.ucfirst($collectionField);
        $collectionContainer = $object->$collectionContainerGetter();
        $validItems = [];
        $fieldValues = [];
        $fieldGetter = 'get' . ucfirst($config->getKey());

        if ($collectionContainer instanceof \Pimcore\Model\Object\Fieldcollection) {
            foreach ($collectionContainer->getItems() as $item) {
                $className = 'Pimcore\Model\Object\Fieldcollection\Data\\' . $config->getClassName();
                if (is_a($item, $className)) {
                    $validItems[] = $item;
                }
            }
        }

        foreach ($validItems as $item) {
            if (method_exists($item, $fieldGetter)) {
                $fieldValues[] = $item->$fieldGetter();
            }
        }

        return count($fieldValues) > 0 ? $fieldValues : null;
    }
}
