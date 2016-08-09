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
use CoreShop\Model\Product;

/**
 * Class Localizedfield
 * @package CoreShop\IndexService\Getter
 */
class Localizedfield extends AbstractGetter
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
    public function get(Product $object, AbstractColumn $config = null)
    {
        $language = null;
        
        if (\Zend_Registry::isRegistered("Zend_Locale")) {
            $language = \Zend_Registry::get('Zend_Locale');
        }

        if ($config->getGetterConfig()['locale']) {
            $language = $config->getGetterConfig()['locale'];
        }

        $getter = 'get'.ucfirst($config->getKey());


        return $object->$getter($language);
    }
}
