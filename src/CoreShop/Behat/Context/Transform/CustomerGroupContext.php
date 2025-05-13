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

namespace CoreShop\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use CoreShop\Component\Customer\Model\CustomerGroupInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

final class CustomerGroupContext implements Context
{
    public function __construct(
        private RepositoryInterface $customerGroupRepository,
    ) {
    }

    /**
     * @Transform /^customer-group "([^"]+)"$/
     */
    public function getCustomerGroupBName($name): CustomerGroupInterface
    {
        $group = $this->customerGroupRepository->findBy(['name' => $name]);

        Assert::eq(
            count($group),
            1,
            sprintf('%d customer-groups has been found with name "%s".', count($group), $name),
        );

        return reset($group);
    }
}
