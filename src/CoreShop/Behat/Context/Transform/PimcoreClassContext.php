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

declare(strict_types=1);

namespace CoreShop\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\ClassStorageInterface;
use CoreShop\Behat\Service\SharedStorageInterface;
use Pimcore\Cache\Runtime;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Fieldcollection\Definition;
use Webmozart\Assert\Assert;

final class PimcoreClassContext implements Context
{
    public function __construct(private SharedStorageInterface $sharedStorage, private ClassStorageInterface $classStorage)
    {
    }

    /**
     * @Transform /^class "([^"]+)"$/
     */
    public function class($name): ClassDefinition
    {
        Runtime::clear();

        $fqcp = sprintf('%s/DataObject/%s.php', PIMCORE_CLASS_DIRECTORY, $name);
        $fqcn = sprintf('\\Pimcore\\Model\\DataObject\\%s', $name);

        if (file_exists($fqcp) && !class_exists($fqcn)) {
            require_once $fqcp;
        }

        $classDefinition = ClassDefinition::getByName($name);

        Assert::notNull($classDefinition, sprintf('Class Definition for class with name %s not found', $name));

        return $classDefinition;
    }

    /**
     * @Transform /^behat-class "([^"]+)"$/
     */
    public function behatClass($name): ClassDefinition
    {
        return $this->class($this->classStorage->get($name));
    }

    /**
     * @Transform /^field-collection "([^"]+)"$/
     */
    public function fieldCollection($name): Definition
    {
        $name = $this->classStorage->get($name);

        $definition = Definition::getByKey($name);

        Assert::notNull($definition, sprintf('Definition for fieldcollection with key %s not found', $name));

        return $definition;
    }

    /**
     * @Transform /^object-instance$/
     */
    public function objectInstance(): Concrete
    {
        return $this->sharedStorage->get('object-instance');
    }

    /**
     * @Transform /^object-instance-2$/
     */
    public function objectInstance2(): Concrete
    {
        return $this->sharedStorage->get('object-instance-2');
    }

    /**
     * @Transform /^object-instance "([^"]+)"$/
     */
    public function objectInstanceWithKey($key): Concrete
    {
        return Concrete::getByPath('/' . $key);
    }

    /**
     * @Transform /^definition/
     * @Transform /^definitions/
     */
    public function definition(): ClassDefinition|Definition
    {
        Runtime::clear();

        $name = $this->sharedStorage->get('pimcore_definition_name');
        $class = $this->sharedStorage->get('pimcore_definition_class');

        if ($class === ClassDefinition::class) {
            return ClassDefinition::getByName($this->classStorage->get($name));
        }

        return $class::getByKey($this->classStorage->get($name));
    }
}
