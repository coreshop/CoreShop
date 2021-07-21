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

namespace CoreShop\Behat\Page\Frontend\Checkout;

use CoreShop\Behat\Page\Frontend\AbstractFrontendPage;
use CoreShop\Component\Core\Model\PaymentProviderInterface;

class PaymentPage extends AbstractFrontendPage implements PaymentPageInterface
{
    public function getRouteName(): string
    {
        return 'coreshop_checkout';
    }

    public function selectPaymentProvider(PaymentProviderInterface $paymentProvider): void
    {
        $this->getElement('payment_provider')->selectOption($paymentProvider->getId());
    }

    public function submitStep(): void
    {
        $this->getElement('submit_payment_step')->click();
    }

    protected function getAdditionalParameters(): array
    {
        return [
            'stepIdentifier' => 'payment'
        ];
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'submit_payment_step' => '[data-test-submit-payment-step]',
            'payment_provider' => '[data-test-select-payment-provider]',
        ]);
    }
}
