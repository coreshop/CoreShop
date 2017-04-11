<?php
namespace CoreShop\Bundle\CurrencyBundle\Collector;

use CoreShop\Component\Core\Repository\CurrencyRepositoryInterface;
use CoreShop\Component\Currency\Context\CurrencyContextInterface;
use CoreShop\Component\Currency\Context\CurrencyNotFoundException;
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
     * @param CurrencyContextInterface $currencyContext
     * @param StoreContextInterface $storeContext
     * @param bool $currencyChangeSupport
     */
    public function __construct(
        CurrencyRepositoryInterface $currencyRepository,
        CurrencyContextInterface $currencyContext,
        StoreContextInterface $storeContext,
        $currencyChangeSupport = false
    ) {
        $this->currencyContext = $currencyContext;

        $this->data = [
            'currency' => null,
            'currencies' => $currencyRepository->findActiveForStore($storeContext->getStore()),
            'currency_change_support' => $currencyChangeSupport,
        ];
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
        } catch (CurrencyNotFoundException $exception) {
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'coreshop.currency_collector';
    }
}
