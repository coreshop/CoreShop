<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\IndexBundle\Service;

use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Index\Service\IndexableClassesProviderInterface;
use Pimcore\Model\DataObject\ClassDefinition;

final class IndexableClassesProvider implements IndexableClassesProviderInterface
{
    public function getIndexableClassNames(): array
    {
        $listing = new ClassDefinition\Listing();
        $result = [];

        foreach ($listing->load() as $class) {
            if (!$class instanceof ClassDefinition) {
                continue;
            }

            $name = $class->getName();

            if (null === $name || '' === $name) {
                continue;
            }

            $pimcoreClass = 'Pimcore\\Model\\DataObject\\' . ucfirst($name);

            if (!class_exists($pimcoreClass)) {
                continue;
            }

            if (in_array(IndexableInterface::class, class_implements($pimcoreClass) ?: [], true)) {
                $result[] = $name;
            }
        }

        return $result;
    }
}
