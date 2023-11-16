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
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeUser;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeUserInterface;
use CoreShop\Component\Order\Repository\CartPriceRuleVoucherCodeUserRepositoryInterface;

class CartPriceRuleVoucherCodeUserRepository extends EntityRepository implements CartPriceRuleVoucherCodeUserRepositoryInterface
{

    public function findByUsesById(int $userId, int $voucherCodeId): ?CartPriceRuleVoucherCodeUserInterface
    {
        return $this->createQueryBuilder('o')
          ->where('o.voucherCode = :voucherCode')
          ->andWhere('o.userId = :userId')
          ->setParameter('voucherCode', $voucherCodeId)
          ->setParameter('userId', $userId)
          ->getQuery()
          ->getOneOrNullResult()
          ;
    }

    public function addCodeUserUsage(int $userId, CartPriceRuleVoucherCodeInterface $voucherCode): void
    {
        $voucherCodeUser = new CartPriceRuleVoucherCodeUser();
        $voucherCodeUser->setUserId($userId);
        $voucherCodeUser->setUses(0);
        $voucherCodeUser->setVoucherCode($voucherCode);

        $this->add($voucherCodeUser);
    }

    public function updateCodeUserUsage(int $id): void
    {
        $existingEntry = $this->find($id);

        if ($existingEntry instanceof CartPriceRuleVoucherCodeUserInterface){
            $existingEntry->incrementUses();
            $this->add($existingEntry);
        }
    }
}
