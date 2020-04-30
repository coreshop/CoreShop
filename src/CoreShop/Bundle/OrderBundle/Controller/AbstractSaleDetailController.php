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

namespace CoreShop\Bundle\OrderBundle\Controller;

use CoreShop\Bundle\OrderBundle\Events;
use CoreShop\Component\Address\Formatter\AddressFormatterInterface;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Address\Model\CountryInterface;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Model\ProposalCartPriceRuleItemInterface;
use CoreShop\Component\Order\Model\SaleInterface;
use CoreShop\Component\Order\Model\SaleItemInterface;
use CoreShop\Component\Order\Notes;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use CoreShop\Component\Store\Model\StoreInterface;
use CoreShop\Component\Taxation\Model\TaxItemInterface;
use Pimcore\Bundle\AdminBundle\Helper\GridHelperService;
use Pimcore\Bundle\AdminBundle\Helper\QueryParams;
use Pimcore\Model\DataObject;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractSaleDetailController extends AbstractSaleController
{
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

        $list = $this->getSalesList();
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

        $sales = $list->load();
        $jsonSales = [];

        foreach ($sales as $sale) {
            $jsonSales[] = $this->prepareSale($sale);
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

        $saleId = $request->get('id');
        $sale = $this->getSaleRepository()->find($saleId);

        if (!$sale instanceof SaleInterface) {
            return $this->viewHandler->handle(['success' => false, 'message' => "Sale with ID '$saleId' not found"]);
        }

        $jsonSale = $this->getDetails($sale);

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
            $list = $this->getSalesList();
            $list->setCondition(sprintf('%s = ? OR o_id = ?', $this->getSaleNumberField()), [$number, $number]);

            $sales = $list->load();

            if (count($sales) > 0) {
                return $this->viewHandler->handle(['success' => true, 'id' => $sales[0]->getId()]);
            }
        }

        return $this->viewHandler->handle(['success' => false]);
    }

    /**
     * @param SaleInterface $sale
     *
     * @return array
     *
     * @throws \Exception
     */
    protected function prepareSale(SaleInterface $sale)
    {
        $date = (int)$sale->getSaleDate()->getTimestamp();

        $element = [
            'o_id' => $sale->getId(),
            'saleDate' => $date,
            'saleNumber' => $sale->getSaleNumber(),
            'lang' => $sale->getLocaleCode(),
            'discount' => $sale->getDiscount(),
            'subtotal' => $sale->getSubtotal(),
            'shipping' => $sale->getShipping(),
            'totalTax' => $sale->getTotalTax(),
            'total' => $sale->getTotal(),
            'currency' => $this->getCurrency($sale->getCurrency() ?: $sale->getStore()->getCurrency()),
            'currencyName' => $sale->getCurrency() instanceof CurrencyInterface ? $sale->getCurrency()->getName() : '',
            'customerName' => $sale->getCustomer() instanceof CustomerInterface ? $sale->getCustomer()->getFirstname() . ' ' . $sale->getCustomer()->getLastname() : '',
            'customerEmail' => $sale->getCustomer() instanceof CustomerInterface ? $sale->getCustomer()->getEmail() : '',
            'store' => $sale->getStore() instanceof StoreInterface ? $sale->getStore()->getId() : null,
        ];

        $element = array_merge($element, $this->prepareAddress($sale->getShippingAddress(), 'shipping'), $this->prepareAddress($sale->getInvoiceAddress(), 'invoice'));

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
     * @param SaleInterface $sale
     *
     * @return array
     */
    protected function getDetails(SaleInterface $sale)
    {
        if ($this->useLegacySerialization()) {
            $jsonSale = $this->getDataForObject($sale);
            $jsonSale['o_id'] = $sale->getId();
            $jsonSale['saleNumber'] = $sale->getSaleNumber();
            $jsonSale['saleDate'] = $sale->getSaleDate()->getTimestamp();
            $jsonSale['customer'] = $sale->getCustomer() instanceof CustomerInterface ? $this->getDataForObject($sale->getCustomer()) : null;
            $jsonSale['currency'] = $this->getCurrency($sale->getCurrency() ?: $sale->getStore()->getCurrency());
            $jsonSale['store'] = $sale->getStore() instanceof StoreInterface ? $this->getStore($sale->getStore()) : null;
        } else {
            $jsonSale = $this->getSerializer()->toArray($sale);
        }

        if ($jsonSale['items'] === null) {
            $jsonSale['items'] = [];
        }

        $jsonSale['details'] = $this->getItemDetails($sale);
        $jsonSale['summary'] = $this->getSummary($sale);
        $jsonSale['mailCorrespondence'] = $this->getMailCorrespondence($sale);

        $jsonSale['address'] = [
            'shipping' => $this->getDataForObject($sale->getShippingAddress()),
            'billing' => $this->getDataForObject($sale->getInvoiceAddress()),
        ];

        if ($sale->getShippingAddress() instanceof AddressInterface && $sale->getShippingAddress()->getCountry() instanceof CountryInterface) {
            $jsonSale['address']['shipping']['formatted'] = $this->getAddressFormatter()->formatAddress($sale->getShippingAddress());
        } else {
            $jsonSale['address']['shipping']['formatted'] = '';
        }

        if ($sale->getInvoiceAddress() instanceof AddressInterface && $sale->getInvoiceAddress()->getCountry() instanceof CountryInterface) {
            $jsonSale['address']['billing']['formatted'] = $this->getAddressFormatter()->formatAddress($sale->getInvoiceAddress());
        } else {
            $jsonSale['address']['billing']['formatted'] = '';
        }

        $jsonSale['priceRule'] = false;

        if ($sale->getPriceRuleItems() instanceof DataObject\Fieldcollection) {
            $rules = [];

            foreach ($sale->getPriceRuleItems()->getItems() as $ruleItem) {
                if ($ruleItem instanceof ProposalCartPriceRuleItemInterface) {
                    $rule = $ruleItem->getCartPriceRule();

                    $ruleData = [
                        'id' => -1,
                        'name' => '--',
                        'code' => empty($ruleItem->getVoucherCode()) ? null : $ruleItem->getVoucherCode(),
                        'discount' => $ruleItem->getDiscount(),
                        'discountNet' => $ruleItem->getDiscount(false),
                    ];

                    if ($rule instanceof CartPriceRuleInterface) {
                        $ruleData['id'] = $rule->getId();
                        $ruleData['name'] = $rule->getName();
                    }

                    $rules[] = $ruleData;
                }
            }

            $jsonSale['priceRule'] = $rules;
        }

        $event = new GenericEvent($sale, $jsonSale);

        $this->get('event_dispatcher')->dispatch(Events::SALE_DETAIL_PREPARE, $event);

        $jsonSale = $event->getArguments();

        return $jsonSale;
    }

    /**
     * @param SaleInterface $sale
     *
     * @return array
     */
    protected function getMailCorrespondence(SaleInterface $sale)
    {
        $list = [];
        $objectNoteService = $this->get('coreshop.object_note_service');
        $notes = $objectNoteService->getObjectNotes($sale, Notes::NOTE_EMAIL);

        foreach ($notes as $note) {
            $noteElement = [
                'date' => $note->date,
                'description' => $note->description,
            ];

            foreach ($note->data as $key => $noteData) {
                $noteElement[$key] = $noteData['data'];
            }

            $list[] = $noteElement;
        }

        return $list;
    }

    /**
     * @param SaleInterface $sale
     *
     * @return array
     */
    protected function getSummary(SaleInterface $sale)
    {
        $summary = [];

        if ($sale->getDiscount() != 0) {
            $summary[] = [
                'key' => $sale->getDiscount() < 0 ? 'discount' : 'surcharge',
                'value' => $sale->getDiscount(),
                'valueNet' => $sale->getDiscount(false),
            ];
        }

        if ($sale->getShipping() > 0) {
            $summary[] = [
                'key' => 'shipping',
                'value' => $sale->getShipping(),
                'valueNet' => $sale->getShipping(false),
            ];

            $summary[] = [
                'key' => 'shipping_tax',
                'value' => $sale->getShippingTax(),
            ];
        }

        $taxes = $sale->getTaxes();

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
            'value' => $sale->getTotalTax(),
        ];
        $summary[] = [
            'key' => 'total',
            'value' => $sale->getTotal(),
        ];
        $summary[] = [
            'key' => 'payment_total',
            'value' => $sale->getPaymentTotal(),
            'precision' => 2,
            'factor' => 100
        ];

        return $summary;
    }

    /**
     * @param SaleInterface $sale
     *
     * @return array
     */
    protected function getItemDetails(SaleInterface $sale)
    {
        $details = $sale->getItems();
        $items = [];

        foreach ($details as $detail) {
            if ($detail instanceof SaleItemInterface) {
                $items[] = $this->prepareSaleItem($detail);
            }
        }

        return $items;
    }

    /**
     * @param SaleItemInterface $item
     *
     * @return array
     */
    protected function prepareSaleItem(SaleItemInterface $item)
    {
        return [
            'o_id' => $item->getId(),
            'product_name' => $item->getName(),
            'product_image' => null, //TODO: ($detail->getProductImage() instanceof \Pimcore\Model\Asset\Image) ? $detail->getProductImage()->getPath() : null,
            'wholesale_price' => $item->getItemWholesalePrice(),
            'price_without_tax' => $item->getItemPrice(false),
            'price' => $item->getItemPrice(true),
            'quantity' => $item->getQuantity(),
            'total' => $item->getTotal(),
            'total_tax' => $item->getTotalTax(),
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
            'iso' => $currency->getIsoCode()
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
     * @return PimcoreRepositoryInterface
     */
    abstract protected function getSaleRepository();

    /**
     * @return \Pimcore\Model\DataObject\Listing
     */
    abstract protected function getSalesList();

    /**
     * @return string
     */
    abstract protected function getSaleClassName();

    /**
     * @return array
     */
    abstract protected function getFolderConfigurationAction();

    /**
     * @return string
     */
    abstract protected function getOrderKey();

    /**
     * @return string
     */
    abstract protected function getSaleNumberField();

    /**
     * @return AddressFormatterInterface
     */
    protected function getAddressFormatter()
    {
        return $this->get('coreshop.address.formatter');
    }

    /**
     * @return \JMS\Serializer\Serializer
     */
    protected function getSerializer()
    {
        return $this->get('jms_serializer');
    }

    /**
     * @return bool
     */
    protected function useLegacySerialization()
    {
        return $this->getParameter('coreshop.order.legacy_serialization') === true;
    }
}
