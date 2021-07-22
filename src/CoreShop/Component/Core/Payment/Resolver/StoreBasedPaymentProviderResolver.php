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

namespace CoreShop\Component\Core\Payment\Resolver;

use CoreShop\Component\Core\Repository\PaymentProviderRepositoryInterface;
use CoreShop\Component\Payment\Resolver\PaymentProviderResolverInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;

class StoreBasedPaymentProviderResolver implements PaymentProviderResolverInterface
{
    private PaymentProviderRepositoryInterface $paymentProviderRepository;
    private StoreContextInterface $storeContext;

    public function __construct(
        PaymentProviderRepositoryInterface $paymentProviderRepository,
        StoreContextInterface $storeContext
    )
    {
        $this->paymentProviderRepository = $paymentProviderRepository;
        $this->storeContext = $storeContext;
    }

    public function resolvePaymentProviders(ResourceInterface $subject = null): array
    {
        return $this->paymentProviderRepository->findActiveForStore($this->storeContext->getStore());
    }
}
