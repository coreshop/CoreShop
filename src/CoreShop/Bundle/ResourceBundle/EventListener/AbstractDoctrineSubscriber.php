<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\ResourceBundle\EventListener;

use CoreShop\Component\Resource\Metadata\RegistryInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\Mapping\ReflectionService;
use Doctrine\Persistence\Mapping\RuntimeReflectionService;

abstract class AbstractDoctrineSubscriber implements EventSubscriber
{
    protected RegistryInterface $resourceRegistry;
    protected ReflectionService $reflectionService;

    public function __construct(RegistryInterface $resourceRegistry)
    {
        $this->resourceRegistry = $resourceRegistry;
        $this->reflectionService = new RuntimeReflectionService();
    }

    protected function isResource(ClassMetadata $metadata): bool
    {
        if (!$reflClass = $metadata->getReflectionClass()) {
            return false;
        }

        return $reflClass->implementsInterface(ResourceInterface::class);
    }

    protected function getReflectionService()
    {
        return $this->reflectionService;
    }
}
