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
use CoreShop\Component\Customer\Model\CustomerGroupInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

final class CustomerGroupContext implements Context
{
    private RepositoryInterface $customerGroupRepository;

    public function __construct(RepositoryInterface $customerGroupRepository)
    {
        $this->customerGroupRepository = $customerGroupRepository;
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
            sprintf('%d customer-groups has been found with name "%s".', count($group), $name)
        );

        return reset($group);
    }
}
