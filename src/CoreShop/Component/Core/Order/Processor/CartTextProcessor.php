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

use CoreShop\Component\Core\Model\OrderItemInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;
use CoreShop\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use Pimcore\Model\DataObject\AbstractObject;

final class CartTextProcessor implements CartProcessorInterface
{
    public function __construct(
        protected TranslationLocaleProviderInterface $localeProvider,
    ) {
    }

    public function process(OrderInterface $cart): void
    {
        /**
         * @var OrderItemInterface $item
         */
        foreach ($cart->getItems() as $item) {
            $product = $item->getProduct();

            if (!$product instanceof PurchasableInterface) {
                continue;
            }

            foreach ($this->localeProvider->getDefinedLocalesCodes() as $locale) {
                $item->setName($product->getName($locale), $locale);
            }

            $item->setObjectId((float)$product->getId());

            if (($product instanceof AbstractObject) && $product->getType() === AbstractObject::OBJECT_TYPE_VARIANT) {
                $mainProduct = $this->findVariantMain($product);
                $item->setMainObjectId((float)$mainProduct->getId());
            }
        }
    }

    private function findVariantMain(AbstractObject $object): AbstractObject
    {
        $master = $object;
        while ($master->getType() === AbstractObject::OBJECT_TYPE_VARIANT) {
            if ($master->getParent() instanceof $master) {
                $master = $master->getParent();
            }
        }

        return $master;
    }
}
