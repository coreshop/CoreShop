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

namespace CoreShop\Bundle\IndexBundle\Form\Type\Worker;

use CoreShop\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

final class OpenSearchWorkerType extends AbstractType
{
    public function __construct(
        private ServiceRegistryInterface $clientRegistry,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $clients = \array_keys($this->clientRegistry->all());

        $builder
            ->add('client', ChoiceType::class, [
                'choices' => \array_combine($clients, $clients),
                'choice_translation_domain' => false,
            ])
            ->add('numberOfShards', IntegerType::class, [
                'required' => false,
                'empty_data' => 1,
            ])
            ->add('numberOfReplicas', IntegerType::class, [
                'required' => false,
                'empty_data' => 1,
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_index_worker_opensearch';
    }
}
