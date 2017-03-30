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

namespace CoreShop\Bundle\CoreShopLegacyBundle\Model;

use Pimcore\Model\Asset\Image;
use Pimcore\Model\Object\AbstractObject;

/**
 * Class AdminStyle
 * @package CoreShop\Bundle\CoreShopLegacyBundle\Model\Product
 */
class AdminStyle extends \Pimcore\Model\Element\AdminStyle
{
    /**
     * @var AbstractObject
     */
    protected $element;

    /**
     * AdminStyle constructor.
     * @param $element
     */
    public function __construct($element)
    {
        parent::__construct($element);

        if ($element instanceof Product) {
            $this->elementIconClass = 'coreshop_icon_product_green';
        } elseif ($element instanceof Order) {
            $this->elementIconClass = 'coreshop_icon_order';
        } elseif ($element instanceof Order\Item) {
            $this->elementIconClass = 'coreshop_icon_product';
        } elseif ($element instanceof User) {
            $this->elementIconClass = 'coreshop_icon_customers';
        } elseif ($element instanceof User\Address) {
            $this->elementIconClass = 'coreshop_icon_address';
        } elseif ($element instanceof Category) {
            $this->elementIconClass = 'coreshop_icon_category';
        } elseif ($element instanceof Order\Invoice) {
            $this->elementIconClass = 'coreshop_icon_orders_invoice';
        } elseif ($element instanceof Customer\Group) {
            $this->elementIconClass = 'coreshop_icon_customer_group';
        } elseif ($element instanceof Manufacturer) {
            $this->elementIconClass = 'coreshop_icon_manufacturer';
        } elseif ($element instanceof Cart) {
            $this->elementIconClass = "coreshop_icon_cart";
        } elseif ($element instanceof Order\Payment) {
            $this->elementIconClass = "coreshop_icon_payment";
        } elseif ($element instanceof Order\Shipment) {
            $this->elementIconClass = "coreshop_icon_orders_shipment";
        } elseif ($element instanceof Order\Shipment\Item) {
            $this->elementIconClass = "coreshop_icon_orders_shipment";
        }

        $this->element = $element;
    }

    /**
     * @return array
     */
    public function getElementQtipConfig()
    {
        if ($this->element instanceof Product) {
            $image = $this->element->getImage();

            $text = sprintf("<h1>%s</h1>", $this->element->getArticleNumber());

            if ($image instanceof Image) {
                $thumbnail = $image->getThumbnail("coreshop_productDetailThumbnail");

                $text .= sprintf("<p>%s</p>", $thumbnail->getHTML());
            }
            return [
                "title" => $this->element->getName() . " (" . $this->element->getId() . ")",
                "text" => $text
            ];
        } elseif ($this->element instanceof Order) {
            $translate = new \Pimcore\Translate\Admin(\Zend_Registry::get("Zend_Locale"));

            $text = sprintf(
                '<p style="text-align: right">
                        %s %s<br/>
                        %s %s<br/>
                        %s %s<br/>
                        <br/>
                        %s (%d)
                </p>',
                $translate->translate("Subtotal"),
                $this->element->formatPrice($this->element->getSubtotal()),
                $translate->translate("Tax"),
                $this->element->formatPrice($this->element->getTotalTax()),
                $translate->translate("Total"),
                $this->element->formatPrice($this->element->getTotal()),
                $translate->translate("Invoices"),
                count($this->element->getInvoices())
            );

            $status = $this->element->getOrderStatus();

            return [
                "title" => is_array($status) ? $status['label'] : '',
                "text" => $text
            ];
        }

        return parent::getElementQtipConfig();
    }
}
