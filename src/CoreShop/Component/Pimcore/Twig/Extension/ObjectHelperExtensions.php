<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Pimcore\Twig\Extension;

use Pimcore\Model\DataObject\Concrete;

final class ObjectHelperExtensions extends \Twig_Extension
{
    public function getTests()
    {
        return [
            new \Twig_Test('object', function ($object) {
                return is_object($object) && $object instanceof Concrete;
            }),
            new \Twig_Test('object_class', function ($object, $className) {
                $className = ucfirst($className);
                $className = 'Pimcore\\Model\\DataObject\\'.$className;

                return class_exists($className) && $object instanceof $className;
            }),
        ];
    }
}
