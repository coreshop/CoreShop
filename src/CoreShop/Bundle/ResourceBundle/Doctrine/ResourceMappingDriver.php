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

declare(strict_types=1);

namespace CoreShop\Bundle\ResourceBundle\Doctrine;

use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use CoreShop\Component\Resource\Metadata\RegistryInterface;

final class ResourceMappingDriver implements MappingDriver
{
    /** @var MappingDriver */
    private $mappingDriver;

    /** @var RegistryInterface */
    private $resourceRegistry;

    public function __construct(MappingDriver $mappingDriver, RegistryInterface $resourceRegistry)
    {
        $this->mappingDriver = $mappingDriver;
        $this->resourceRegistry = $resourceRegistry;
    }

    public function loadMetadataForClass($className, ClassMetadata $metadata): void
    {
        $this->mappingDriver->loadMetadataForClass($className, $metadata);

        $this->convertResourceMappedSuperclass($metadata);
    }

    public function getAllClassNames(): iterable
    {
        return $this->mappingDriver->getAllClassNames();
    }

    public function isTransient($className): bool
    {
        return $this->mappingDriver->isTransient($className);
    }

    private function convertResourceMappedSuperclass(ClassMetadata $metadata): void
    {
        if (!isset($metadata->isMappedSuperclass)) {
            return;
        }

        if (false === $metadata->isMappedSuperclass) {
            return;
        }

        try {
            $resourceMetadata = $this->resourceRegistry->getByClass($metadata->getName());
        } catch (\InvalidArgumentException $exception) {
            return;
        }

        if ($metadata->getName() !== $resourceMetadata->getClass('model')) {
            return;
        }

        $metadata->isMappedSuperclass = false;
    }
}
