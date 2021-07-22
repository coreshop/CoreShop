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
use CoreShop\Component\Address\Model\AddressInterface;

class AddressPage extends AbstractFrontendPage implements AddressPageInterface
{
    public function getRouteName(): string
    {
        return 'coreshop_checkout';
    }

    public function chooseDifferentShippingAddress(): void
    {
        $this->getElement('use_invoice_as_shipping')->click();
    }

    public function useShippingAddress(AddressInterface $shippingAddress): void
    {
        $this->getElement('shipping_address')->selectOption($shippingAddress->getId());
    }

    public function useInvoiceAddress(AddressInterface $invoiceAddress): void
    {
        $this->getElement('invoice_address')->selectOption($invoiceAddress->getId());
    }

    public function shippingAddressVisible(): bool
    {
        return null !== $this->getElement('shipping_address');
    }

    public function submitStep(): void
    {
        $this->getElement('submit_address_step')->click();
    }

    protected function getAdditionalParameters(): array
    {
        return [
            'stepIdentifier' => 'address'
        ];
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'submit_address_step' => '[data-test-submit-address-step]',
            'use_invoice_as_shipping' => '[data-test-use-invoice-as-shipping]',
            'invoice_address' => '[data-test-invoice-address]',
            'shipping_address' => '[data-test-shipping-address]',
        ]);
    }
}
