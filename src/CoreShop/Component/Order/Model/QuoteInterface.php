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

namespace CoreShop\Component\Order\Model;

use Carbon\Carbon;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Payment\Model\PaymentInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;
use CoreShop\Component\Store\Model\StoreInterface;
use Pimcore\Model\Object\Fieldcollection;

interface QuoteInterface extends SaleInterface
{
    /**
     * @return string
     */
    public function getQuoteLanguage();

    /**
     * @param $quoteLanguage
     */
    public function setQuoteLanguage($quoteLanguage);

    /**
     * @return Carbon
     */
    public function getQuoteDate();

    /**
     * @param Carbon $quoteDate
     */
    public function setQuoteDate($quoteDate);

    /**
     * @return string
     */
    public function getQuoteNumber();

    /**
     * @param string $quoteNumber
     */
    public function setQuoteNumber($quoteNumber);
}
