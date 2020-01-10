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

namespace CoreShop\Bundle\CoreBundle\Collector;

use CoreShop\Component\Core\Repository\CurrencyRepositoryInterface;
use CoreShop\Component\Currency\Context\CurrencyContextInterface;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

final class CurrencyCollector extends DataCollector
{
    /**
     * @var CurrencyContextInterface
     */
    private $currencyContext;

    /**
     * @param CurrencyRepositoryInterface $currencyRepository
     * @param CurrencyContextInterface    $currencyContext
     * @param StoreContextInterface       $storeContext
     * @param bool                        $currencyChangeSupport
     */
    public function __construct(
        CurrencyRepositoryInterface $currencyRepository,
        CurrencyContextInterface $currencyContext,
        StoreContextInterface $storeContext,
        $currencyChangeSupport = false
    ) {
        $this->currencyContext = $currencyContext;

        try {
            $this->data = [
                'currency' => null,
                'currencies' => $currencyRepository->findActiveForStore($storeContext->getStore()),
                'currency_change_support' => $currencyChangeSupport,
            ];
        } catch (\Exception $ex) {
            //If some goes wrong, we just ignore it
        }
    }

    /**
     * @return CurrencyInterface
     */
    public function getCurrency()
    {
        return $this->data['currency'];
    }

    /**
     * @return CurrencyInterface[]
     */
    public function getCurrencies()
    {
        return $this->data['currencies'];
    }

    /**
     * @return bool
     */
    public function isCurrencyChangeSupported()
    {
        return $this->data['currency_change_support'];
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        try {
            $this->data['currency'] = $this->currencyContext->getCurrency();
        } catch (\Exception $exception) {
            //If some goes wrong, we just ignore it
        }
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        $this->data = [];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'coreshop.currency_collector';
    }
}
