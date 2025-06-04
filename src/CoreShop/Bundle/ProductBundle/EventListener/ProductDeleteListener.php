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

namespace CoreShop\Bundle\ProductBundle\EventListener;

use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Product\Repository\ProductSpecificPriceRuleRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Event\Model\ElementEventInterface;

final class ProductDeleteListener
{
    public function __construct(
        private ProductSpecificPriceRuleRepositoryInterface $repository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function onPostDelete(ElementEventInterface $event): void
    {
        if ($event instanceof DataObjectEvent) {
            $object = $event->getObject();

            if (!$object instanceof ProductInterface) {
                return;
            }

            $entities = $this->repository->findForProduct($object);

            foreach ($entities as $rule) {
                $this->entityManager->remove($rule);
            }

            $this->entityManager->flush();
        }
    }
}
