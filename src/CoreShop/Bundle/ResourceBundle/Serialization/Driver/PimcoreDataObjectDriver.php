<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\ResourceBundle\Serialization\Driver;

use Metadata\ClassMetadata;
use Metadata\Driver\DriverInterface;

class PimcoreDataObjectDriver implements DriverInterface
{
    public function __construct(
        protected DriverInterface $decorated,
    ) {
    }

    public function loadMetadataForClass(\ReflectionClass $class): ?ClassMetadata
    {
//        //We don't want Pimcore entities to be serialized directly
        if ($class->getNamespaceName() === \Pimcore\Model\DataObject::class) {
            return new \JMS\Serializer\Metadata\ClassMetadata($name = $class->name);
        }

        if ($class->getName() === \Pimcore\Model\DataObject\Fieldcollection::class) {
            return new \JMS\Serializer\Metadata\ClassMetadata($name = $class->name);
        }

        return $this->decorated->loadMetadataForClass($class);
    }
}
