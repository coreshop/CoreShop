<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Component\Core\Payment\Resolver;

use CoreShop\Component\Core\Repository\PaymentProviderRepositoryInterface;
use CoreShop\Component\Payment\Resolver\PaymentProviderResolverInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;

class StoreBasedPaymentProviderResolver implements PaymentProviderResolverInterface
{
    public function __construct(
        private PaymentProviderRepositoryInterface $paymentProviderRepository,
        private StoreContextInterface $storeContext,
    ) {
    }

    public function resolvePaymentProviders(?ResourceInterface $subject = null): array
    {
        return $this->paymentProviderRepository->findActiveForStore($this->storeContext->getStore());
    }
}
