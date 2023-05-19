<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\PayumBundle\Action\Paypal\ExpressCheckout;

use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Core\Model\PaymentInterface;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\Convert;

final class ConvertPaymentAction implements ActionInterface
{
    public function __construct(
        private int $decimalFactor,
        private int $decimalPrecision,
    ) {
    }

    /**
     * @param Convert $request
     */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getSource();
        /** @var OrderInterface $order */
        $order = $payment->getOrder();

        $details = [];
        $details['PAYMENTREQUEST_0_INVNUM'] = $payment->getNumber();
        $details['PAYMENTREQUEST_0_CURRENCYCODE'] = $order->getCurrency()->getIsoCode();
        $details['PAYMENTREQUEST_0_AMT'] = $this->formatPrice($order->getTotal(true));
        $details['PAYMENTREQUEST_0_ITEMAMT'] = $this->formatPrice($order->getTotal(true));

        $details = $this->prepareAddressData($order, $details);

        $m = 0;
        foreach ($order->getItems() as $item) {
            $details['L_PAYMENTREQUEST_0_NAME' . $m] = $item->getName();
            $details['L_PAYMENTREQUEST_0_AMT' . $m] = $this->formatPrice($item->getItemPrice(false));
            $details['L_PAYMENTREQUEST_0_QTY' . $m] = $item->getQuantity();

            ++$m;
        }

        if (0 !== $order->getTotalTax()) {
            $details['L_PAYMENTREQUEST_0_NAME' . $m] = 'Tax Total';
            $details['L_PAYMENTREQUEST_0_AMT' . $m] = $this->formatPrice($order->getTotalTax());
            $details['L_PAYMENTREQUEST_0_QTY' . $m] = 1;

            ++$m;
        }

        if (0 !== $order->getDiscount()) {
            $details['L_PAYMENTREQUEST_0_NAME' . $m] = 'Discount';
            $details['L_PAYMENTREQUEST_0_AMT' . $m] = $this->formatPrice($order->getDiscount(false));
            $details['L_PAYMENTREQUEST_0_QTY' . $m] = 1;

            ++$m;
        }

        if (0 !== $order->getShipping()) {
            $details['L_PAYMENTREQUEST_0_NAME' . $m] = 'Shipping Total';
            $details['L_PAYMENTREQUEST_0_AMT' . $m] = $this->formatPrice($order->getShipping(false));
            $details['L_PAYMENTREQUEST_0_QTY' . $m] = 1;
        }

        $request->setResult($details);
    }

    public function supports($request): bool
    {
        return
            $request instanceof Convert &&
            $request->getSource() instanceof PaymentInterface &&
            $request->getTo() === 'array';
    }

    private function formatPrice(int $price): float
    {
        return round($price / $this->decimalFactor, $this->decimalPrecision);
    }

    private function prepareAddressData(OrderInterface $order, array $details): array
    {
        if ($customer = $order->getCustomer()) {
            $details['EMAIL'] = $customer->getEmail();
        }

        $invoiceAddress = $order->getInvoiceAddress();

        if ($invoiceAddress) {
            if ($country = $invoiceAddress->getCountry()) {
                $details['LOCALECODE'] = $country->getIsoCode();
                $details['PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE'] = $country->getIsoCode();
            }

            $details['PAYMENTREQUEST_0_SHIPTONAME'] = $invoiceAddress->getFirstname() . ' ' . $invoiceAddress->getLastname();
            $details['PAYMENTREQUEST_0_SHIPTOSTREET'] = $invoiceAddress->getStreet();
            $details['PAYMENTREQUEST_0_SHIPTOCITY'] = $invoiceAddress->getCity();
            $details['PAYMENTREQUEST_0_SHIPTOZIP'] = $invoiceAddress->getPostcode();

            if ($invoiceAddress->getPhoneNumber() !== null) {
                $details['PAYMENTREQUEST_0_SHIPTOPHONENUM'] = $invoiceAddress->getPhoneNumber();
            }

            if ($invoiceAddress->getState()) {
                $details['PAYMENTREQUEST_0_SHIPTOSTATE'] = $invoiceAddress->getState()->getIsoCode();
            }
        }

        return $details;
    }
}
