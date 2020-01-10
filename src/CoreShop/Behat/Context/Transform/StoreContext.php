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

namespace CoreShop\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Store\Repository\StoreRepositoryInterface;
use Webmozart\Assert\Assert;

final class StoreContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;

    /**
     * @param SharedStorageInterface   $sharedStorage,
     * @param StoreRepositoryInterface $storeRepository
     */
    public function __construct(SharedStorageInterface $sharedStorage, StoreRepositoryInterface $storeRepository)
    {
        $this->sharedStorage = $sharedStorage;
        $this->storeRepository = $storeRepository;
    }

    /**
     * @Transform /^store(?:|s) "([^"]+)"$/
     */
    public function getStoreByName($name)
    {
        $stores = $this->storeRepository->findBy(['name' => $name]);

        Assert::eq(
            count($stores),
            1,
            sprintf('%d stores has been found with name "%s".', count($stores), $name)
        );

        return reset($stores);
    }
}
