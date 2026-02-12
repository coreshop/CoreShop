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

namespace CoreShop\Bundle\StudioFormBundle\Form\Schema\Mapper;

use CoreShop\Bundle\StudioFormBundle\Form\Schema\FormTypeMapperInterface;
use CoreShop\Bundle\StudioFormBundle\Form\Schema\UiTypeDescriptor;
use Symfony\Component\Form\FormInterface;

final class TranslationsTypeMapper implements FormTypeMapperInterface
{
    public function supports(FormInterface $field): bool
    {
        return $field->getConfig()->getType()->getBlockPrefix() === 'coreshop_translations';
    }

    public function map(FormInterface $field, array $options): UiTypeDescriptor
    {
        return new UiTypeDescriptor('coreshop_translations', [
            'childrenFromFirstEntry' => true,
        ]);
    }
}
