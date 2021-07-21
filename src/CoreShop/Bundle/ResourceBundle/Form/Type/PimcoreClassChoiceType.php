<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\ResourceBundle\Form\Type;

use Pimcore\Model\DataObject\ClassDefinition;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PimcoreClassChoiceType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $classes = new ClassDefinition\Listing();
        $classes = $classes->load();

        $choices = [];

        foreach ($classes as $class) {
            $choices[$class->getName()] = $class->getName();
        }

        $resolver->setDefaults([
            'choices' => $choices,
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_resource_pimcore_class_choice';
    }
}
