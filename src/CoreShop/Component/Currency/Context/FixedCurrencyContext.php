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

declare(strict_types=1);

namespace CoreShop\Component\Currency\Context;

use CoreShop\Component\Currency\Model\CurrencyInterface;

final class FixedCurrencyContext implements CurrencyContextInterface
{
    /**
     * @var CurrencyInterface
     */
    private $currency;

    public function setCurrency(CurrencyInterface $currency): void
    {
        $this->currency = $currency;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency(): CurrencyInterface
    {
        if ($this->currency instanceof CurrencyInterface) {
            return $this->currency;
        }

        throw new CurrencyNotFoundException();
    }
}
