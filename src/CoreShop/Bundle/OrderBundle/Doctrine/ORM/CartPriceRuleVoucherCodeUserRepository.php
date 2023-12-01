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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\OrderBundle\Doctrine\ORM;

use CoreShop\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeUserInterface;
use CoreShop\Component\Order\Repository\CartPriceRuleVoucherCodeUserRepositoryInterface;

class CartPriceRuleVoucherCodeUserRepository extends EntityRepository implements CartPriceRuleVoucherCodeUserRepositoryInterface
{
    public function findUsesByCustomer(
        CustomerInterface $customer,
        CartPriceRuleVoucherCodeInterface $voucherCode
    ): ?CartPriceRuleVoucherCodeUserInterface {
        return $this->createQueryBuilder('o')
            ->where('o.voucherCode = :voucherCode')
            ->andWhere('o.customerId = :customerId')
            ->setParameter('voucherCode', $voucherCode)
            ->setParameter('customerId', $customer->getId())
            ->getQuery()
            ->getOneOrNullResult();
    }
}
