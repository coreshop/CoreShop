<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\ResourceBundle\CoreExtension;

use CoreShop\Component\Resource\Model\ResourceInterface;
use DeepCopy\Reflection\ReflectionHelper;

trait CloneDoctrineEntityTrait
{
    public function cloneEntity(ResourceInterface $resource)
    {
        $cloned = clone $resource;

        $reflectionProperty = ReflectionHelper::getProperty($cloned, 'id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($cloned, null);

        return $cloned;
    }
}
