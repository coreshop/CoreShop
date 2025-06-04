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

namespace CoreShop\Bundle\ResourceBundle\EventListener;

use CoreShop\Component\Resource\Metadata\RegistryInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\Mapping\ReflectionService;
use Doctrine\Persistence\Mapping\RuntimeReflectionService;

abstract class AbstractDoctrineSubscriber implements EventSubscriber
{
    protected ?ReflectionService $reflectionService = null;

    public function __construct(
        protected RegistryInterface $resourceRegistry,
    ) {
    }

    protected function isResource(ClassMetadata $metadata): bool
    {
        return $metadata->getReflectionClass()->implementsInterface(ResourceInterface::class);
    }

    protected function getReflectionService(): ReflectionService
    {
        if ($this->reflectionService === null) {
            $this->reflectionService = new RuntimeReflectionService();
        }

        return $this->reflectionService;
    }
}
