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
