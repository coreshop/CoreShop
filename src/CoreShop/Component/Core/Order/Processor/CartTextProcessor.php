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

namespace CoreShop\Component\Core\Order\Processor;

use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;
use CoreShop\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;

final class CartTextProcessor implements CartProcessorInterface
{
    /**
     * @var TranslationLocaleProviderInterface
     */
    protected $localeProvider;

    public function __construct(TranslationLocaleProviderInterface $localeProvider)
    {
        $this->localeProvider = $localeProvider;
    }

    /**
     * {@inheritdoc}
     */
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
