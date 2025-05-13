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

namespace CoreShop\Component\Resource\Service;

use CoreShop\Component\Pimcore\DataObject\ObjectServiceInterface;
use CoreShop\Component\Resource\Metadata\RegistryInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use Pimcore\Model\DataObject\Folder;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FolderCreationService implements FolderCreationServiceInterface
{
    public function __construct(
        protected RegistryInterface $metadataRegistry,
        protected ObjectServiceInterface $objectService,
    ) {
    }

    public function createFolderForResource(ResourceInterface $resource, array $options): Folder
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefault('prefix', null);
        $optionsResolver->setDefault('suffix', null);
        $optionsResolver->setDefault('path', null);

        $resourceConfig = $this->metadataRegistry->getByClass($resource::class)->getParameter('path');

        $options = $optionsResolver->resolve($options);

        if ($options['prefix']) {
            $options['prefix'] .= '/';
        }

        if ($options['suffix']) {
            $options['suffix'] = '/' . $options['suffix'];
        }

        if (!$resourceConfig) {
            throw new \InvalidArgumentException('Resource has no valid paths configured');
        }

        if (!is_array($resourceConfig)) {
            $resourceConfig = [$resourceConfig];
        }

        if (count($resourceConfig) === 0) {
            throw new \InvalidArgumentException('Resource has no valid paths configured');
        }

        if (count($resourceConfig) === 1) {
            $path = reset($resourceConfig);
        } elseif (!isset($options['path']) || null === $options['path']) {
            throw new \InvalidArgumentException('Resource has multiple paths configured, please specify which one to use');
        } else {
            $path = $resourceConfig[$options['path']];
        }

        return $this->objectService->createFolderByPath(
            sprintf('%s%s%s', $options['prefix'], $path, $options['suffix']),
        );
    }
}
