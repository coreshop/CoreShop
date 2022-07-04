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

namespace CoreShop\Component\StorageList;

use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\StorageList\Model\StorageListInterface;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionStorageManager implements StorageListManagerInterface
{
    public function __construct(
        private RequestStack $requestStack,
        private string $name,
        private FactoryInterface $sessionListFactory)
    {
    }

    public function getStorageList(): StorageListInterface
    {
        $list = $this->getSession()->get($this->name);

        if (!$list instanceof StorageListInterface) {
            $list = $this->sessionListFactory->createNew();
        }

        return $list;
    }

    public function hasStorageList(): bool
    {
        return $this->getSession()->has($this->name);
    }

    public function persist(StorageListInterface $storageList): void
    {
        $this->getSession()->set($this->name, $storageList);
    }

    private function getSession(): SessionInterface
    {
        if (null !== $this->requestStack->getSession()) {
            return $this->requestStack->getSession();
        }

        throw new SessionNotFoundException();
    }
}
