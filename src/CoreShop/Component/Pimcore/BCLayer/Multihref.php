<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Pimcore\BCLayer;

if (class_exists(\Pimcore\Model\DataObject\ClassDefinition\Data\Multihref::class)) {
    class Multihref extends \Pimcore\Model\DataObject\ClassDefinition\Data\Multihref
    {
    }
} elseif (class_exists(\Pimcore\Model\DataObject\ClassDefinition\Data\ManyToManyRelation::class)) {
    class Multihref extends \Pimcore\Model\DataObject\ClassDefinition\Data\ManyToManyRelation
    {
    }
} else {
    abstract class Multihref extends \Pimcore\Model\DataObject\ClassDefinition\Data
    {
    }

    throw new \RuntimeException(sprintf('This Exception should never be called, if it does get called, the class %s or %s is missing.', '\Pimcore\Model\DataObject\ClassDefinition\Data\Multihref', 'Pimcore\Model\DataObject\ClassDefinition\Data\ManyToManyRelation'));
}
