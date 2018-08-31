<?php

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Resource\ImplementedByPimcoreException;
use Pimcore\Model\DataObject\Fieldcollection;

trait ProposalPriceRuleTrait
{
    /**
     * @return Fieldcollection
     */
    public function getPriceRuleItems()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @param Fieldcollection $priceRulesCollection
     */
    public function setPriceRuleItems($priceRulesCollection)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function hasPriceRules()
    {
        return $this->getPriceRuleItems() instanceof Fieldcollection && $this->getPriceRuleItems()->getCount() > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriceRules()
    {
        $rules = [];

        if ($this->getPriceRuleItems() instanceof Fieldcollection) {
            foreach ($this->getPriceRuleItems() as $ruleItem) {
                if ($ruleItem instanceof ProposalCartPriceRuleItem) {
                    $rules[] = $ruleItem->getCartPriceRule();
                }
            }
        }

        return $rules;
    }

    /**
     * {@inheritdoc}
     */
    public function setPriceRules($priceRules)
    {
        if ($priceRules instanceof Fieldcollection) {
            $this->setPriceRuleItems($priceRules);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addPriceRule($priceRule)
    {
        if (!$this->hasPriceRule($priceRule)) {
            $items = $this->getPriceRuleItems();

            if (!$items instanceof Fieldcollection) {
                $items = new Fieldcollection();
            }

            $items->add($priceRule);

            $this->setPriceRules($items);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removePriceRule($priceRule)
    {
        $items = $this->getPriceRuleItems();

        if ($items instanceof Fieldcollection) {
            for ($i = 0, $c = $items->getCount(); $i < $c; ++$i) {
                $arrayItem = $items->get($i);

                if ($arrayItem->getCartPriceRule()->getId() === $priceRule->getId()) {
                    $items->remove($i);
                    break;
                }
            }

            $this->setPriceRules($items);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasPriceRule($priceRule)
    {
        $items = $this->getPriceRuleItems();

        if ($items instanceof Fieldcollection) {
            foreach ($items as $item) {
                if ($item instanceof ProposalCartPriceRuleItem) {
                    if ($item->getCartPriceRule() instanceof CartPriceRuleInterface) {
                        if ($item->getCartPriceRule()->getId() === $priceRule->getId()) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

}