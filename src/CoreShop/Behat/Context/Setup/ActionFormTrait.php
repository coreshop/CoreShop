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
use CoreShop\Component\Rule\Model\ActionInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Webmozart\Assert\Assert;

trait ActionFormTrait
{
    protected function assertActionForm(string $class, string $type): void
    {
        $conditionForm = $this->getActionForm($type);

        Assert::isInstanceOf($conditionForm, FormInterface::class);
        Assert::true($this->getActionFormRegistry()->has($type, 'default'));
        Assert::same($class, $this->getActionFormRegistry()->get($type, 'default'));
    }

    protected function createActionWithForm($type, $data = [])
    {
        $form = $this->getFormFactory()->createNamed('', $this->getActionFormClass());

        $formData = [
            'type' => $type,
            'configuration' => $data,
        ];

        $form->submit($formData);

        Assert::true($form->isValid());

        $action = $form->getData();

        Assert::isInstanceOf($action, ActionInterface::class);

        return $action;
    }

    protected function getActionForm(string $type): FormInterface
    {
        if (!$this->getActionFormRegistry()->has($type, 'default')) {
            throw new \Exception("Form not found for $type");
        }

        return $this->getFormFactory()->createNamed('', $this->getActionFormRegistry()->get($type, 'default'));
    }

    abstract protected function getActionFormRegistry(): FormTypeRegistryInterface;

    abstract protected function getFormFactory(): FormFactoryInterface;

    abstract protected function getActionFormClass(): string;
}
