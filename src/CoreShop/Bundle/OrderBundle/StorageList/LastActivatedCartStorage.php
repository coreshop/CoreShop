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

namespace CoreShop\Bundle\OrderBundle\StorageList;

use Carbon\Carbon;
use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Order\Model\ActivatableCartInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\StorageList\Model\StorageListInterface;
use CoreShop\Component\StorageList\Storage\StorageListStorageInterface;
use Pimcore\Model\Version;

/**
 * Records when a cart is explicitly selected by its logged-in customer so the
 * last-opened cart can be restored across sessions (see
 * CustomerAndStoreBasedStorageListContext, which orders by lastActivatedAt).
 *
 * Reads and removals are delegated untouched. Only setForContext stamps, and
 * only when a customer is present in the context — that is the explicit
 * select/create path (StorageMultiListController), not the per-response session
 * sync (which passes only the store), so a switch is recorded once per action
 * rather than on every request.
 */
final class LastActivatedCartStorage implements StorageListStorageInterface
{
    public function __construct(
        private readonly StorageListStorageInterface $inner,
    ) {
    }

    public function hasForContext(array $context): bool
    {
        return $this->inner->hasForContext($context);
    }

    public function getForContext(array $context): ?StorageListInterface
    {
        return $this->inner->getForContext($context);
    }

    public function setForContext(array $context, StorageListInterface $storageList): void
    {
        $this->inner->setForContext($context, $storageList);

        $customer = $context['customer'] ?? null;
        if (!$customer instanceof CustomerInterface) {
            return;
        }

        if (!$storageList instanceof ActivatableCartInterface || !$storageList instanceof OrderInterface) {
            return;
        }

        if ($storageList->getCustomer()?->getId() !== $customer->getId()) {
            return;
        }

        $storageList->setLastActivatedAt(Carbon::now());

        $versioningEnabled = Version::isEnabled();
        Version::disable();

        try {
            $storageList->save();
        } finally {
            if ($versioningEnabled) {
                Version::enable();
            }
        }
    }

    public function removeForContext(array $context): void
    {
        $this->inner->removeForContext($context);
    }
}
