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

namespace CoreShop\Component\Customer\Context;

use Zend\Stdlib\PriorityQueue;

final class CompositeCustomerContext implements CustomerContextInterface
{
    /**
     * @var PriorityQueue|CustomerContextInterface[]
     */
    private $customerContexts;

    public function __construct()
    {
        $this->customerContexts = new PriorityQueue();
    }

    /**
     * @param CustomerContextInterface $customerContext
     * @param int                      $priority
     */
    public function addContext(CustomerContextInterface $customerContext, $priority = 0)
    {
        $this->customerContexts->insert($customerContext, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomer()
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
