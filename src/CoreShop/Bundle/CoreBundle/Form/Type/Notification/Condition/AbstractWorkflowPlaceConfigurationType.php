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

namespace CoreShop\Bundle\CoreBundle\Form\Type\Notification\Condition;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Workflow\WorkflowInterface;

abstract class AbstractWorkflowPlaceConfigurationType extends AbstractType
{
    public function __construct(
        private readonly WorkflowInterface $workflow,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $places = array_keys($this->workflow->getDefinition()->getPlaces());
        $choices = array_combine($places, $places);

        $builder->add($this->getFieldName(), ChoiceType::class, [
            'label' => 'coreshop_select_state',
            'choices' => $choices,
            'placeholder' => null,
        ]);
    }

    abstract protected function getFieldName(): string;
}
