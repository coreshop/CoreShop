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

namespace CoreShop\Bundle\StudioFormBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Ensures CollectionType always generates a prototype, even when allow_add is false.
 *
 * Symfony's CollectionType only creates the prototype when allow_add is true.
 * For schema-driven forms (grid_collection), the prototype is needed to derive
 * column definitions regardless of whether adding rows is allowed.
 */
final class CollectionPrototypeExtension extends AbstractTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        return [CollectionType::class];
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['allow_add'] || !$options['prototype']) {
            return;
        }

        $prototypeOptions = array_replace($options['entry_options'], [
            'required' => $options['required'],
            'label' => $options['prototype_name'] . 'label__',
        ]);

        $prototype = $builder->create($options['prototype_name'], $options['entry_type'], $prototypeOptions);
        $builder->setAttribute('prototype', $prototype->getForm());
    }
}
