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

namespace CoreShop\Bundle\StudioFormBundle\Form\Schema;

use Symfony\Component\Form\FormInterface;

final class FormTypeMapperRegistry
{
    /** @var FormTypeMapperInterface[] */
    private array $mappers = [];

    public function addMapper(FormTypeMapperInterface $mapper): void
    {
        $this->mappers[] = $mapper;
    }

    public function resolve(FormInterface $field): ?UiTypeDescriptor
    {
        foreach ($this->mappers as $mapper) {
            if ($mapper->supports($field)) {
                return $mapper->map($field, $field->getConfig()->getOptions());
            }
        }

        return null;
    }
}
