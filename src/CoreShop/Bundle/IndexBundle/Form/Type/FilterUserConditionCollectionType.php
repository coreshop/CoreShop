<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\IndexBundle\Form\Type;

use CoreShop\Bundle\IndexBundle\Form\DataMapper\ConditionsFormMapper;
use CoreShop\Bundle\IndexBundle\Form\Type\Core\AbstractConfigurationCollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class FilterUserConditionCollectionType extends AbstractConfigurationCollectionType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('entry_type', FilterUserConditionType::class);
        $resolver->setDefault('nested', false);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!$options['nested']) {
            $builder->setDataMapper(new ConditionsFormMapper($builder->getDataMapper()));
        }
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_filter_user_condition_collection';
    }
}
