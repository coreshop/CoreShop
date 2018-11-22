<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Behat\Context\Setup;

use CoreShop\Bundle\ResourceBundle\Form\Registry\FormTypeRegistryInterface;
use CoreShop\Component\Rule\Model\ConditionInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Webmozart\Assert\Assert;

trait ConditionFormTrait
{
    /**
     * @param string $class
     * @param string $type
     * @throws \Exception
     */
    protected function assertConditionForm($class, $type)
    {
        $conditionForm = $this->getConditionForm($type);

        Assert::isInstanceOf($conditionForm, FormInterface::class);
        Assert::true($this->getConditionFormRegistry()->has($type, 'default'));
        Assert::same($class, $this->getConditionFormRegistry()->get($type, 'default'));
    }

    /**
     * @param string $type
     * @param mixed $data
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

        Assert::true($form->isValid());

        $condition = $form->getData();

        Assert::isInstanceOf($condition, ConditionInterface::class);

        return $condition;
    }

    /**
     * @param string $type
     * @return FormInterface
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
     * @return FormTypeRegistryInterface
     */
    protected abstract function getConditionFormRegistry();

    /**
     * @return FormFactoryInterface
     */
    protected abstract function getFormFactory();

    /**
     * @return string
     */
    protected abstract function getConditionFormClass();
}