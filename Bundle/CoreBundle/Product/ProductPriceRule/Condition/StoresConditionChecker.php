<?php

namespace CoreShop\Bundle\CoreBundle\Product\ProductPriceRule\Condition;

use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Rule\Condition\ConditionCheckerInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;
use CoreShop\Component\Store\Model\StoreInterface;
use Webmozart\Assert\Assert;

class StoresConditionChecker implements ConditionCheckerInterface
{
    /**
     * @var StoreContextInterface
     */
    private $storeContext;

    /**
     * @param StoreContextInterface $storeContext
     */
    public function __construct(StoreContextInterface $storeContext)
    {
        $this->storeContext = $storeContext;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($subject, array $configuration)
    {
        Assert::isInstanceOf($subject, ProductInterface::class);

        $store = $this->storeContext->getStore();

        if (!$store instanceof StoreInterface) {
            return false;
        }


        return in_array($store->getId(), $configuration['stores']);
    }
}
