<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Core\Repository\PaymentProviderRepositoryInterface;
use Webmozart\Assert\Assert;

final class PaymentContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var PaymentProviderRepositoryInterface
     */
    private $paymentProviderRepository;

    /**
     * @param SharedStorageInterface             $sharedStorage
     * @param PaymentProviderRepositoryInterface $paymentProviderRepository
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        PaymentProviderRepositoryInterface $paymentProviderRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->paymentProviderRepository = $paymentProviderRepository;
    }

    /**
     * @Transform /^payment provider "([^"]+)"$/
     */
    public function getPaymentProviderByTitle($title)
    {
        $paymentProviders = $this->paymentProviderRepository->findByTitle($title, 'en');

        Assert::eq(
            count($paymentProviders),
            1,
            sprintf('%d payment provider has been found with name "%s".', count($paymentProviders), $title)
        );

        return reset($paymentProviders);
    }

    /**
     * @Transform /^payment provider/
     */
    public function country()
    {
        return $this->sharedStorage->get('paymentProvider');
    }
}
