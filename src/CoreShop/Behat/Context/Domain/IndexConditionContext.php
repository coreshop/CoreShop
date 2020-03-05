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

declare(strict_types=1);

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Index\Condition\ConditionInterface;
use CoreShop\Component\Index\Worker\WorkerInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use Webmozart\Assert\Assert;

final class IndexConditionContext implements Context
{
    private $sharedStorage;
    private $workerRegistry;

    public function __construct(SharedStorageInterface $sharedStorage, ServiceRegistryInterface $workerRegistry)
    {
        $this->sharedStorage = $sharedStorage;
        $this->workerRegistry = $workerRegistry;
    }

    /**
     * @Then /^the (condition) rendered for "([^"]+)" should look like "([^"]+)"$/
     */
    public function theConditionForTypeShouldLookLike(ConditionInterface $condition, $rendererType, $expected)
    {
        $is = $this->render($rendererType, $condition);

        Assert::eq(
            $is,
            $expected,
            sprintf('Expected condition to look like %s but got %s instead".', $expected, $is)
        );
    }

    private function render($worker, $condition)
    {
        if (!$this->workerRegistry->has($worker)) {
            throw new \InvalidArgumentException(sprintf('Worker with type %s not found', $worker));
        }

        /**
         * @var WorkerInterface $worker
         */
        $worker = $this->workerRegistry->get($worker);

        return $worker->renderCondition($condition);
    }
}
