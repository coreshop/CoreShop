<?php
/**
 * CoreShop
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
use CoreShop\Model\Index\Config\Column\AbstractColumn;
use CoreShop\Model\Product;

class Localizedfield extends AbstractGetter
{

    /**
     * @param $object
     * @param AbstractColumn $config
     * @return mixed
     * @throws UnsupportedException
     */
    public function get(Product $object, AbstractColumn $config = null)
    {
        $language = \Zend_Registry::get("Zend_Locale");

        if ($config->getGetterConfig()['locale']) {
            $language = $config->getGetterConfig()['locale'];
        }

        $getter = "get" . ucfirst($config->getKey());

        return $object->$getter($language);
    }
}
