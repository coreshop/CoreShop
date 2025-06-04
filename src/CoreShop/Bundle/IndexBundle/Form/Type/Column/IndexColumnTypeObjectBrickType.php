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

namespace CoreShop\Bundle\IndexBundle\Form\Type\Column;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

final class IndexColumnTypeObjectBrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('key', TextType::class, [
                'constraints' => [
                    new NotBlank(['groups' => ['coreshop']]),
                ],
            ])
            ->add('className', TextType::class, [
                'constraints' => [
                    new NotBlank(['groups' => ['coreshop']]),
                ],
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_index_column_type_object_brick';
    }
}
