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

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Resource\Exception\ImplementedByPimcoreException;
use Pimcore\Model\DataObject\Fieldcollection;

trait ProposalPriceRuleTrait
{
    /**
     * @return Fieldcollection|null
     */
    public function getPriceRuleItems()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setPriceRuleItems(?Fieldcollection $priceRuleItems)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function hasPriceRules(): bool
    {
        return $this->getPriceRuleItems() instanceof Fieldcollection && $this->getPriceRuleItems()->getCount() > 0;
    }

    /**
     * @return ProposalCartPriceRuleItemInterface[]
     */
    public function getPriceRules(): array
    {
        $rules = [];

        if ($this->getPriceRuleItems() instanceof Fieldcollection) {
            foreach ($this->getPriceRuleItems() as $ruleItem) {
                if ($ruleItem instanceof ProposalCartPriceRuleItemInterface) {
                    $rules[] = $ruleItem->getCartPriceRule();
                }
            }
        }

        /*
         * @var ProposalCartPriceRuleItemInterface[] $rules
         */
        return $rules;
    }

    public function setPriceRules($priceRules): void
    {
        if ($priceRules instanceof Fieldcollection) {
            $this->setPriceRuleItems($priceRules);
        }
    }

    public function addPriceRule(ProposalCartPriceRuleItemInterface $priceRule): void
    {
        if (!$this->hasPriceRule($priceRule)) {
            $items = $this->getPriceRuleItems();

            if (!$items instanceof Fieldcollection) {
                $items = new Fieldcollection();
            }

            if ($priceRule instanceof Fieldcollection\Data\AbstractData) {
                $items->add($priceRule);
            }

            $this->setPriceRules($items);
        }
    }

    public function removePriceRule(ProposalCartPriceRuleItemInterface $priceRule): void
    {
        $items = $this->getPriceRuleItems();

        if ($items instanceof Fieldcollection) {
            for ($i = 0, $c = $items->getCount(); $i < $c; ++$i) {
                $item = $items->get($i);

                if (!$item instanceof ProposalCartPriceRuleItem) {
                    continue;
                }

                if (!$item->getCartPriceRule() instanceof CartPriceRuleInterface) {
                    continue;
                }

                if (!$priceRule->getCartPriceRule() instanceof CartPriceRuleInterface) {
                    continue;
                }

                if ($item->getCartPriceRule()->getId() === $priceRule->getCartPriceRule()->getId()
                    && $item->getVoucherCode() === $priceRule->getVoucherCode()) {
                    $items->remove($i);

                    break;
                }
            }

            $this->setPriceRules($items);
        }
    }

    public function hasPriceRule(ProposalCartPriceRuleItemInterface $priceRule): bool
    {
        $items = $this->getPriceRuleItems();

        if ($items instanceof Fieldcollection) {
            foreach ($items as $item) {
                if (!$item instanceof ProposalCartPriceRuleItem) {
                    continue;
                }

                if (!$item->getCartPriceRule() instanceof CartPriceRuleInterface) {
                    continue;
                }

                if (!$priceRule->getCartPriceRule() instanceof CartPriceRuleInterface) {
                    continue;
                }

                if ($item->getCartPriceRule()->getId() === $priceRule->getCartPriceRule()->getId()
                    && $item->getVoucherCode() === $priceRule->getVoucherCode()) {
                    return true;
                }
            }
        }

        return false;
    }

    public function hasCartPriceRule(
        CartPriceRuleInterface $cartPriceRule,
        CartPriceRuleVoucherCodeInterface $voucherCode = null
    ): bool {
        return null !== $this->getPriceRuleByCartPriceRule($cartPriceRule, $voucherCode);
    }

    public function getPriceRuleByCartPriceRule(
        CartPriceRuleInterface $cartPriceRule,
        CartPriceRuleVoucherCodeInterface $voucherCode = null
    ): ?ProposalCartPriceRuleItemInterface {
        $items = $this->getPriceRuleItems();

        if ($items instanceof Fieldcollection) {
            foreach ($items as $item) {
                if (!$item instanceof ProposalCartPriceRuleItem) {
                    continue;
                }

                if (!$item->getCartPriceRule() instanceof CartPriceRuleInterface) {
                    continue;
                }

                if ($item->getCartPriceRule()->getId() === $cartPriceRule->getId()) {
                    if (null === $voucherCode || $voucherCode->getCode() === $item->getVoucherCode()) {
                        return $item;
                    }
                }
            }
        }

        return null;
    }
}
