<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Currency\Context;

use CoreShop\Component\Currency\Model\CurrencyInterface;
use Laminas\Stdlib\PriorityQueue;

final class CompositeCurrencyContext implements CurrencyContextInterface
{
    /**
     * @var PriorityQueue|CurrencyContextInterface[]
     * @psalm-var PriorityQueue<CurrencyContextInterface>
     */
    private PriorityQueue $currencyContexts;

    public function __construct()
    {
        $this->currencyContexts = new PriorityQueue();
    }

    public function addContext(CurrencyContextInterface $currencyContext, int $priority = 0): void
    {
        $this->currencyContexts->insert($currencyContext, $priority);
    }

    public function getCurrency(): CurrencyInterface
    {
        $lastException = null;

        foreach ($this->currencyContexts as $currencyContext) {
            try {
                return $currencyContext->getCurrency();
            } catch (CurrencyNotFoundException $exception) {
                $lastException = $exception;

                continue;
            }
        }

        throw new CurrencyNotFoundException(null, $lastException);
    }
}
