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

declare(strict_types=1);

namespace CoreShop\Component\Customer\Context;

use CoreShop\Component\Customer\Model\CustomerInterface;
use Laminas\Stdlib\PriorityQueue;

final class CompositeCustomerContext implements CustomerContextInterface
{
    /**
     * @var PriorityQueue|CustomerContextInterface[]
     */
    private PriorityQueue $customerContexts;

    public function __construct()
    {
        $this->customerContexts = new PriorityQueue();
    }

    public function addContext(CustomerContextInterface $customerContext, int $priority = 0): void
    {
        $this->customerContexts->insert($customerContext, $priority);
    }

    public function getCustomer(): CustomerInterface
    {
        foreach ($this->customerContexts as $customerContext) {
            try {
                return $customerContext->getCustomer();
            } catch (CustomerNotFoundException $exception) {
                continue;
            }
        }

        throw new CustomerNotFoundException();
    }
}
