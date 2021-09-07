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

namespace CoreShop\Component\Customer\Context\RequestBased;

use CoreShop\Component\Customer\Context\CustomerContextInterface;
use CoreShop\Component\Customer\Context\CustomerNotFoundException;
use CoreShop\Component\Customer\Model\CustomerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class CustomerContext implements CustomerContextInterface
{
    private RequestResolverInterface $requestResolver;
    private RequestStack $requestStack;

    public function __construct(RequestResolverInterface $requestResolver, RequestStack $requestStack)
    {
        $this->requestResolver = $requestResolver;
        $this->requestStack = $requestStack;
    }

    public function getCustomer(): CustomerInterface
    {
        try {
            return $this->getCustomerForRequest($this->getMasterRequest());
        } catch (\UnexpectedValueException $exception) {
            throw new CustomerNotFoundException($exception);
        }
    }

    private function getCustomerForRequest(Request $request): CustomerInterface
    {
        $customer = $this->requestResolver->findCustomer($request);

        $this->assertCustomerWasFound($customer);

        return $customer;
    }

    private function getMasterRequest(): Request
    {
        $masterRequest = $this->requestStack->getMasterRequest();
        if (null === $masterRequest) {
            throw new \UnexpectedValueException('There are not any requests on request stack');
        }

        return $masterRequest;
    }

    private function assertCustomerWasFound(CustomerInterface $customer = null)
    {
        if (null === $customer) {
            throw new \UnexpectedValueException('Customer was not found for given request');
        }
    }
}
