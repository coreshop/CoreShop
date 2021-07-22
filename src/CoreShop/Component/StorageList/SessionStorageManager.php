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

namespace CoreShop\Component\StorageList;

use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\StorageList\Model\StorageListInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionStorageManager implements StorageListManagerInterface
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var string
     */
    private $name;

    /**
     * @var FactoryInterface
     */
    private $sessionListFactory;

    /**
     * @param SessionInterface $session
     * @param string           $name
     * @param FactoryInterface $sessionListFactory
     */
    public function __construct(SessionInterface $session, string $name, FactoryInterface $sessionListFactory)
    {
        $this->session = $session;
        $this->name = $name;
        $this->sessionListFactory = $sessionListFactory;
    }

    /**
     * @return StorageListInterface
     */
    public function getStorageList()
    {
        $list = $this->session->get($this->name);

        if (!$list instanceof StorageListInterface) {
            $list = $this->sessionListFactory->createNew();
        }

        return $list;
    }

    /**
     * @return bool
     */
    public function hasStorageList()
    {
        return $this->session->has($this->name);
    }

    /**
     * @param StorageListInterface $storageList
     *
     * @return bool
     */
    public function persist(StorageListInterface $storageList)
    {
        $this->session->set($this->name, $storageList);

        return true;
    }
}
