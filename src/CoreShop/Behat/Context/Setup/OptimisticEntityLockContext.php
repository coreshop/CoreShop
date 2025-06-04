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

use Behat\Behat\Context\Context;
use CoreShop\Bundle\OptimisticEntityLockBundle\Exception\OptimisticLockException;
use CoreShop\Bundle\OptimisticEntityLockBundle\Manager\EntityLockManagerInterface;
use Pimcore\Model\DataObject\Concrete;
use Webmozart\Assert\Assert;

final class OptimisticEntityLockContext implements Context
{
    public function __construct(
        private EntityLockManagerInterface $entityLockManager,
    ) {
    }

    /**
     * @Given /^I successfully lock the (object-instance) with the current version$/
     */
    public function iLockTheObjectInstanceWithCurrentVersion(Concrete $dataObject): void
    {
        $this->entityLockManager->lock($dataObject, $dataObject->getValueForFieldName('optimisticLockVersion'));
    }

    /**
     * @Given /^I unsuccessfully lock the (object-instance) with the current version$/
     * @Given /^I unsuccessfully lock the (object-instance-2) with the current version$/
     */
    public function iUnsuccessfullyLockTheObjectInstanceWithCurrentVersion(Concrete $dataObject): void
    {
        Assert::throws(function () use ($dataObject) {
            $this->entityLockManager->lock($dataObject, $dataObject->getValueForFieldName('optimisticLockVersion'));
        }, OptimisticLockException::class);
    }

    /**
     * @Given /^I unsuccessfully save versioned (object-instance)$/
     * @Given /^I unsuccessfully save versioned (object-instance-2)$/
     */
    public function iUnsuccessfullySaveTheObject(Concrete $dataObject): void
    {
        Assert::throws(function () use ($dataObject) {
            $dataObject->save();
        }, OptimisticLockException::class);
    }

    /**
     * @Given /^I successfully save versioned (object-instance)$/
     * @Given /^I successfully save versioned (object-instance-2)$/
     */
    public function iSuccessfullySaveTheObject(Concrete $dataObject): void
    {
        $dataObject->save();
    }
}
