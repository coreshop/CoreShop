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

namespace CoreShop\Bundle\StoreBundle\CoreExtension;

use CoreShop\Bundle\StoreBundle\Doctrine\ORM\StoreRepository;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\ClassDefinition\DynamicOptionsProvider\SelectOptionsProviderInterface;

class StoreOptionProvider implements SelectOptionsProviderInterface
{
    public function __construct(
        private StoreRepository $repository,
    ) {
    }

    public function getOptions(array $context, Data $fieldDefinition): array
    {
        $options = [];
        $stores = $this->repository->getAll();
        foreach ($stores as $store) {
            $options[] = [
                'key' => $store->getName(),
                'value' => $store->getId(),
            ];
        }

        return $options;
    }

    public function getDefaultValue(array $context, Data $fieldDefinition): ?string
    {
        return null;
    }

    public function hasStaticOptions(array $context, Data $fieldDefinition): bool
    {
        return true;
    }
}
