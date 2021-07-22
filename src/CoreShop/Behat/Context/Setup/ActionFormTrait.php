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

namespace CoreShop\Behat\Context\Setup;

use CoreShop\Bundle\ResourceBundle\Form\Registry\FormTypeRegistryInterface;
use CoreShop\Component\Rule\Model\ActionInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Webmozart\Assert\Assert;

trait ActionFormTrait
{
    /**
     * @param string $class
     * @param string $type
     *
     * @throws \Exception
     */
    protected function assertActionForm($class, $type)
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

    /**
     * @param string $type
     *
     * @return FormInterface
     *
     * @throws \Exception
     */
    protected function getActionForm($type)
    {
        if (!$this->getActionFormRegistry()->has($type, 'default')) {
            throw new \Exception("Form not found for $type");
        }

        return $this->getFormFactory()->createNamed('', $this->getActionFormRegistry()->get($type, 'default'));
    }

    /**
     * @return FormTypeRegistryInterface
     */
    abstract protected function getActionFormRegistry();

    /**
     * @return FormFactoryInterface
     */
    abstract protected function getFormFactory();

    /**
     * @return string
     */
    abstract protected function getActionFormClass();
}
