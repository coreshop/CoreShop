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

use Carbon\Carbon;
use CoreShop\Component\Currency\Converter\CurrencyConverterInterface;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartItemInterface;
use CoreShop\Component\Order\Model\ProposalCartPriceRuleItemInterface;
use CoreShop\Component\Order\Model\ProposalInterface;
use CoreShop\Component\Order\Model\SaleInterface;
use CoreShop\Component\Order\NumberGenerator\NumberGeneratorInterface;
use CoreShop\Component\Order\Repository\CartPriceRuleVoucherRepositoryInterface;
use CoreShop\Component\Pimcore\DataObject\ObjectClonerInterface;
use CoreShop\Component\Pimcore\DataObject\ObjectServiceInterface;
use CoreShop\Component\Pimcore\DataObject\VersionHelper;
use CoreShop\Component\Resource\Factory\PimcoreFactoryInterface;
use CoreShop\Component\Resource\Transformer\ItemKeyTransformerInterface;
use CoreShop\Component\Taxation\Model\TaxItemInterface;
use Pimcore\Model\DataObject\Fieldcollection;
use Webmozart\Assert\Assert;

abstract class AbstractCartToSaleTransformer implements ProposalTransformerInterface
{
    /**
     * @var ProposalItemTransformerInterface
     */
    protected $cartItemToSaleItemTransformer;

    /**
     * @var ItemKeyTransformerInterface
     */
    protected $keyTransformer;

    /**
     * @var NumberGeneratorInterface
     */
    protected $numberGenerator;

    /**
     * @var string
     */
    protected $orderFolderPath;

    /**
     * @var ObjectServiceInterface
     */
    protected $objectService;

    /**
     * @var PimcoreFactoryInterface
     */
    protected $saleItemFactory;

    /**
     * @var TransformerEventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var CurrencyConverterInterface
     */
    protected $currencyConverter;

    /**
     * @var ObjectClonerInterface
     */
    protected $objectCloner;

    /**
     * @var CartPriceRuleVoucherRepositoryInterface
     */
    protected $voucherCodeRepository;

    /**
     * @param ProposalItemTransformerInterface        $cartItemToSaleItemTransformer
     * @param ItemKeyTransformerInterface             $keyTransformer
     * @param NumberGeneratorInterface                $numberGenerator
     * @param string                                  $orderFolderPath
     * @param ObjectServiceInterface                  $objectService
     * @param PimcoreFactoryInterface                 $saleItemFactory
     * @param TransformerEventDispatcherInterface     $eventDispatcher
     * @param CurrencyConverterInterface              $currencyConverter
     * @param ObjectClonerInterface                   $objectCloner
     * @param CartPriceRuleVoucherRepositoryInterface $voucherCodeRepository
     */
    public function __construct(
        ProposalItemTransformerInterface $cartItemToSaleItemTransformer,
        ItemKeyTransformerInterface $keyTransformer,
        NumberGeneratorInterface $numberGenerator,
        $orderFolderPath,
        ObjectServiceInterface $objectService,
        PimcoreFactoryInterface $saleItemFactory,
        TransformerEventDispatcherInterface $eventDispatcher,
        CurrencyConverterInterface $currencyConverter,
        ObjectClonerInterface $objectCloner,
        CartPriceRuleVoucherRepositoryInterface $voucherCodeRepository
    ) {
        $this->cartItemToSaleItemTransformer = $cartItemToSaleItemTransformer;
        $this->keyTransformer = $keyTransformer;
        $this->numberGenerator = $numberGenerator;
        $this->orderFolderPath = $orderFolderPath;
        $this->objectService = $objectService;
        $this->saleItemFactory = $saleItemFactory;
        $this->eventDispatcher = $eventDispatcher;
        $this->currencyConverter = $currencyConverter;
        $this->objectCloner = $objectCloner;
        $this->voucherCodeRepository = $voucherCodeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function transformSale(ProposalInterface $cart, ProposalInterface $sale, $type)
    {
        /**
         * @var $cart CartInterface
         * @var $sale SaleInterface
         */
        Assert::isInstanceOf($cart, CartInterface::class);
        Assert::isInstanceOf($sale, SaleInterface::class);

        $fromCurrency = $cart->getStore()->getCurrency();
        $toCurrency = $cart->getCurrency() instanceof CurrencyInterface ? $cart->getCurrency() : $fromCurrency;

        $fromCurrencyCode = $fromCurrency->getIsoCode();
        $toCurrencyCode = $toCurrency->getIsoCode();

        $this->eventDispatcher->dispatchPreEvent($type, $sale, ['cart' => $cart]);

        $orderFolder = $this->objectService->createFolderByPath(sprintf('%s/%s', $this->orderFolderPath, date('Y/m/d')));

        /**
         * @var $sale SaleInterface
         */
        $sale->setBaseCurrency($fromCurrency);
        $sale->setCurrency($toCurrency);
        $sale->setPublished(true);
        $sale->setParent($orderFolder);
        $sale->setCustomer($cart->getCustomer());
        $sale->setLocaleCode($cart->getLocaleCode());
        $sale->setSaleDate(Carbon::now());
        $sale->setStore($cart->getStore());

        $sale->setTotal($this->currencyConverter->convert($cart->getTotal(true), $fromCurrencyCode, $toCurrencyCode), true);
        $sale->setTotal($this->currencyConverter->convert($cart->getTotal(false), $fromCurrencyCode, $toCurrencyCode), false);
        $sale->setSubtotal($this->currencyConverter->convert($cart->getSubtotal(true), $fromCurrencyCode, $toCurrencyCode), true);
        $sale->setSubtotal($this->currencyConverter->convert($cart->getSubtotal(false), $fromCurrencyCode, $toCurrencyCode), false);

        $sale->setBaseTotal($cart->getTotal(true), true);
        $sale->setBaseTotal($cart->getTotal(false), false);
        $sale->setBaseSubtotal($cart->getSubtotal(true), true);
        $sale->setBaseSubtotal($cart->getSubtotal(false), false);

        foreach ($cart->getAdjustments() as $adjustment) {
            $sale->addAdjustment($adjustment);

            $baseAdjustment = clone $adjustment;
            $baseAdjustmentGross = $this->currencyConverter->convert($baseAdjustment->getAmount(true), $fromCurrencyCode, $toCurrencyCode);
            $baseAdjustmentNet = $this->currencyConverter->convert($baseAdjustment->getAmount(false), $fromCurrencyCode, $toCurrencyCode);

            $baseAdjustment->setAmount($baseAdjustmentGross, $baseAdjustmentNet);

            $sale->addBaseAdjustment($baseAdjustment);
        }

        $sale->setWeight($cart->getWeight());

        if ($cart->getPriceRuleItems() instanceof Fieldcollection) {
            foreach ($cart->getPriceRuleItems() as $priceRule) {
                if ($priceRule instanceof ProposalCartPriceRuleItemInterface) {
                    $sale->addPriceRule($priceRule);
                }
            }
        }

        $saleNumber = $this->numberGenerator->generate($sale);

        $sale->setKey($this->keyTransformer->transform($saleNumber));
        $sale->setSaleNumber($saleNumber);

        /*
         * We need to save the sale twice in order to create the object in the tree for pimcore
         */
        VersionHelper::useVersioning(function () use ($sale) {
            $sale->save();
        }, false);

        //TODO: hasShippableItems doesn't exist in this Component -> it only exists in Core
        //But leave this here now for BC reasons
        $shippingAddress = $this->objectCloner->cloneObject(
            method_exists($cart, 'hasShippableItems') && $cart->hasShippableItems() === false ? $cart->getInvoiceAddress() : $cart->getShippingAddress(),
            $this->objectService->createFolderByPath(sprintf('%s/addresses', $sale->getFullPath())),
            'shipping'
        );
        $invoiceAddress = $this->objectCloner->cloneObject(
            $cart->getInvoiceAddress(),
            $this->objectService->createFolderByPath(sprintf('%s/addresses', $sale->getFullPath())),
            'invoice'
        );

        VersionHelper::useVersioning(function () use ($shippingAddress, $invoiceAddress) {
            $shippingAddress->save();
            $invoiceAddress->save();
        }, false);

        $sale->setShippingAddress($shippingAddress);
        $sale->setInvoiceAddress($invoiceAddress);

        /**
         * @var CartItemInterface
         */
        foreach ($cart->getItems() as $cartItem) {
            $saleItem = $this->saleItemFactory->createNew();

            $sale->addItem($this->cartItemToSaleItemTransformer->transform($sale, $cartItem, $saleItem));
        }

        $baseTaxesFieldCollection = new Fieldcollection();
        $baseTaxesFieldCollection->setItems($cart->getTaxes() instanceof Fieldcollection ? $cart->getTaxes()->getItems() : []);

        $taxesFieldCollection = new Fieldcollection();
        $taxesFieldCollection->setItems($cart->getTaxes() instanceof Fieldcollection ? $cart->getTaxes()->getItems() : []);

        foreach ($taxesFieldCollection->getItems() as $item) {
            if ($item instanceof TaxItemInterface) {
                $item->setAmount($this->currencyConverter->convert($item->getAmount(), $fromCurrencyCode, $toCurrencyCode));
            }
        }

        $sale->setTaxes($taxesFieldCollection);
        $sale->setBaseTaxes($baseTaxesFieldCollection);

        $this->eventDispatcher->dispatchPostEvent($type, $sale, ['cart' => $cart]);

        VersionHelper::useVersioning(function () use ($sale) {
            $sale->save();
        }, false);

        //Necessary?
        //$cart->setQuote($quote);
        //$cart->save();

        return $sale;
    }
}
