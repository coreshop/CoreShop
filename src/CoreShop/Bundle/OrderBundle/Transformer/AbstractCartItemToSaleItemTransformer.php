<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\OrderBundle\Transformer;

use CoreShop\Component\Currency\Converter\CurrencyConverterInterface;
use CoreShop\Component\Order\Model\CartItemInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Model\ProposalInterface;
use CoreShop\Component\Order\Model\ProposalItemInterface;
use CoreShop\Component\Order\Model\SaleInterface;
use CoreShop\Component\Order\Model\SaleItemInterface;
use CoreShop\Component\Order\Taxation\ProposalItemTaxCollectorInterface;
use CoreShop\Component\Order\Transformer\ProposalItemTransformerInterface;
use CoreShop\Component\Resource\Pimcore\ObjectServiceInterface;
use CoreShop\Component\Taxation\Model\TaxItemInterface;
use Pimcore\Model\Object\Fieldcollection;
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
     * @var ProposalItemTaxCollectorInterface
     */
    protected $cartItemTaxCollector;

    /**
     * @var CurrencyConverterInterface
     */
    protected $currencyConverter;

    /**
     * @param ObjectServiceInterface $objectService
     * @param string $pathForItems
     * @param ProposalItemTaxCollectorInterface $cartItemTaxCollector
     * @param TransformerEventDispatcherInterface $eventDispatcher
     * @param CurrencyConverterInterface $currencyConverter
     */
    public function __construct(
        ObjectServiceInterface $objectService,
        $pathForItems,
        ProposalItemTaxCollectorInterface $cartItemTaxCollector,
        TransformerEventDispatcherInterface $eventDispatcher,
        CurrencyConverterInterface $currencyConverter
    )
    {
        $this->objectService = $objectService;
        $this->pathForItems = $pathForItems;
        $this->cartItemTaxCollector = $cartItemTaxCollector;
        $this->eventDispatcher = $eventDispatcher;
        $this->currencyConverter = $currencyConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function transformSaleItem(ProposalInterface $sale, ProposalItemInterface $cartItem, ProposalItemInterface $saleItem, $type)
    {
        /**
         * @var $sale SaleInterface
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

        $saleItem->setKey($cartItem->getKey());
        $saleItem->setParent($itemFolder);
        $saleItem->setPublished(true);

        /*$baseTaxesFieldCollection = new Fieldcollection();
        $baseTaxesFieldCollection->setItems($this->cartItemTaxCollector->getTaxes($cartItem));

        $taxesFieldCollection = new Fieldcollection();
        $taxesFieldCollection->setItems($this->cartItemTaxCollector->getTaxes($cartItem));

        foreach ($taxesFieldCollection->getItems() as $item) {
            if ($item instanceof TaxItemInterface) {
                $item->setAmount($this->currencyConverter->convert($item->getAmount(), $fromCurrency, $toCurrency));
            }
        }

        $saleItem->setTaxes($taxesFieldCollection);
        $saleItem->setBaseTaxes($baseTaxesFieldCollection);*/

        $saleItem->setProduct($cartItem->getProduct());
        $saleItem->setItemWholesalePrice($this->currencyConverter->convert($cartItem->getItemWholesalePrice(), $fromCurrency, $toCurrency));

        $saleItem->setItemRetailPrice($this->currencyConverter->convert($cartItem->getItemRetailPrice(true), $fromCurrency, $toCurrency), true);
        $saleItem->setItemRetailPrice($this->currencyConverter->convert($cartItem->getItemRetailPrice(false), $fromCurrency, $toCurrency), false);
        $saleItem->setTotal($this->currencyConverter->convert($cartItem->getTotal(true), $fromCurrency, $toCurrency), true);
        $saleItem->setTotal($this->currencyConverter->convert($cartItem->getTotal(false), $fromCurrency, $toCurrency), false);
        $saleItem->setItemPrice($this->currencyConverter->convert($cartItem->getItemPrice(true), $fromCurrency, $toCurrency), true);
        $saleItem->setItemPrice($this->currencyConverter->convert($cartItem->getItemPrice(false), $fromCurrency, $toCurrency), false);
        $saleItem->setTotalTax($this->currencyConverter->convert($cartItem->getTotalTax(), $fromCurrency, $toCurrency));
        $saleItem->setItemTax($this->currencyConverter->convert($cartItem->getItemTax(), $fromCurrency, $toCurrency));

        $saleItem->setBaseItemRetailPrice($cartItem->getItemRetailPrice(true), true);
        $saleItem->setBaseItemRetailPrice($cartItem->getItemRetailPrice(false), false);
        $saleItem->setBaseTotal($cartItem->getTotal(true), true);
        $saleItem->setBaseTotal($cartItem->getTotal(false), false);
        $saleItem->setBaseItemPrice($cartItem->getItemPrice(true), true);
        $saleItem->setBaseItemPrice($cartItem->getItemPrice(false), false);
        $saleItem->setBaseTotalTax($cartItem->getTotalTax());
        $saleItem->setBaseItemTax($cartItem->getItemTax());

        $saleItem->setItemWeight($cartItem->getItemWeight());
        $saleItem->setTotalWeight($cartItem->getTotalWeight());
        $saleItem->save();

        $sale->addItem($saleItem);

        $this->eventDispatcher->dispatchPostEvent($type, $cartItem, ['sale' => $sale, 'cart' => $cartItem->getCart(), 'item' => $saleItem]);

        return $saleItem;
    }
}
