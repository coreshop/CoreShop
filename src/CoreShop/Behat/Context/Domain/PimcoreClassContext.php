<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\ClassStorageInterface;
use CoreShop\Behat\Service\SharedStorageInterface;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Objectbrick;
use Webmozart\Assert\Assert;

final class PimcoreClassContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var ClassStorageInterface
     */
    private $classStorage;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param ClassStorageInterface  $classStorage
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ClassStorageInterface $classStorage
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->classStorage = $classStorage;
    }

    /**
     * @Then /^there should be a pimcore class "([^"]+)"$/
     */
    public function thereShouldBeAPimcoreClass($name)
    {
        $definition = ClassDefinition::getByName($this->classStorage->get($name));

        Assert::notNull(
            $definition,
            sprintf('Pimcore Class with name "%s" not found', $name)
        );
    }

    /**
     * @Then /^there should be a pimcore brick "([^"]+)"$/
     */
    public function thereShouldBeAPimcoreBrick($name)
    {
        $definition = Objectbrick\Definition::getByKey($this->classStorage->get($name));

        Assert::notNull(
            $definition,
            sprintf('Pimcore Brick with key "%s" not found', $name)
        );
    }

    /**
     * @Then /^there should be a pimcore field-collection "([^"]+)"$/
     */
    public function thereShouldBeAPimcoreCollection($name)
    {
        $definition = Fieldcollection\Definition::getByKey($this->classStorage->get($name));

        Assert::notNull(
            $definition,
            sprintf('Pimcore Fieldcollection with key "%s" not found', $name)
        );
    }

    /**
     * @Then /^the (definition) should have a field named "([^"]+)"$/
     */
    public function theDefinitionShouldHaveAField($definition, $name)
    {
        if ($definition instanceof Objectbrick\Definition) {
            $field = $definition->getFieldDefinition($name);
        } elseif ($definition instanceof Fieldcollection\Definition) {
            $field = $definition->getFieldDefinition($name);
        } elseif ($definition instanceof ClassDefinition) {
            $field = $definition->getFieldDefinition($name);
        } else {
            throw new \InvalidArgumentException(sprintf('Definition with type %s is not supported', null !== $definition ? get_class($definition) : 'null'));
        }

        Assert::isInstanceOf(
            $field,
            ClassDefinition\Data::class,
            sprintf('Could not find field with name "%s" in definition', $name)
        );
    }

    /**
     * @Then /^an instance of (definition) should implement "([^"]+)"$/
     */
    public function anInstanceofDefinitionShouldImplement($definition, $class)
    {
        if ($definition instanceof ClassDefinition) {
            $className = sprintf('Pimcore\\Model\\DataObject\\%s', $definition->getName());
            $instance = new $className();

            Assert::isInstanceOf($instance, $class);
        }
    }
}
