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

namespace CoreShop\Bundle\IndexBundle\Form\Type;

use CoreShop\Bundle\IndexBundle\Form\Type\Core\AbstractConfigurationCollectionType;
use CoreShop\Component\Index\Model\IndexColumnInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class IndexColumnCollectionType extends AbstractConfigurationCollectionType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        /*
         * The index editor submits the columns as a plain list, and the stored columns are ordered
         * by name, so the positions of both only line up by accident: remove a column, and every
         * column behind it would be mapped onto the stored column of its predecessor, form and
         * leftover configuration included. A column is identified by its name, so the submitted
         * columns are re-keyed to the entry of the stored column with the same name before the
         * collection is resized. New and renamed columns get a key of their own.
         *
         * The priority makes sure this runs before the ResizeFormListener of the CollectionType.
         */
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event): void {
            $data = $event->getData();

            if (!is_array($data)) {
                return;
            }

            $entriesByName = [];

            foreach ($event->getForm() as $key => $child) {
                $column = $child->getData();

                if ($column instanceof IndexColumnInterface && null !== $column->getName()) {
                    $entriesByName[mb_strtolower($column->getName())] = $key;
                }
            }

            $rekeyed = [];
            $new = 0;

            foreach ($data as $column) {
                $name = is_array($column) && is_string($column['name'] ?? null) ? mb_strtolower($column['name']) : null;
                $key = null !== $name ? ($entriesByName[$name] ?? null) : null;

                // unknown name, or a name that was already taken by a column submitted before
                if (null === $key || array_key_exists($key, $rekeyed)) {
                    do {
                        $key = 'new_' . $new++;
                    } while ($event->getForm()->has($key) || array_key_exists($key, $rekeyed));
                }

                $rekeyed[$key] = $column;
            }

            $event->setData($rekeyed);
        }, 16);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('entry_type', IndexColumnType::class);
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_index_column_collection';
    }
}
