<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreShopLegacyBundle\Model\Order;

use Carbon\Carbon;
use CoreShop\Bundle\CoreShopLegacyBundle\Exception;
use CoreShop\Bundle\CoreShopLegacyBundle\Exception\ObjectUnsupportedException;
use CoreShop\Bundle\CoreShopLegacyBundle\Helper\Zend\Action;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\Base;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\Configuration;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\NumberRange;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\Order;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\TaxCalculator;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\User;
use CoreShop\Bundle\CoreShopLegacyBundle\Tool\Wkhtmltopdf;
use Pimcore\Date;
use Pimcore\File;
use Pimcore\Logger;
use Pimcore\Model\Asset;
use Pimcore\Model\Asset\Service;
use Pimcore\Model\Object;

/**
 * Class Document
 * @package CoreShop\Bundle\CoreShopLegacyBundle\Model\Order
 *
 * @method static Object\Listing\Concrete getByOrder($value, $limit = 0)
 * @method static Object\Listing\Concrete getByDocumentDate ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByDocumentNumber ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByLang ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByItems ($value, $limit = 0)
 *
 * @Todo: Does every Document has taxes? Can we somehow refactor this to make it more easier?
 */
abstract class Document extends Base
{
    /**
     * @var string
     */
    public static $documentType = '';

    /**
     * @return string
     */
    public static function getDocumentType()
    {
        return static::$documentType;
    }

    /**
     * Creates next Document Number.
     *
     * @return string
     */
    public static function getNextDocumentNumber()
    {
        $number = NumberRange::getNextNumberForType(static::getDocumentType());

        return self::getValidDocumentNumber($number);
    }

    /**
     * Converts any Number to a valid Document Number with Suffix and Prefix.
     *
     * @param $number
     *
     * @return string
     */
    public static function getValidDocumentNumber($number)
    {
        $prefix = Configuration::get(sprintf('SYSTEM.%s.PREFIX', strtoupper(self::getDocumentType())));
        $suffix = Configuration::get(sprintf('SYSTEM.%s.SUFFIX', strtoupper(self::getDocumentType())));

        if ($prefix) {
            $number = $prefix.$number;
        }

        if ($suffix) {
            $number = $number.$suffix;
        }

        return $number;
    }

    /**
     * Get Doucment by Number
     *
     * @param $documentNumber
     * @return static|null
     */
    public static function findByDocumentNumber($documentNumber)
    {
        $documents = static::getByDocumentNumber($documentNumber);

        if (count($documents->getObjects())) {
            return $documents->getObjects()[0];
        }

        return null;
    }

    /**
     * get folder for Document
     *
     * @param Order $order
     * @param \DateTime $date
     *
     * @return Object\Folder
     */
    public static function getPathForDocuments(Order $order, $date = null)
    {
        if (is_null($date)) {
            $date = new Carbon();
        }

        return Object\Service::createFolderByPath(sprintf("/%s/%s/%s", $order->getFullPath(), static::getDocumentType(), $date->format("Y/m/d")));
    }

    /**
     * get all processable items for order
     * @param Order $order
     * @return array
     */
    public static function getProcessableItems(Order $order)
    {
        $items = $order->getItems();
        $processedItems = static::getProcessedItems($order);
        $processAbleItems = [];

        foreach ($items as $item) {
            if (array_key_exists($item->getId(), $processedItems)) {
                if ($processedItems[$item->getId()]['amount'] < $item->getAmount()) {
                    $processAbleItems[$item->getId()] = [
                        "amount" => $item->getAmount() - $processedItems[$item->getId()]['amount'],
                        "item" => $item
                    ];
                }
            } else {
                $processAbleItems[$item->getId()] = [
                    "amount" => $item->getAmount(),
                    "item" => $item
                ];
            }
        }

        return $processAbleItems;
    }

    /**
     * get all processed items for order
     *
     * @param Order $order
     * @return array
     */
    public static function getProcessedItems(Order $order)
    {
        $documents = $order->getDocumentsForType(static::getDocumentType());
        $processedItems = [];

        foreach ($documents as $document) {
            foreach ($document->getItems() as $processedItem) {
                $orderItem = $processedItem->getOrderItem();

                if ($orderItem instanceof Item) {
                    if (array_key_exists($orderItem->getId(), $processedItems)) {
                        $processedItems[$orderItem->getId()]['amount'] += $processedItem->getAmount();
                    } else {
                        $processedItems[$orderItem->getId()] = [
                            'amount' => $processedItem->getAmount(),
                            'orderItem' => $orderItem
                        ];
                    }
                }
            }
        }

        return $processedItems;
    }

    /**
     * Check if items are available for invoicing
     *
     * @param $items
     * @return bool
     */
    public static function checkItemsAreProcessable($items)
    {
        if (!is_array($items)) {
            return false;
        }

        foreach ($items as $item) {
            $orderItem = Item::getById($item['orderItemId']);
            $amount = $item['amount'];

            if ($orderItem instanceof Item) {
                $processedAmount = $orderItem->getProcessedAmountForType(static::getDocumentType());
                $newProcessedAmount = $processedAmount + $amount;

                if ($newProcessedAmount > $orderItem->getAmount()) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param Order $order
     * @return bool
     */
    public static function isFullyProcessed(Order $order)
    {
        return count(static::getProcessableItems($order)) === 0;
    }

    /**
     * @param string $field
     *
     * @return float
     */
    public function getProcessedValue($field)
    {
        $documents = $this->getOrder()->getDocumentsForType(static::getDocumentType());
        $processedValue = 0;

        foreach ($documents as $document) {
            $processedValue += $document->getValueForFieldName($field);
        }

        return $processedValue;
    }

    /**
     * @return null
     */
    public function getPathForItems()
    {
        return Object\Service::createFolderByPath($this->getFullPath().'/items/');
    }

    /**
     * @return Asset
     */
    public function getAsset()
    {
        return Asset::getByPath($this->getAssetPath() . "/" . $this->getAssetFilename());
    }

    /**
     * @return string
     */
    public function getAssetPath()
    {
        $path = static::getPathForDocuments($this->getOrder(), $this->getDocumentDate());

        return $path instanceof Asset\Folder ? $path->getFullPath() : $path;
    }

    /**
     * @return string
     */
    public function getAssetFilename()
    {
        return sprintf('%s-%s.pdf', static::getDocumentType(), File::getValidFilename($this->getDocumentNumber()));
    }

    /**
     * Renders the Document to a PDF
     *
     * @throws Exception
     *
     * @return Asset\Document|bool
     */
    public function generate()
    {
        $locale = new \Zend_Locale($this->getOrder()->getLang());

        $params = [
            "order" => $this->getOrder(),
            "document" => $this,
            "language" => (string) $locale,
            "type" => static::getDocumentType(),
            static::getDocumentType() => $this
        ];

        $forward = new Action();
        $html = $forward->action(static::getDocumentType(), "order-print", "CoreShop", $params);
        $header = $forward->action("header", "order-print", "CoreShop", $params);
        $footer = $forward->action("footer", "order-print", "CoreShop", $params);

        try {
            $options = Configuration::get(sprintf('SYSTEM.%s.WKHTML', strtoupper(static::getDocumentType())));
            $pdfContent = Wkhtmltopdf::fromString($html, $header, $footer, ['options' => [$options]]);

            if ($pdfContent) {
                $document = Asset\Document::getByPath($this->getAssetPath().'/'.$this->getAssetFilename());

                if ($document instanceof Asset\Document) {
                    $document->delete();
                }

                $document = new Asset\Document();
                $document->setFilename($this->getAssetFilename());
                $document->setParent(Service::createFolderByPath($this->getAssetPath()));
                $document->setData($pdfContent);
                $document->save();

                return $document;
            }
        } catch (Exception $ex) {
            Logger::warn('wkhtmltopdf library not found, no document was generated');
        }

        return false;
    }

    /**
     * @param Order $order
     * @param array $items
     * @param array $params
     *
     * @return static
     */
    abstract public function fillDocument(Order $order, array $items, array $params = []);

    /**
     * @return Order\Document\Item
     */
    abstract public function createItemInstance();

    /**
     * @param array $items
     * @return Order\Document\Item[]
     */
    protected function fillDocumentItems(array $items)
    {
        $filledItems = [];

        foreach ($items as $item) {
            $orderItem = Item::getById($item['orderItemId']);
            $amount = $item['amount'];

            if ($orderItem instanceof Item) {
                $filledItem = $this->fillDocumentItem($orderItem, static::createItemInstance(), $amount);

                $filledItems[] = $filledItem;
            }
        }

        return $filledItems;
    }

    /**
     * @param Item $orderItem
     * @param Document\Item $documentItem
     * @param $amount
     *
     * @return Order\Document\Item
     */
    protected function fillDocumentItem(Item $orderItem, Order\Document\Item $documentItem, $amount)
    {
        \CoreShop\Bundle\CoreShopLegacyBundle\Tool\Service::copyObject($orderItem, $documentItem);

        $documentItem->setAmount($amount);
        $documentItem->setParent($this->getPathForItems());
        $documentItem->setTotal($orderItem->getPrice() * $amount);
        $documentItem->setTotalTax(($orderItem->getPrice() - $orderItem->getPriceWithoutTax()) * $amount);
        $this->setDocumentItemTaxes($orderItem, $documentItem, $documentItem->getTotalWithoutTax());
        $documentItem->setOrderItem($orderItem);
        $documentItem->setKey($orderItem->getKey());
        $documentItem->setPublished(true);
        $documentItem->save();

        return $documentItem;
    }

    /**
     * Calculates Item taxes for a specific amount
     *
     * @param Item $orderItem
     * @param Document\Item $docItem
     * @param $amount
     */
    protected function setDocumentItemTaxes(Item $orderItem, Document\Item $docItem, $amount)
    {
        $itemTaxes = new Object\Fieldcollection();
        $totalTax = 0;

        $orderTaxes = $orderItem->getTaxes();

        if (is_array($orderTaxes)) {
            foreach ($orderTaxes as $tax) {
                if ($tax instanceof Order\Tax) {
                    $taxRate = Tax::create();
                    $taxRate->setRate($tax->getRate());

                    $taxCalculator = new TaxCalculator([$taxRate]);

                    $itemTax = Order\Tax::create([
                        'name' => $tax->getName(),
                        'rate' => $tax->getRate(),
                        'amount' => $taxCalculator->getTaxesAmount($amount)
                    ]);

                    $itemTaxes->add($itemTax);

                    $totalTax += $itemTax->getAmount();
                }
            }
        }

        $docItem->setTotalTax($totalTax);
        $docItem->setTaxes($itemTaxes);
    }

    /**
     * @return User
     *
     * @deprecated Not supported anymore, will be removed with 1.3
     */
    public function getCustomer()
    {
        return $this->getOrder()->getCustomer();
    }

    /**
     * @param User $customer
     *
     * @deprecated Not supported anymore, will be removed with 1.3
     *
     * @return boolean
     */
    public function setCustomer($customer)
    {
        return false;
    }

    /**
     * @return mixed
     *
     * @deprecated Not supported anymore, will be removed with 1.3
     */
    public function getShippingAddress()
    {
        return $this->getOrder()->getShippingAddress();
    }

    /**
     * @param mixed $shippingAddress
     *
     * @deprecated Not supported anymore, will be removed with 1.3
     *
     * @return boolean
     */
    public function setShippingAddress($shippingAddress)
    {
        return false;
    }

    /**
     * @return mixed
     *
     * @deprecated Not supported anymore, will be removed with 1.3
     */
    public function getBillingAddress()
    {
        return $this->getOrder()->getBillingAddress();
    }

    /**
     * @param mixed $billingAddress
     *
     * @deprecated Not supported anymore, will be removed with 1.3
     *
     * @return boolean
     */
    public function setBillingAddress($billingAddress)
    {
        return false;
    }

    /**
     * @return mixed
     *
     * @throws ObjectUnsupportedException
     *
     * @deprecated Not supported anymore, will be removed with 1.3
     */
    public function getExtraInformation()
    {
        return $this->getOrder()->getExtraInformation();
    }

    /**
     * @param $extraInformation
     * @return bool
     *
     * @deprecated Not supported anymore, will be removed with 1.3
     */
    public function setExtraInformation($extraInformation)
    {
        return false;
    }


    /**
     * @return Order
     *
     * @throws ObjectUnsupportedException
     */
    public function getOrder()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param Order $order
     *
     * @throws ObjectUnsupportedException
     */
    public function setOrder($order)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return Carbon
     *
     * @throws ObjectUnsupportedException
     */
    public function getDocumentDate()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param Carbon|Date $documentDate
     *
     * @throws ObjectUnsupportedException
     */
    public function setDocumentDate($documentDate)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return string
     *
     * @throws ObjectUnsupportedException
     */
    public function getDocumentNumber()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param string $documentNumber
     *
     * @throws ObjectUnsupportedException
     */
    public function setDocumentNumber($documentNumber)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return string
     *
     * @throws ObjectUnsupportedException
     */
    public function getLang()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param string $lang
     *
     * @throws ObjectUnsupportedException
     */
    public function setLang($lang)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return Invoice\Item[]
     *
     * @throws ObjectUnsupportedException
     */
    public function getItems()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param Invoice\Item[] $items
     *
     * @throws ObjectUnsupportedException
     */
    public function setItems($items)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }
}
