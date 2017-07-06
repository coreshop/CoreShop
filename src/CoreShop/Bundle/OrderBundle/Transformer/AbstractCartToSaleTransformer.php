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

use Carbon\Carbon;
use CoreShop\Bundle\ResourceBundle\Pimcore\ObjectCloner;
use CoreShop\Component\Core\Model\CurrencyInterface;
use CoreShop\Component\Currency\Converter\CurrencyConverterInterface;
use CoreShop\Component\Locale\Context\LocaleContextInterface;
use CoreShop\Component\Order\Cart\Rule\CartPriceRuleOrderProcessorInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartItemInterface;
use CoreShop\Component\Order\Model\ProposalCartPriceRuleItemInterface;
use CoreShop\Component\Order\Model\ProposalInterface;
use CoreShop\Component\Order\Model\SaleInterface;
use CoreShop\Component\Order\NumberGenerator\NumberGeneratorInterface;
use CoreShop\Component\Order\Taxation\ProposalTaxCollectorInterface;
use CoreShop\Component\Order\Transformer\ProposalItemTransformerInterface;
use CoreShop\Component\Order\Transformer\ProposalTransformerInterface;
use CoreShop\Component\Resource\Factory\PimcoreFactoryInterface;
use CoreShop\Component\Resource\Pimcore\ObjectServiceInterface;
use CoreShop\Component\Resource\Transformer\ItemKeyTransformerInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;
use CoreShop\Component\Taxation\Model\TaxItemInterface;
use Pimcore\Model\Object\Fieldcollection;
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
     * @var LocaleContextInterface
     */
    protected $localeContext;

    /**
     * @var StoreContextInterface
     */
    protected $storeContext;

    /**
     * @var PimcoreFactoryInterface
     */
    protected $saleItemFactory;

    /**
     * @var CartPriceRuleOrderProcessorInterface
     */
    protected $cartPriceRuleOrderProcessor;

    /**
     * @var TransformerEventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var ProposalTaxCollectorInterface
     */
    private $cartTaxCollector;

    /**
     * @var CurrencyConverterInterface
     */
    protected $currencyConverter;

    /**
     * @var ObjectCloner
     */
    protected $objectCloner;

    /**
     * @param ProposalItemTransformerInterface $cartItemToSaleItemTransformer
     * @param ItemKeyTransformerInterface $keyTransformer
     * @param NumberGeneratorInterface $numberGenerator
     * @param string $orderFolderPath
     * @param ObjectServiceInterface $objectService
     * @param LocaleContextInterface $localeContext
     * @param PimcoreFactoryInterface $saleItemFactory
     * @param StoreContextInterface $storeContext
     * @param CartPriceRuleOrderProcessorInterface $cartPriceRuleOrderProcessor
     * @param TransformerEventDispatcherInterface $eventDispatcher
     * @param ProposalTaxCollectorInterface $cartTaxCollector
     * @param CurrencyConverterInterface $currencyConverter
     * @param ObjectCloner $objectCloner
     */
    public function __construct(
        ProposalItemTransformerInterface $cartItemToSaleItemTransformer,
        ItemKeyTransformerInterface $keyTransformer,
        NumberGeneratorInterface $numberGenerator,
        $orderFolderPath,
        ObjectServiceInterface $objectService,
        LocaleContextInterface $localeContext,
        PimcoreFactoryInterface $saleItemFactory,
        StoreContextInterface $storeContext,
        CartPriceRuleOrderProcessorInterface $cartPriceRuleOrderProcessor,
        TransformerEventDispatcherInterface $eventDispatcher,
        ProposalTaxCollectorInterface $cartTaxCollector,
        CurrencyConverterInterface $currencyConverter,
        ObjectCloner $objectCloner
    )
    {
        $this->cartItemToSaleItemTransformer = $cartItemToSaleItemTransformer;
        $this->keyTransformer = $keyTransformer;
        $this->numberGenerator = $numberGenerator;
        $this->orderFolderPath = $orderFolderPath;
        $this->objectService = $objectService;
        $this->localeContext = $localeContext;
        $this->saleItemFactory = $saleItemFactory;
        $this->storeContext = $storeContext;
        $this->cartPriceRuleOrderProcessor = $cartPriceRuleOrderProcessor;
        $this->eventDispatcher = $eventDispatcher;
        $this->cartTaxCollector = $cartTaxCollector;
        $this->currencyConverter = $currencyConverter;
        $this->objectCloner = $objectCloner;
    }

    /**
     * {@inheritdoc}
     */
    public function transformSale(ProposalInterface $cart, ProposalInterface $sale, $type)
    {
        /**
         * @var $cart CartInterface
         */
        Assert::isInstanceOf($cart, CartInterface::class);
        Assert::isInstanceOf($sale, SaleInterface::class);

        $fromCurrency = $this->storeContext->getStore()->getCurrency();
        $toCurrency = $cart->getCurrency() instanceof CurrencyInterface ? $cart->getCurrency() : $fromCurrency;

        $fromCurrencyCode = $fromCurrency->getIsoCode();
        $toCurrencyCode = $toCurrency->getIsoCode();

        $this->eventDispatcher->dispatchPreEvent($type, $sale, ['cart' => $cart]);

        $orderFolder = $this->objectService->createFolderByPath(sprintf('%s/%s', $this->orderFolderPath, date('Y/m/d')));
        $saleNumber = $this->numberGenerator->generate($sale);
        /**
         * @var $sale SaleInterface
         */
        $sale->setBaseCurrency($fromCurrency);
        $sale->setCurrency($toCurrency);
        $sale->setKey($this->keyTransformer->transform($saleNumber));
        $sale->setSaleNumber($saleNumber);
        $sale->setParent($orderFolder);
        $sale->setPublished(true);
        $sale->setCustomer($cart->getCustomer());
        $sale->setSaleLanguage($this->localeContext->getLocaleCode());
        $sale->setSaleDate(Carbon::now());
        $sale->setStore($this->storeContext->getStore());

        $sale->setTotal($this->currencyConverter->convert($cart->getTotal(true), $fromCurrencyCode, $toCurrencyCode), true);
        $sale->setTotal($this->currencyConverter->convert($cart->getTotal(false), $fromCurrencyCode, $toCurrencyCode), false);
        $sale->setTotalTax($this->currencyConverter->convert($cart->getTotalTax(), $fromCurrencyCode, $toCurrencyCode));
        $sale->setSubtotal($this->currencyConverter->convert($cart->getSubtotal(true), $fromCurrencyCode, $toCurrencyCode), true);
        $sale->setSubtotal($this->currencyConverter->convert($cart->getSubtotal(false), $fromCurrencyCode, $toCurrencyCode), false);
        $sale->setSubtotalTax($this->currencyConverter->convert($cart->getSubtotalTax(), $fromCurrencyCode, $toCurrencyCode));
        $sale->setDiscount($this->currencyConverter->convert($cart->getDiscount(true), $fromCurrencyCode, $toCurrencyCode), true);
        $sale->setDiscount($this->currencyConverter->convert($cart->getDiscount(false), $fromCurrencyCode, $toCurrencyCode), false);

        $sale->setBaseTotal($cart->getTotal(true), true);
        $sale->setBaseTotal($cart->getTotal(false), false);
        $sale->setBaseTotalTax($cart->getTotalTax());
        $sale->setBaseSubtotal($cart->getSubtotal(true), true);
        $sale->setBaseSubtotal($cart->getSubtotal(false), false);
        $sale->setBaseSubtotalTax($cart->getSubtotalTax());
        $sale->setBaseDiscount($cart->getDiscount(true), true);
        $sale->setBaseDiscount($cart->getDiscount(false), false);

        $sale->setWeight($cart->getWeight());

        if ($cart->getPriceRuleItems() instanceof Fieldcollection) {
            foreach ($cart->getPriceRuleItems() as $priceRule) {
                if ($priceRule instanceof ProposalCartPriceRuleItemInterface) {
                    $this->cartPriceRuleOrderProcessor->process($priceRule->getCartPriceRule(), $priceRule->getVoucherCode(), $cart, $sale);
                }
            }
        }

        /*
         * We need to save the sale twice in order to create the object in the tree for pimcore
         */
        $sale->save();

        $shippingAddress = $this->objectCloner->cloneObject(
            $cart->getShippingAddress(),
            $this->objectService->createFolderByPath(sprintf("%s/addresses", $sale->getFullPath())),
            "shipping"
        );
        $invoiceAddress = $this->objectCloner->cloneObject(
            $cart->getInvoiceAddress(),
            $this->objectService->createFolderByPath(sprintf("%s/addresses", $sale->getFullPath())),
            "invoice"
        );

        $shippingAddress->save();
        $invoiceAddress->save();

        $sale->setShippingAddress($shippingAddress);
        $sale->setInvoiceAddress($invoiceAddress);

        /**
         * @var CartItemInterface
         */
        foreach ($cart->getItems() as $cartItem) {
            $saleItem = $this->saleItemFactory->createNew();

            $sale->addItem($this->cartItemToSaleItemTransformer->transform($sale, $cartItem, $saleItem));
        }

        /*$baseTaxesFieldCollection = new Fieldcollection();
        $baseTaxesFieldCollection->setItems($this->cartTaxCollector->getTaxes($cart));

        $taxesFieldCollection = new Fieldcollection();
        $taxesFieldCollection->setItems($this->cartTaxCollector->getTaxes($cart));

        foreach ($taxesFieldCollection->getItems() as $item) {
            if ($item instanceof TaxItemInterface) {
                $item->setAmount($this->currencyConverter->convert($item->getAmount(), $fromCurrencyCode, $toCurrencyCode));
            }
        }

        $sale->setTaxes($taxesFieldCollection);
        $sale->setBaseTaxes($baseTaxesFieldCollection);*/

        $this->eventDispatcher->dispatchPostEvent($type, $sale, ['cart' => $cart]);

        $sale->save();

        //Necessary?
        //$cart->setQuote($quote);
        //$cart->save();

        return $sale;
    }
}
