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

class QuoteItem extends SaleItem implements QuoteItemInterface
{
    /**
     * {@inheritdoc}
     */
    public function getSaleDocument()
    {
        return $this->getQuote();
    }

    /**
     * {@inheritdoc}
     */
    public function getQuote()
    {
        $parent = $this->getParent();

        do {
            if (is_subclass_of($parent, QuoteInterface::class)) {
                return $parent;
            }
            $parent = $parent->getParent();
        } while ($parent != null);

        throw new \InvalidArgumentException('Quote could not be found!');
    }
}
