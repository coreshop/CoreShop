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

namespace CoreShop\Test;

use CoreShop\Bundle\ResourceBundle\Form\Registry\FormTypeRegistryInterface;
use CoreShop\Component\Rule\Condition\RuleValidationProcessorInterface;
use CoreShop\Component\Rule\Model\ActionInterface;
use CoreShop\Component\Rule\Model\ConditionInterface;
use CoreShop\Component\Rule\Model\RuleInterface;
use Symfony\Component\Form\FormInterface;

abstract class RuleTest extends Base
{
    /**
     * @return string
     */
    abstract protected function getConditionFormRegistryName();

    /**
     * @return string
     */
    abstract protected function getConditionValidatorName();

    /**
     * @return string
     */
    abstract protected function getConditionFormClass();

    /**
     * @return string
     */
    abstract protected function getActionFormRegistryName();

    /**
     * @return string
     */
    abstract protected function getActionProcessorName();

    /**
     * @return string
     */
    abstract protected function getActionFormClass();

    /**
     * @return RuleInterface
     */
    abstract protected function createRule();

    /**
     * @return RuleValidationProcessorInterface
     */
    protected function getConditionValidator()
    {
        return $this->get($this->getConditionValidatorName());
    }

    /**
     * @return FormTypeRegistryInterface
     */
    protected function getConditionFormRegistry()
    {
        return $this->get($this->getConditionFormRegistryName());
    }

    /**
     * @return FormTypeRegistryInterface
     */
    protected function getActionFormRegistry()
    {
        return $this->get($this->getActionFormRegistryName());
    }

    /**
     * @param $type
     *
     * @return \Symfony\Component\Form\FormInterface
     *
     * @throws \Exception
     */
    protected function getConditionForm($type)
    {
        if (!$this->getConditionFormRegistry()->has($type, 'default')) {
            throw new \Exception("Form not found for $type");
        }

        return $this->getFormFactory()->createNamed('', $this->getConditionFormRegistry()->get($type, 'default'));
    }

    /**
     * @param $type
     *
     * @return \Symfony\Component\Form\FormInterface
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
     * @param $class
     * @param $type
     */
    protected function assertConditionForm($class, $type)
    {
        $conditionForm = $this->getConditionForm($type);

        $this->assertInstanceOf(FormInterface::class, $conditionForm);
        $this->assertTrue($this->getConditionFormRegistry()->has($type, 'default'));
        $this->assertSame($class, $this->getConditionFormRegistry()->get($type, 'default'));
    }

    /**
     * @param $type
     * @param $data
     *
     * @return ConditionInterface
     */
    protected function createConditionWithForm($type, $data)
    {
        $form = $this->getFormFactory()->createNamed('', $this->getConditionFormClass());

        $formData = [
            'type' => $type,
            'configuration' => $data,
        ];

        $form->submit($formData);

        $condition = $form->getData();

        $this->assertInstanceOf(ConditionInterface::class, $condition);

        return $condition;
    }

    /**
     * @param $subject
     * @param RuleInterface $rule
     * @param array $params
     * @param bool $trueOrFalse
     */
    protected function assertPriceRuleCondition($subject, RuleInterface $rule, $params = [], $trueOrFalse = true)
    {
        $params = array_merge($params, $this->getContext());

        $result = $this->getConditionValidator()->isValid($subject, $rule, $params);
        if ($trueOrFalse) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    /**
     * @param $subject
     * @param ConditionInterface $condition
     * @param array $params
     * @param bool $trueOrFalse
     */
    protected function assertRuleCondition($subject, ConditionInterface $condition, $params = [], $trueOrFalse = true)
    {
        $rule = $this->createRule();
        $rule->addCondition($condition);

        $this->assertPriceRuleCondition($subject, $rule, $params, $trueOrFalse);
    }

    /**
     * @param $class
     * @param $type
     */
    protected function assertActionForm($class, $type)
    {
        $conditionForm = $this->getActionForm($type);

        $this->assertInstanceOf(FormInterface::class, $conditionForm);
        $this->assertTrue($this->getActionFormRegistry()->has($type, 'default'));
        $this->assertSame($class, $this->getActionFormRegistry()->get($type, 'default'));
    }

    /**
     * @param $type
     * @param $data
     *
     * @return ActionInterface
     */
    protected function createActionWithForm($type, $data = [])
    {
        $form = $this->getFormFactory()->createNamed('', $this->getActionFormClass());

        $formData = [
            'type' => $type,
            'configuration' => $data,
        ];

        $form->submit($formData);

        $action = $form->getData();

        $this->assertInstanceOf(ActionInterface::class, $action);

        return $action;
    }

    /**
     * @return array
     */
    protected function getContext()
    {
        return [
            'store' => $this->get('coreshop.context.shopper')->getStore(),
            'customer' => $this->get('coreshop.context.shopper')->hasCustomer() ? $this->get('coreshop.context.shopper')->getCustomer() : null,
            'currency' => $this->get('coreshop.context.shopper')->getCurrency(),
            'country' => $this->get('coreshop.context.shopper')->getCountry(),
            'cart' => $this->get('coreshop.context.shopper')->getCart()
        ];
    }
}
