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

namespace CoreShop\Component\Payment\Resolver;

use CoreShop\Component\Payment\Repository\PaymentProviderRepositoryInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;

class PaymentProviderResolver implements PaymentProviderResolverInterface
{
    /**
     * @var PaymentProviderRepositoryInterface
     */
    private $paymentProviderRepository;

    /**
     * @param PaymentProviderRepositoryInterface $paymentProviderRepository
     */
    public function __construct(PaymentProviderRepositoryInterface $paymentProviderRepository)
    {
        $this->paymentProviderRepository = $paymentProviderRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function resolvePaymentProviders(ResourceInterface $subject = null)
    {
        return $this->paymentProviderRepository->findActive();
    }
}