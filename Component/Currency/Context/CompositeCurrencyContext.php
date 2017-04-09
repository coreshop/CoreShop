<?php

namespace CoreShop\Component\Currency\Context;

use Zend\Stdlib\PriorityQueue;

final class CompositeCurrencyContext implements CurrencyContextInterface
{
    /**
     * @var PriorityQueue|CurrencyContextInterface[]
     */
    private $currencyContexts;

    public function __construct()
    {
        $this->currencyContexts = new PriorityQueue();
    }

    /**
     * @param CurrencyContextInterface $currencyContext
     * @param int $priority
     */
    public function addContext(CurrencyContextInterface $currencyContext, $priority = 0)
    {
        $this->currencyContexts->insert($currencyContext, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrencyCode()
    {
        $lastException = null;

        foreach ($this->currencyContexts as $currencyContext) {
            try {
                return $currencyContext->getCurrencyCode();
            } catch (CurrencyNotFoundException $exception) {
                $lastException = $exception;

                continue;
            }
        }

        throw new CurrencyNotFoundException(null, $lastException);
    }
}
