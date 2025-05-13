<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Order\Modifier;

use CoreShop\Component\Order\Factory\CartPriceRuleVoucherCodeCustomerFactoryInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeCustomerInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\PriceRuleItemInterface;
use CoreShop\Component\Order\Repository\CartPriceRuleVoucherCodeCustomerRepositoryInterface;
use CoreShop\Component\Order\Repository\CartPriceRuleVoucherRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Pimcore\Model\DataObject\Fieldcollection;

class VoucherModifier implements VoucherModifierInterface
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected CartPriceRuleVoucherRepositoryInterface $voucherCodeRepository,
        protected CartPriceRuleVoucherCodeCustomerRepositoryInterface $codePerUserRepository,
        protected CartPriceRuleVoucherCodeCustomerFactoryInterface $voucherCodeCustomerFactory,
    ) {
    }

    public function increment(OrderInterface $order): void
    {
        $priceRuleItems = $order->getPriceRuleItems();
        if (!$priceRuleItems instanceof Fieldcollection) {
            return;
        }

        foreach ($priceRuleItems->getItems() as $item) {
            if (!$item instanceof PriceRuleItemInterface) {
                continue;
            }

            if (!$item->getVoucherCode()) {
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

            $customer = $order->getCustomer();

            if (!$customer) {
                continue;
            }

            foreach ($item->getCartPriceRule()?->getConditions() ?: [] as $conditions) {
                if ($conditions->getType() === 'voucher') {
                    $maxUsagePerCustomer = $conditions->getConfiguration()['maxUsagePerUser'] ?? null;

                    if ($maxUsagePerCustomer !== null) {
                        $perCustomerEntry = $this->codePerUserRepository->findUsesByCustomer($customer, $voucherCode);

                        if ($perCustomerEntry instanceof CartPriceRuleVoucherCodeCustomerInterface) {
                            $perCustomerEntry->incrementUses();

                            $this->entityManager->persist($perCustomerEntry);
                        }

                        if (null === $perCustomerEntry) {
                            $perCustomerEntry = $this->voucherCodeCustomerFactory->createWithInitialData($customer, $voucherCode);

                            $this->entityManager->persist($perCustomerEntry);
                        }
                    }
                }
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
            if (!$item instanceof PriceRuleItemInterface) {
                continue;
            }

            if (!$item->getVoucherCode()) {
                continue;
            }

            $voucherCode = $this->voucherCodeRepository->findByCode($item->getVoucherCode());
            if ($voucherCode instanceof CartPriceRuleVoucherCodeInterface) {
                if ($voucherCode->getUses() !== 0) {
                    $voucherCode->setUses($voucherCode->getUses() - 1);
                    $voucherCode->setUsed($voucherCode->getUses() !== 0);

                    if ($voucherCode->isCreditCode()) {
                        $voucherCode->setCreditUsed(max(0, $voucherCode->getCreditUsed() - (-1 * $item->getDiscount(true))));
                    }

                    $this->entityManager->persist($voucherCode);
                }
            }

            $customer = $order->getCustomer();

            if (!$customer) {
                continue;
            }

            foreach ($item->getCartPriceRule()?->getConditions() ?: [] as $conditions) {
                if ($conditions->getType() === 'voucher') {
                    $maxUsagePerCustomer = $conditions->getConfiguration()['maxUsagePerUser'] ?? null;

                    if ($maxUsagePerCustomer !== null) {
                        $perCustomerEntry = $this->codePerUserRepository->findUsesByCustomer($customer, $voucherCode);

                        if ($perCustomerEntry instanceof CartPriceRuleVoucherCodeCustomerInterface) {
                            $perCustomerEntry->decrementUses();

                            if ($perCustomerEntry->getUses() === 0) {
                                $this->entityManager->remove($perCustomerEntry);
                            } else {
                                $this->entityManager->persist($perCustomerEntry);
                            }
                        }
                    }
                }
            }
        }

        $this->entityManager->flush();
    }
}
