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

namespace CoreShop\Component\Order\Modifier;

use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\ProposalCartPriceRuleItemInterface;
use CoreShop\Component\Order\Repository\CartPriceRuleVoucherRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Pimcore\Model\DataObject\Fieldcollection;

class VoucherModifier implements VoucherModifierInterface
{
    protected EntityManagerInterface $entityManager;
    protected CartPriceRuleVoucherRepositoryInterface $voucherCodeRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        CartPriceRuleVoucherRepositoryInterface $voucherCodeRepository
    ) {
        $this->entityManager = $entityManager;
        $this->voucherCodeRepository = $voucherCodeRepository;
    }

    public function increment(OrderInterface $order): void
    {
        $priceRuleItems = $order->getPriceRuleItems();
        if (!$priceRuleItems instanceof Fieldcollection) {
            return;
        }

        foreach ($priceRuleItems->getItems() as $item) {
            if (!$item instanceof ProposalCartPriceRuleItemInterface) {
                continue;
            }

            $voucherCode = $this->voucherCodeRepository->findByCode($item->getVoucherCode());
            if ($voucherCode instanceof CartPriceRuleVoucherCodeInterface) {
                $voucherCode->setUsed(true);
                $voucherCode->setUses($voucherCode->getUses() + 1);

                if ($voucherCode->isCreditCode()) {
                    $voucherCode->setCreditUsed(-1 * $item->getDiscount(true));
                }

                $this->entityManager->persist($voucherCode);
            }
        }

        $this->entityManager->flush();
    }

    public function decrement(OrderInterface $order): void
    {
        $priceRuleItems = $order->getPriceRuleItems();
        if (!$priceRuleItems instanceof Fieldcollection) {
            return;
        }

        foreach ($priceRuleItems->getItems() as $item) {
            if (!$item instanceof ProposalCartPriceRuleItemInterface) {
                continue;
            }

            $voucherCode = $this->voucherCodeRepository->findByCode($item->getVoucherCode());
            if ($voucherCode instanceof CartPriceRuleVoucherCodeInterface) {
                if ($voucherCode->getUses() !== 0) {
                    $voucherCode->setUses($voucherCode->getUses() - 1);
                    $voucherCode->setUsed($voucherCode->getUses() !== 0);

                    if ($voucherCode->isCreditCode()) {
                        $voucherCode->setCreditUsed(max(0,$voucherCode->getCreditUsed() - (-1 * $item->getDiscount(true))));
                    }

                    $this->entityManager->persist($voucherCode);
                }
            }
        }

        $this->entityManager->flush();
    }
}
