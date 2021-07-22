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

namespace CoreShop\Bundle\RuleBundle\Form\Type;

use CoreShop\Bundle\RuleBundle\Form\DataMapper\ActionsFormMapper;
use CoreShop\Bundle\RuleBundle\Form\Type\Core\AbstractConfigurationCollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RuleActionCollectionType extends AbstractConfigurationCollectionType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setDataMapper(new ActionsFormMapper($builder->getDataMapper()));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_rule_action_collection';
    }
}
