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

namespace CoreShop\Behat\Context\Setup;

use CoreShop\Bundle\ResourceBundle\Form\Registry\FormTypeRegistryInterface;
use CoreShop\Component\Rule\Model\ConditionInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Webmozart\Assert\Assert;

trait ConditionFormTrait
{
    protected function assertConditionForm(string $class, string $type): void
    {
        $conditionForm = $this->getConditionForm($type);

        Assert::isInstanceOf($conditionForm, FormInterface::class);
        Assert::true($this->getConditionFormRegistry()->has($type, 'default'));
        Assert::same($class, $this->getConditionFormRegistry()->get($type, 'default'));
    }

    protected function createConditionWithForm(string $type, mixed $data): ConditionInterface
    {
        $form = $this->getFormFactory()->createNamed('', $this->getConditionFormClass());

        $formData = [
            'type' => $type,
            'configuration' => $data,
        ];

        $form->submit($formData);

        Assert::true($form->isValid());

        $condition = $form->getData();

        Assert::isInstanceOf($condition, ConditionInterface::class);

        return $condition;
    }

    protected function getConditionForm(string $type): FormInterface
    {
        if (!$this->getConditionFormRegistry()->has($type, 'default')) {
            throw new \Exception("Form not found for $type");
        }

        return $this->getFormFactory()->createNamed('', $this->getConditionFormRegistry()->get($type, 'default'));
    }

    abstract protected function getConditionFormRegistry(): FormTypeRegistryInterface;

    abstract protected function getFormFactory(): FormFactoryInterface;

    abstract protected function getConditionFormClass(): string;
}
