<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.coreshop.org/license
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     New BSD License
 */

namespace Pimcore\Model\Object\ClassDefinition\Data;

use Pimcore\Model;

use CoreShop\Model\Currency;

class CoreShopCurrencyMultiselect extends Model\Object\ClassDefinition\Data\Multiselect {

    /**
     * Static type of this element
     *
     * @var string
     */
    public $fieldtype = "coreShopCurrencyMultiselect";

    /** Restrict selection to comma-separated list of currencies.
     * @var null
     */
    public $restrictTo = null;


    public function __construct() {
        $currencies = new Currency\Listing();
        $currencies = $currencies->getCurrencies();

        $options = array();

        foreach ($currencies as $currency) {
            $options[] = array(
                "key" => $currency->getName(),
                "value" => $currency->getId()
            );
        }

        $this->setOptions($options);
    }

    /**
     * @param string $restrictTo
     */
    public function setRestrictTo($restrictTo)
    {
        $this->restrictTo = $restrictTo;
    }

    /**
     * @return string
     */
    public function getRestrictTo()
    {
        return $this->restrictTo;
    }

    /**
     * @return array
     */
    public function getOptions() {
        return $this->options;
    }
}
