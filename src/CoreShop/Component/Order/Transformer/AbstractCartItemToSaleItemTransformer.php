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

namespace CoreShop\Component\Order\Transformer;

use CoreShop\Component\Currency\Converter\CurrencyConverterInterface;
use CoreShop\Component\Order\Model\CartItemInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Model\ProposalInterface;
use CoreShop\Component\Order\Model\ProposalItemInterface;
use CoreShop\Component\Order\Model\SaleInterface;
use CoreShop\Component\Order\Model\SaleItemInterface;
use CoreShop\Component\Pimcore\DataObject\ObjectServiceInterface;
use CoreShop\Component\Pimcore\DataObject\VersionHelper;
use CoreShop\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use CoreShop\Component\Taxation\Model\TaxItemInterface;
use Pimcore\Model\DataObject\Fieldcollection;
use Webmozart\Assert\Assert;

abstract class AbstractCartItemToSaleItemTransformer implements ProposalItemTransformerInterface
{
    /**
     * @var ObjectServiceInterface
     */
    protected $objectService;

    /**
     * @var string
     */
    protected $pathForItems;

    /**
     * @var TransformerEventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var CurrencyConverterInterface
     */
    protected $currencyConverter;

    /**
     * @var TranslationLocaleProviderInterface
     */
    protected $localeProvider;

    /**
     * @param ObjectServiceInterface              $objectService
     * @param string                              $pathForItems
     * @param TransformerEventDispatcherInterface $eventDispatcher
     * @param CurrencyConverterInterface          $currencyConverter
     * @param TranslationLocaleProviderInterface  $localeProvider
     */
    public function __construct(
        ObjectServiceInterface $objectService,
        $pathForItems,
        TransformerEventDispatcherInterface $eventDispatcher,
        CurrencyConverterInterface $currencyConverter,
        TranslationLocaleProviderInterface $localeProvider
    ) {
        $this->objectService = $objectService;
        $this->pathForItems = $pathForItems;
        $this->eventDispatcher = $eventDispatcher;
        $this->currencyConverter = $currencyConverter;
        $this->localeProvider = $localeProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function transformSaleItem(ProposalInterface $sale, ProposalItemInterface $cartItem, ProposalItemInterface $saleItem, $type)
    {
        /**
         * @var $sale     SaleInterface
         * @var $cartItem CartItemInterface
         * @var $saleItem OrderItemInterface
         */
        Assert::isInstanceOf($cartItem, CartItemInterface::class);
        Assert::isInstanceOf($saleItem, SaleItemInterface::class);
        Assert::isInstanceOf($sale, SaleInterface::class);

        $fromCurrency = $sale->getBaseCurrency()->getIsoCode();
        $toCurrency = $sale->getCurrency()->getIsoCode();

        $this->eventDispatcher->dispatchPreEvent($type, $cartItem, ['sale' => $sale, 'cart' => $cartItem->getCart(), 'item' => $saleItem]);

        $itemFolder = $this->objectService->createFolderByPath($sale->getFullPath() . '/' . $this->pathForItems);

        $this->objectService->copyObject($cartItem, $saleItem);

        $saleItem->setKey($cartItem->getKey() ?? uniqid());
        $saleItem->setParent($itemFolder);
        $saleItem->setPublished(true);

        $baseTaxesFieldCollection = new Fieldcollection();
        $baseTaxesFieldCollection->setItems($cartItem->getTaxes() instanceof Fieldcollection ? $cartItem->getTaxes()->getItems() : []);

        $taxesFieldCollection = new Fieldcollection();
        $taxesFieldCollection->setItems($cartItem->getTaxes() instanceof Fieldcollection ? $cartItem->getTaxes()->getItems() : []);

        foreach ($taxesFieldCollection->getItems() as $item) {
            if ($item instanceof TaxItemInterface) {
                $item->setAmount($this->currencyConverter->convert($item->getAmount(), $fromCurrency, $toCurrency));
            }
        }

        $saleItem->setTaxes($taxesFieldCollection);
        $saleItem->setBaseTaxes($baseTaxesFieldCollection);

        $saleItem->setProduct($cartItem->getProduct());
        $saleItem->setItemWholesalePrice($this->currencyConverter->convert($cartItem->getItemWholesalePrice(), $fromCurrency, $toCurrency));

        $saleItem->setItemRetailPrice($this->currencyConverter->convert($cartItem->getItemRetailPrice(true), $fromCurrency, $toCurrency), true);
        $saleItem->setItemRetailPrice($this->currencyConverter->convert($cartItem->getItemRetailPrice(false), $fromCurrency, $toCurrency), false);
        $saleItem->setItemDiscountPrice($this->currencyConverter->convert($cartItem->getItemDiscountPrice(true), $fromCurrency, $toCurrency), true);
        $saleItem->setItemDiscountPrice($this->currencyConverter->convert($cartItem->getItemDiscountPrice(false), $fromCurrency, $toCurrency), false);
        $saleItem->setItemDiscount($this->currencyConverter->convert($cartItem->getItemDiscount(true), $fromCurrency, $toCurrency), true);
        $saleItem->setItemDiscount($this->currencyConverter->convert($cartItem->getItemDiscount(false), $fromCurrency, $toCurrency), false);
        $saleItem->setTotal($this->currencyConverter->convert($cartItem->getTotal(true), $fromCurrency, $toCurrency), true);
        $saleItem->setTotal($this->currencyConverter->convert($cartItem->getTotal(false), $fromCurrency, $toCurrency), false);
        $saleItem->setItemPrice($this->currencyConverter->convert($cartItem->getItemPrice(true), $fromCurrency, $toCurrency), true);
        $saleItem->setItemPrice($this->currencyConverter->convert($cartItem->getItemPrice(false), $fromCurrency, $toCurrency), false);
        $saleItem->setItemTax($this->currencyConverter->convert($cartItem->getItemTax(), $fromCurrency, $toCurrency));

        $saleItem->setBaseItemRetailPrice($cartItem->getItemRetailPrice(true), true);
        $saleItem->setBaseItemRetailPrice($cartItem->getItemRetailPrice(false), false);
        $saleItem->setBaseTotal($cartItem->getTotal(true), true);
        $saleItem->setBaseTotal($cartItem->getTotal(false), false);
        $saleItem->setBaseItemPrice($cartItem->getItemPrice(true), true);
        $saleItem->setBaseItemPrice($cartItem->getItemPrice(false), false);
        $saleItem->setBaseItemTax($cartItem->getItemTax());

        foreach ($cartItem->getAdjustments() as $adjustment) {
            $saleItem->addAdjustment($adjustment);

            $baseAdjustment = clone $adjustment;
            $baseAdjustmentGross = $this->currencyConverter->convert($baseAdjustment->getAmount(true), $fromCurrency, $toCurrency);
            $baseAdjustmentNet = $this->currencyConverter->convert($baseAdjustment->getAmount(false), $fromCurrency, $toCurrency);

            $baseAdjustment->setAmount($baseAdjustmentGross, $baseAdjustmentNet);

            $saleItem->addBaseAdjustment($baseAdjustment);
        }

        foreach ($this->localeProvider->getDefinedLocalesCodes() as $locale) {
            $saleItem->setName($cartItem->getProduct()->getName($locale), $locale);
        }

        VersionHelper::useVersioning(function () use ($saleItem) {
            $saleItem->save();
        }, false);

        $sale->addItem($saleItem);

        $this->eventDispatcher->dispatchPostEvent($type, $cartItem, ['sale' => $sale, 'cart' => $cartItem->getCart(), 'item' => $saleItem]);

        return $saleItem;
    }
}
