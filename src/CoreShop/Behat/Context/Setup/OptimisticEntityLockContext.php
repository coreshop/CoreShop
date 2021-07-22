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

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Bundle\OptimisticEntityLockBundle\Exception\OptimisticLockException;
use CoreShop\Bundle\OptimisticEntityLockBundle\Manager\EntityLockManagerInterface;
use Pimcore\Model\DataObject\Concrete;
use Webmozart\Assert\Assert;

final class OptimisticEntityLockContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var EntityLockManagerInterface
     */
    private $entityLockManager;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        EntityLockManagerInterface $entityLockManager
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->entityLockManager = $entityLockManager;
    }

    /**
     * @Given /^I successfully lock the (object-instance) with the current version$/
     */
    public function iLockTheObjectInstanceWithCurrentVersion(Concrete $dataObject)
    {
        $this->entityLockManager->lock($dataObject, $dataObject->getValueForFieldName('optimisticLockVersion'));
    }

    /**
     * @Given /^I unsuccessfully lock the (object-instance) with the current version$/
     * @Given /^I unsuccessfully lock the (object-instance-2) with the current version$/
     */
    public function iUnsuccessfullyLockTheObjectInstanceWithCurrentVersion(Concrete $dataObject)
    {
        Assert::throws(function () use ($dataObject) {
            $this->entityLockManager->lock($dataObject, $dataObject->getValueForFieldName('optimisticLockVersion'));
        }, OptimisticLockException::class);
    }

    /**
     * @Given /^I unsuccessfully save versioned (object-instance)$/
     * @Given /^I unsuccessfully save versioned (object-instance-2)$/
     */
    public function iUnsuccessfullySaveTheObject(Concrete $dataObject)
    {
        Assert::throws(function () use ($dataObject) {
            $dataObject->save();
        }, OptimisticLockException::class);
    }
    /**
     * @Given /^I successfully save versioned (object-instance)$/
     * @Given /^I successfully save versioned (object-instance-2)$/
     */
    public function iSuccessfullySaveTheObject(Concrete $dataObject)
    {
        $dataObject->save();
    }
}
