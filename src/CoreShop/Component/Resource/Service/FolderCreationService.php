<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Resource\Service;

use CoreShop\Component\Pimcore\DataObject\ObjectServiceInterface;
use CoreShop\Component\Resource\Metadata\RegistryInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use Pimcore\Model\DataObject\Folder;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FolderCreationService implements FolderCreationServiceInterface
{
    protected RegistryInterface $metadataRegistry;
    protected ObjectServiceInterface $objectService;

    public function __construct(
        RegistryInterface $metadataRegistry,
        ObjectServiceInterface $objectService
    )
    {
        $this->metadataRegistry = $metadataRegistry;
        $this->objectService = $objectService;
    }

    public function createFolderForResource(ResourceInterface $resource, array $options): Folder
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefault('prefix', null);
        $optionsResolver->setDefault('suffix', null);
        $optionsResolver->setDefault('path', null);

        $resourceConfig = $this->metadataRegistry->getByClass(get_class($resource))->getParameter('path');

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
        }
        elseif (!isset($options['path']) || null === $options['path']) {
            throw new \InvalidArgumentException('Resource has multiple paths configured, please specify which one to use');
        }
        else {
            $path = $resourceConfig[$options['path']];
        }

        return $this->objectService->createFolderByPath(
            sprintf('%s%s%s', $options['prefix'], $path, $options['suffix'])
        );
    }
}
