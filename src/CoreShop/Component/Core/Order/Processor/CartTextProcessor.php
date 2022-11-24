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

namespace CoreShop\Component\Core\Order\Processor;

use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;
use CoreShop\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;

final class CartTextProcessor implements CartProcessorInterface
{
    public function __construct(
        protected TranslationLocaleProviderInterface $localeProvider,
    ) {
    }

    public function process(OrderInterface $cart): void
    {
        foreach ($cart->getItems() as $item) {
            foreach ($this->localeProvider->getDefinedLocalesCodes() as $locale) {
                if ($item->getProduct() instanceof PurchasableInterface) {
                    $item->setName($item->getProduct()->getName($locale), $locale);
                }
            }
        }
    }
}
