<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Order\Model;

use Carbon\Carbon;

interface QuoteInterface extends SaleInterface
{
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
