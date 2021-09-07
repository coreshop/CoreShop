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

namespace CoreShop\Behat\Page\Frontend\Checkout;

use CoreShop\Behat\Page\Frontend\FrontendPageInterface;
use CoreShop\Component\Address\Model\AddressInterface;

interface AddressPageInterface extends FrontendPageInterface
{
    public function chooseDifferentShippingAddress(): void;

    public function useShippingAddress(AddressInterface $shippingAddress): void;

    public function useInvoiceAddress(AddressInterface $invoiceAddress): void;

    public function shippingAddressVisible(): bool;

    public function submitStep(): void;
}
