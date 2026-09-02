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

namespace CoreShop\Behat\Page\Frontend\Checkout;

use CoreShop\Bundle\TestBundle\Page\Frontend\FrontendPageInterface;
use CoreShop\Component\Address\Model\AddressInterface;

interface AddressPageInterface extends FrontendPageInterface
{
    public function chooseDifferentShippingAddress(): void;

    public function useShippingAddress(AddressInterface $shippingAddress): void;

    public function useInvoiceAddress(AddressInterface $invoiceAddress): void;

    public function shippingAddressVisible(): bool;

    public function submitStep(): void;
}
