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
use CoreShop\Bundle\StudioFormBundle\Form\Type\AutocompleteType;
use Symfony\Component\Form\FormInterface;

final class AutocompleteTypeMapper implements FormTypeMapperInterface
{
    public function supports(FormInterface $field): bool
    {
        $resolvedType = $field->getConfig()->getType();

        while ($resolvedType !== null) {
            if ($resolvedType->getInnerType() instanceof AutocompleteType) {
                return true;
            }

            $resolvedType = $resolvedType->getParent();
        }

        return false;
    }

    public function map(FormInterface $field, array $options): UiTypeDescriptor
    {
        $view = $field->createView();

        $autocompleteOptions = [];

        if (!empty($view->vars['autocomplete_url'])) {
            $autocompleteOptions['url'] = $view->vars['autocomplete_url'];
        }

        if (!empty($view->vars['autocomplete_class'])) {
            $autocompleteOptions['autocompleteClass'] = $view->vars['autocomplete_class'];
        }

        if (!empty($view->vars['multiple'])) {
            $autocompleteOptions['multiple'] = true;
        }

        if (isset($view->vars['min_chars'])) {
            $autocompleteOptions['minChars'] = (int) $view->vars['min_chars'];
        }

        return new UiTypeDescriptor('autocomplete', $autocompleteOptions);
    }
}
