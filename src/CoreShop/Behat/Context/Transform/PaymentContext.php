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
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Core\Model\PaymentProviderInterface;
use CoreShop\Component\Core\Repository\PaymentProviderRepositoryInterface;
use Webmozart\Assert\Assert;

final class PaymentContext implements Context
{
    public function __construct(private SharedStorageInterface $sharedStorage, private PaymentProviderRepositoryInterface $paymentProviderRepository)
    {
    }

    /**
     * @Transform /^payment provider "([^"]+)"$/
     */
    public function getPaymentProviderByTitle($title): PaymentProviderInterface
    {
        /**
         * @var PaymentProviderInterface[] $paymentProviders
         */
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
    public function paymentProvider(): PaymentProviderInterface
    {
        return $this->sharedStorage->get('payment-provider');
    }
}
