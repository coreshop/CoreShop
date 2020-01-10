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

namespace CoreShop\Component\Core\Payment\Resolver;

use CoreShop\Component\Core\Repository\PaymentProviderRepositoryInterface;
use CoreShop\Component\Payment\Resolver\PaymentProviderResolverInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;

class StoreBasedPaymentProviderResolver implements PaymentProviderResolverInterface
{
    /**
     * @var PaymentProviderRepositoryInterface
     */
    private $paymentProviderRepository;

    /**
     * @var StoreContextInterface
     */
    private $storeContext;

    /**
     * @param PaymentProviderRepositoryInterface $paymentProviderRepository
     * @param StoreContextInterface              $storeContext
     */
    public function __construct(PaymentProviderRepositoryInterface $paymentProviderRepository, StoreContextInterface $storeContext)
    {
        $this->paymentProviderRepository = $paymentProviderRepository;
        $this->storeContext = $storeContext;
    }

    /**
     * {@inheritdoc}
     */
    public function resolvePaymentProviders(ResourceInterface $subject = null)
    {
        return $this->paymentProviderRepository->findActiveForStore($this->storeContext->getStore());
    }
}
