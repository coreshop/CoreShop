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

namespace CoreShop\Bundle\IndexBundle\Form\Type\Worker;

use CoreShop\Bundle\IndexBundle\Worker\MysqlWorker\TableIndex;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class MysqlWorkerTableIndexType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    TableIndex::TABLE_INDEX_TYPE_INDEX,
                    TableIndex::TABLE_INDEX_TYPE_UNIQUE,
                ],
                'required' => false,
            ])
            ->add('columns', CollectionType::class, [
                'entry_type' => TextType::class,
                'allow_delete' => true,
                'allow_add' => true,
                'required' => false,
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_index_worker_mysql';
    }
}
