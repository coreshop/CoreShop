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
use CoreShop\Component\Locale\Context\LocaleContextInterface;
use CoreShop\Component\Order\Model\QuoteInterface;
use CoreShop\Component\Order\Model\SaleInterface;
use CoreShop\Component\Order\Taxation\ProposalTaxCollectorInterface;
use CoreShop\Component\Resource\Pimcore\ObjectServiceInterface;
use CoreShop\Component\Currency\Context\CurrencyContextInterface;
use CoreShop\Component\Order\Cart\Rule\CartPriceRuleOrderProcessorInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartItemInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\ProposalCartPriceRuleItemInterface;
use CoreShop\Component\Order\Model\ProposalInterface;
use CoreShop\Component\Order\NumberGenerator\NumberGeneratorInterface;
use CoreShop\Component\Order\Transformer\ProposalItemTransformerInterface;
use CoreShop\Component\Order\Transformer\ProposalTransformerInterface;
use CoreShop\Component\Resource\Factory\PimcoreFactoryInterface;
use CoreShop\Component\Resource\Transformer\ItemKeyTransformerInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;
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
     * @var CurrencyContextInterface
     */
    protected $currencyContext;

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
     * @param ProposalItemTransformerInterface $cartItemToSaleItemTransformer
     * @param ItemKeyTransformerInterface $keyTransformer
     * @param NumberGeneratorInterface $numberGenerator
     * @param string $orderFolderPath
     * @param ObjectServiceInterface $objectService
     * @param LocaleContextInterface $localeContext
     * @param PimcoreFactoryInterface $saleItemFactory
     * @param CurrencyContextInterface $currencyContext
     * @param StoreContextInterface $storeContext
     * @param CartPriceRuleOrderProcessorInterface $cartPriceRuleOrderProcessor
     * @param TransformerEventDispatcherInterface $eventDispatcher
     * @param ProposalTaxCollectorInterface $cartTaxCollector
     */
    public function __construct(
        ProposalItemTransformerInterface $cartItemToSaleItemTransformer,
        ItemKeyTransformerInterface $keyTransformer,
        NumberGeneratorInterface $numberGenerator,
        $orderFolderPath,
        ObjectServiceInterface $objectService,
        LocaleContextInterface $localeContext,
        PimcoreFactoryInterface $saleItemFactory,
        CurrencyContextInterface $currencyContext,
        StoreContextInterface $storeContext,
        CartPriceRuleOrderProcessorInterface $cartPriceRuleOrderProcessor,
        TransformerEventDispatcherInterface $eventDispatcher,
        ProposalTaxCollectorInterface $cartTaxCollector
    )
    {
        $this->cartItemToSaleItemTransformer = $cartItemToSaleItemTransformer;
        $this->keyTransformer = $keyTransformer;
        $this->numberGenerator = $numberGenerator;
        $this->orderFolderPath = $orderFolderPath;
        $this->objectService = $objectService;
        $this->localeContext = $localeContext;
        $this->saleItemFactory = $saleItemFactory;
        $this->currencyContext = $currencyContext;
        $this->storeContext = $storeContext;
        $this->cartPriceRuleOrderProcessor = $cartPriceRuleOrderProcessor;
        $this->eventDispatcher = $eventDispatcher;
        $this->cartTaxCollector = $cartTaxCollector;
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

        $this->eventDispatcher->dispatchPreEvent($type, $sale, ['cart' => $cart]);

        $orderFolder = $this->objectService->createFolderByPath(sprintf('%s/%s', $this->orderFolderPath, date('Y/m/d')));

        $quoteNumber = $this->numberGenerator->generate($sale);
        /**
         * @var $sale SaleInterface
         */
        $sale->setKey($this->keyTransformer->transform($quoteNumber));
        $sale->setSaleNumber($quoteNumber);
        $sale->setParent($orderFolder);
        $sale->setPublished(true);
        $sale->setCustomer($cart->getCustomer());
        $sale->setSaleLanguage($this->localeContext->getLocaleCode());
        $sale->setSaleDate(Carbon::now());
        $sale->setCurrency($this->currencyContext->getCurrency());
        $sale->setStore($this->storeContext->getStore());
        $sale->setTotal($cart->getTotal(true), true);
        $sale->setTotal($cart->getTotal(false), false);
        $sale->setTotalTax($cart->getTotalTax());
        $sale->setSubtotal($cart->getSubtotal(true), true);
        $sale->setSubtotal($cart->getSubtotal(false), false);
        $sale->setSubtotalTax($cart->getSubtotalTax());
        $sale->setDiscount($cart->getDiscount(true), true);
        $sale->setDiscount($cart->getDiscount(false), false);
        $sale->setShippingAddress($cart->getShippingAddress());
        $sale->setInvoiceAddress($cart->getInvoiceAddress());
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

        /**
         * @var CartItemInterface
         */
        foreach ($cart->getItems() as $cartItem) {
            $saleItem = $this->saleItemFactory->createNew();

            $sale->addItem($this->cartItemToSaleItemTransformer->transform($sale, $cartItem, $saleItem));
        }

        $fieldCollection = new Fieldcollection();
        $fieldCollection->setItems($this->cartTaxCollector->getTaxes($cart));

        $sale->setTaxes($fieldCollection);

        $this->eventDispatcher->dispatchPostEvent($type, $sale, ['cart' => $cart]);

        $sale->save();

        //Necessary?
        //$cart->setQuote($quote);
        //$cart->save();

        return $sale;
    }
}
