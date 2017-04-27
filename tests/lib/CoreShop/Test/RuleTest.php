<?php

namespace CoreShop\Test;

use CoreShop\Bundle\ResourceBundle\Form\Registry\FormTypeRegistryInterface;
use CoreShop\Component\Rule\Condition\RuleValidationProcessorInterface;
use CoreShop\Component\Rule\Model\ActionInterface;
use CoreShop\Component\Rule\Model\ConditionInterface;
use CoreShop\Component\Rule\Model\RuleInterface;
use Symfony\Component\Form\FormFactoryInterface;
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
    protected function getConditionValidator() {
        return $this->get($this->getConditionValidatorName());
    }

    /**
     * @return FormTypeRegistryInterface
     */
    protected function getConditionFormRegistry() {
        return $this->get($this->getConditionFormRegistryName());
    }

    /**
     * @return FormTypeRegistryInterface
     */
    protected function getActionFormRegistry() {
        return $this->get($this->getActionFormRegistryName());
    }

    /**
     * @param $type
     * @return \Symfony\Component\Form\FormInterface
     * @throws \Exception
     */
    protected function getConditionForm($type) {
        if (!$this->getConditionFormRegistry()->has($type, 'default')) {
            throw new \Exception("Form not found for $type");
        }

        return $this->getFormFactory()->createNamed('', $this->getConditionFormRegistry()->get($type, 'default'));
    }

    /**
     * @param $type
     * @return \Symfony\Component\Form\FormInterface
     * @throws \Exception
     */
    protected function getActionForm($type) {
        if (!$this->getActionFormRegistry()->has($type, 'default')) {
            throw new \Exception("Form not found for $type");
        }

        return $this->getFormFactory()->createNamed('', $this->getActionFormRegistry()->get($type, 'default'));
    }

    /**
     * @param $class
     * @param $type
     */
    protected function assertConditionForm($class, $type) {
        $conditionForm = $this->getConditionForm($type);

        $this->assertInstanceOf(FormInterface::class, $conditionForm);
        $this->assertTrue($this->getConditionFormRegistry()->has($type, 'default'));
        $this->assertSame($class, $this->getConditionFormRegistry()->get($type, 'default'));
    }

    /**
     * @param $type
     * @param $data
     * @return ConditionInterface
     */
    protected function createConditionWithForm($type, $data) {
        $form = $this->getFormFactory()->createNamed('', $this->getConditionFormClass());

        $formData = [
            'type' => $type,
            'configuration' => $data
        ];

        $form->submit($formData);

        $condition = $form->getData();

        $this->assertInstanceOf(ConditionInterface::class, $condition);

        return $condition;
    }

    /**
     * @param $subject
     * @param RuleInterface $rule
     * @param bool $trueOrFalse
     */
    protected function assertPriceRuleCondition($subject, RuleInterface $rule, $trueOrFalse = true) {
        $result = $this->getConditionValidator()->isValid($subject, $rule);
        if ($trueOrFalse) {
            $this->assertTrue($result);
        }
        else {
            $this->assertFalse($result);
        }
    }

    /**
     * @param $subject
     * @param ConditionInterface $condition
     * @param bool $trueOrFalse
     */
    protected function assertRuleCondition($subject, ConditionInterface $condition, $trueOrFalse = true) {
        $rule = $this->createRule();
        $rule->addCondition($condition);

        $this->assertPriceRuleCondition($subject, $rule, $trueOrFalse);
    }

    /**
     * @param $class
     * @param $type
     */
    protected function assertActionForm($class, $type) {
        $conditionForm = $this->getActionForm($type);

        $this->assertInstanceOf(FormInterface::class, $conditionForm);
        $this->assertTrue($this->getActionFormRegistry()->has($type, 'default'));
        $this->assertSame($class, $this->getActionFormRegistry()->get($type, 'default'));
    }

    /**
     * @param $type
     * @param $data
     * @return ActionInterface
     */
    protected function createActionWithForm($type, $data) {
        $form = $this->getFormFactory()->createNamed('', $this->getActionFormClass());

        $formData = [
            'type' => $type,
            'configuration' => $data
        ];

        $form->submit($formData);

        $action = $form->getData();

        $this->assertInstanceOf(ActionInterface::class, $action);

        return $action;
    }
}
