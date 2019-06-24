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

namespace CoreShop\Bundle\OrderBundle\Controller;

use Carbon\Carbon;
use CoreShop\Component\Address\Formatter\AddressFormatterInterface;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Address\Model\CountryInterface;
use CoreShop\Component\Core\Model\CartItemInterface;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Model\ProposalCartPriceRuleItemInterface;
use CoreShop\Component\Store\Model\StoreInterface;
use CoreShop\Component\Taxation\Model\TaxItemInterface;
use Pimcore\Bundle\AdminBundle\Helper\GridHelperService;
use Pimcore\Bundle\AdminBundle\Helper\QueryParams;
use Pimcore\Model\DataObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CartController extends AbstractSaleController
{
    /**
     * @return mixed
     *
     * @throws \Exception
     */
    public function getFolderConfigurationAction()
    {
        $this->isGrantedOr403();

        $name = null;
        $folderId = null;

        $cartClassId = $this->getParameter('coreshop.model.cart.pimcore_class_name');
        $folderPath = $this->getParameter('coreshop.folder.cart');
        $orderClassDefinition = DataObject\ClassDefinition::getByName($cartClassId);

        $folder = DataObject::getByPath('/' . $folderPath);

        if ($folder instanceof DataObject\Folder) {
            $folderId = $folder->getId();
        }

        if ($orderClassDefinition instanceof DataObject\ClassDefinition) {
            $name = $orderClassDefinition->getName();
        }

        return $this->viewHandler->handle(['success' => true, 'className' => $name, 'folderId' => $folderId]);
    }


    /**
     * @param Request $request
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function listAction(Request $request)
    {
        $this->isGrantedOr403();

        $list = $this->getList();
        $list->setLimit($request->get('limit', 30));
        $list->setOffset($request->get('page', 1) - 1);

        if ($request->get('filter', null)) {
            $gridHelper = new GridHelperService();

            $conditionFilters = [];
            $conditionFilters[] = $gridHelper->getFilterCondition($request->get('filter'), DataObject\ClassDefinition::getByName($this->getParameter($this->getSaleClassName())));
            if (count($conditionFilters) > 0 && $conditionFilters[0] !== '(())') {
                $list->setCondition(implode(' AND ', $conditionFilters));
            }
        }

        $sortingSettings = QueryParams::extractSortingSettings($request->request->all());

        $order = 'DESC';
        $orderKey = $this->getOrderKey();

        if ($sortingSettings['order']) {
            $order = $sortingSettings['order'];
        }
        if (strlen($sortingSettings['orderKey']) > 0) {
            $orderKey = $sortingSettings['orderKey'];
        }

        $list->setOrder($order);
        $list->setOrderKey($orderKey);

        $carts = $list->load();
        $jsonSales = [];

        foreach ($carts as $cart) {
            $jsonSales[] = $this->prepareSale($cart);
        }

        return $this->viewHandler->handle(['success' => true, 'data' => $jsonSales, 'count' => count($jsonSales), 'total' => $list->getTotalCount()]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function detailAction(Request $request)
    {
        $this->isGrantedOr403();

        $cartId = $request->get('id');
        $cart = $this->getRepository()->find($cartId);

        if (null === $cart) {
            return $this->viewHandler->handle(['success' => false, 'message' => "Cart with ID '$cartId' not found"]);
        }

        $jsonSale = $this->getDetails($cart);

        return $this->viewHandler->handle(['success' => true, 'sale' => $jsonSale]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function findSaleAction(Request $request)
    {
        $this->isGrantedOr403();

        $number = $request->get('number');

        if ($number) {
            $list = $this->getList();
            $list->setCondition(sprintf('%s = ? OR o_id = ?', $this->getSaleNumberField()), [$number, $number]);

            $carts = $list->load();

            if (count($carts) > 0) {
                return $this->viewHandler->handle(['success' => true, 'id' => $carts[0]->getId()]);
            }
        }

        return $this->viewHandler->handle(['success' => false]);
    }

    /**
     * @param CartInterface $cart
     *
     * @return array
     *
     * @throws \Exception
     */
    protected function prepareSale(CartInterface $cart)
    {
        $date = (int) $cart->getCreationDate();

        if ($date instanceof Carbon) {
            $date = $date->getTimestamp();
        }

        $element = [
            'o_id' => $cart->getId(),
            'saleDate' => $date,
            'lang' => $cart->getLocaleCode(),
            'discount' => $cart->getDiscount(),
            'subtotal' => $cart->getSubtotal(),
            'totalTax' => $cart->getTotalTax(),
            'total' => $cart->getTotal(),
            'currency' => $this->getCurrency($cart->getCurrency() ?: $cart->getStore()->getCurrency()),
            'currencyName' => $cart->getCurrency() instanceof CurrencyInterface ? $cart->getCurrency()->getName() : '',
            'customerName' => $cart->getCustomer() instanceof CustomerInterface ? $cart->getCustomer()->getFirstname() . ' ' . $cart->getCustomer()->getLastname() : '',
            'customerEmail' => $cart->getCustomer() instanceof CustomerInterface ? $cart->getCustomer()->getEmail() : '',
            'store' => $cart->getStore() instanceof StoreInterface ? $cart->getStore()->getId() : null,
        ];

        $element = array_merge($element, $this->prepareAddress($cart->getShippingAddress(), 'shipping'), $this->prepareAddress($cart->getInvoiceAddress(), 'invoice'));

        return $element;
    }

    /**
     * @param string $address
     * @param string $type
     *
     * @return array
     *
     * @throws \Exception
     */
    protected function prepareAddress($address, $type)
    {
        $prefix = 'address' . ucfirst($type);
        $values = [];
        $fullAddress = [];
        $classDefinition = DataObject\ClassDefinition::getByName($this->getParameter('coreshop.model.address.pimcore_class_name'));

        foreach ($classDefinition->getFieldDefinitions() as $fieldDefinition) {
            $value = '';

            if ($address instanceof AddressInterface && $address instanceof DataObject\Concrete) {
                $getter = 'get' . ucfirst($fieldDefinition->getName());

                if (method_exists($address, $getter)) {
                    $value = $address->$getter();

                    if (method_exists($value, 'getName')) {
                        $value = $value->getName();
                    }

                    $fullAddress[] = $value;
                }
            }

            $values[$prefix . ucfirst($fieldDefinition->getName())] = $value;
        }

        if ($address instanceof AddressInterface && $address->getCountry() instanceof CountryInterface) {
            $values[$prefix . 'All'] = $this->getAddressFormatter()->formatAddress($address, false);
        }

        return $values;
    }

    /**
     * @param CartInterface $cart
     *
     * @return array
     */
    protected function getDetails(CartInterface $cart)
    {
        $jsonSale = $this->getDataForObject($cart);

        if ($jsonSale['items'] === null) {
            $jsonSale['items'] = [];
        }

        $jsonSale['o_id'] = $cart->getId();
        $jsonSale['saleDate'] = $cart->getCreationDate();
        $jsonSale['customer'] = $cart->getCustomer() instanceof CustomerInterface ? $this->getDataForObject($cart->getCustomer()) : null;
        $jsonSale['details'] = $this->getItemDetails($cart);
        $jsonSale['currency'] = $this->getCurrency($cart->getCurrency() ?: $cart->getStore()->getCurrency());
        $jsonSale['store'] = $cart->getStore() instanceof StoreInterface ? $this->getStore($cart->getStore()) : null;
        $jsonSale['totalGross'] = $cart->getTotal();
        $jsonSale['edit'] = true;

        $jsonSale['address'] = [
            'shipping' => $this->getDataForObject($cart->getShippingAddress()),
            'billing' => $this->getDataForObject($cart->getInvoiceAddress()),
        ];

        if ($cart->getShippingAddress() instanceof AddressInterface && $cart->getShippingAddress()->getCountry() instanceof CountryInterface) {
            $jsonSale['address']['shipping']['formatted'] = $this->getAddressFormatter()->formatAddress($cart->getShippingAddress());
        } else {
            $jsonSale['address']['shipping']['formatted'] = '';
        }

        if ($cart->getInvoiceAddress() instanceof AddressInterface && $cart->getInvoiceAddress()->getCountry() instanceof CountryInterface) {
            $jsonSale['address']['billing']['formatted'] = $this->getAddressFormatter()->formatAddress($cart->getInvoiceAddress());
        } else {
            $jsonSale['address']['billing']['formatted'] = '';
        }

        $jsonSale['priceRule'] = false;

        if ($cart->getPriceRuleItems() instanceof DataObject\Fieldcollection) {
            $rules = [];

            foreach ($cart->getPriceRuleItems()->getItems() as $ruleItem) {
                if ($ruleItem instanceof ProposalCartPriceRuleItemInterface) {
                    $rule = $ruleItem->getCartPriceRule();

                    if ($rule instanceof CartPriceRuleInterface) {
                        $rules[] = [
                            'id' => $rule->getId(),
                            'name' => $rule->getName(),
                            'code' => $ruleItem->getVoucherCode(),
                            'discount' => $ruleItem->getDiscount(),
                        ];
                    }
                }
            }

            $jsonSale['priceRule'] = $rules;
        }

        $totals = $this->getSummary($cart);

        foreach ($totals as &$totalEntry) {
            $priceFormatted = $this->get('coreshop.money_formatter')->format(
                $totalEntry['value'],
                $cart->getCurrency()->getIsoCode()
            );

            $totalEntry['valueFormatted'] = $priceFormatted;
        }

        unset ($totalEntry);

        $jsonSale['summary'] = $totals;

        return $jsonSale;
    }

    /**
     * @param CartInterface $cart
     *
     * @return array
     */
    protected function getSummary(CartInterface $cart)
    {
        $summary = [];

        if ($cart->getDiscount() > 0) {
            $summary[] = [
                'key' => 'discount',
                'value' => $cart->getDiscount(),
            ];
        }

        $taxes = $cart->getTaxes();

        if (is_array($taxes)) {
            foreach ($taxes as $tax) {
                if ($tax instanceof TaxItemInterface) {
                    $summary[] = [
                        'key' => 'tax_' . $tax->getName(),
                        'text' => sprintf('Tax (%s - %s)', $tax->getName(), $tax->getRate()),
                        'value' => $tax->getAmount(),
                    ];
                }
            }
        }

        $summary[] = [
            'key' => 'total_tax',
            'value' => $cart->getTotalTax(),
        ];
        $summary[] = [
            'key' => 'total',
            'value' => $cart->getTotal(),
        ];

        return $summary;
    }

    /**
     * @param CartInterface $cart
     *
     * @return array
     */
    protected function getItemDetails(CartInterface $cart)
    {
        $details = $cart->getItems();
        $items = [];

        foreach ($details as $detail) {
            if ($detail instanceof CartItemInterface) {
                $items[] = $this->prepareCartItem($detail);
            }
        }

        return $items;
    }

    /**
     * @param CartItemInterface $item
     *
     * @return array<string,integer|null|string>
     */
    protected function prepareCartItem(CartItemInterface $item)
    {
        return [
            'o_id' => $item->getId(),
            'product_name' => $item->getProduct() ? $item->getProduct()->getName() : '',
            'quantity' => $item->getQuantity(),
            'totalGrossFormatted' => $this->get('coreshop.money_formatter')->format($item->getTotal(true), $item->getCart()->getCurrency()->getIsoCode()),
            'totalNetFormatted' => $this->get('coreshop.money_formatter')->format($item->getTotal(false), $item->getCart()->getCurrency()->getIsoCode()),
            'itemPriceGrossFormatted' => $this->get('coreshop.money_formatter')->format($item->getItemPrice(true), $item->getCart()->getCurrency()->getIsoCode()),
            'itemPriceNetFormatted' => $this->get('coreshop.money_formatter')->format($item->getItemPrice(false), $item->getCart()->getCurrency()->getIsoCode()),
        ];
    }

    /**
     * @param CurrencyInterface $currency
     *
     * @return array
     */
    protected function getCurrency(CurrencyInterface $currency)
    {
        return [
            'name' => $currency->getName(),
            'symbol' => $currency->getSymbol(),
        ];
    }

    /**
     * @param StoreInterface $store
     *
     * @return array<string,integer|string>
     */
    protected function getStore(StoreInterface $store)
    {
        return [
            'id' => $store->getId(),
            'name' => $store->getName(),
        ];
    }

    /**
     * @return AddressFormatterInterface
     */
    private function getAddressFormatter()
    {
        return $this->get('coreshop.address.formatter');
    }

    /**
     * {@inheritdoc}
     */
    protected function getRepository()
    {
        return $this->get('coreshop.repository.cart');
    }

    /**
     * {@inheritdoc}
     */
    protected function getList()
    {
        return $this->getRepository()->getList();
    }

    /**
     * {@inheritdoc}
     */
    protected function getSaleClassName()
    {
        return 'coreshop.model.cart.pimcore_class_name';
    }

    /**
     * {@inheritdoc}
     */
    protected function getOrderKey()
    {
        return 'id';
    }

    /**
     * {@inheritdoc}
     */
    protected function getSaleNumberField()
    {
        return 'id';
    }
}
