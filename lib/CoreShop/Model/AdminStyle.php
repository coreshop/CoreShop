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
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model;

/**
 * Class AdminStyle
 * @package CoreShop\Model\Product
 */
class AdminStyle extends \Pimcore\Model\Element\AdminStyle
{
    /**
     * AdminStyle constructor.
     * @param $element
     */
    public function __construct($element)
    {
        parent::__construct($element);

        if ($element instanceof Product) {
            $this->elementIconClass = 'coreshop_icon_product_green';
        }
        else if ($element instanceof Order) {
            $this->elementIconClass = 'coreshop_icon_order';
        }
        else if ($element instanceof Order\Item) {
            $this->elementIconClass = 'coreshop_icon_product';
        }
        else if ($element instanceof User) {
            $this->elementIconClass = 'coreshop_icon_customers';
        }
        else if ($element instanceof User\Address) {
            $this->elementIconClass = 'coreshop_icon_address';
        }
        else if ($element instanceof Category) {
            $this->elementIconClass = 'coreshop_icon_category';
        }
        else if ($element instanceof Order\Invoice) {
            $this->elementIconClass = 'coreshop_icon_orders_invoice';
        }
        else if($element instanceof Customer\Group) {
            $this->elementIconClass = 'coreshop_icon_customer_group';
        }
        else if($element instanceof Manufacturer) {
            $this->elementIconClass = 'coreshop_icon_manufacturer';
        }
        else if($element instanceof Cart) {
            $this->elementIconClass = "coreshop_icon_cart";
        }
        else if($element instanceof Order\Payment) {
            $this->elementIconClass = "coreshop_icon_payment";
        }
    }
}
