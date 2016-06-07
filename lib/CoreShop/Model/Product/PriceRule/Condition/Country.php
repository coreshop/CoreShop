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
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\Product\PriceRule\Condition;

use CoreShop\Model\Product\PriceRule;
use CoreShop\Model\Product;
use CoreShop\Model\Country as CountryModel;
use CoreShop\Tool;

class Country extends AbstractCondition
{
    /**
     * @var int
     */
    public $country;

    /**
     * @var string
     */
    public $type = 'country';

    /**
     * @return CountryModel
     */
    public function getCountry()
    {
        if (!$this->country instanceof CountryModel) {
            $this->country = CountryModel::getById($this->country);
        }

        return $this->country;
    }

    /**
     * @param int $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * Check if Product is Valid for Condition.
     *
     * @param Product $product
     * @param Product\AbstractProductPriceRule $priceRule
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function checkCondition(Product $product, Product\AbstractProductPriceRule $priceRule)
    {
        if ($this->getCountry()->getId() !== Tool::getCountry()->getId()) {
            return false;
        }

        return true;
    }
}
