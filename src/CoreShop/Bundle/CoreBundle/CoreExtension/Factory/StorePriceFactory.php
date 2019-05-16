<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\CoreExtension\Factory;

use CoreShop\Bundle\CoreBundle\CoreExtension\StorePrice;
use CoreShop\Component\Core\Repository\ProductStorePriceRepositoryInterface;
use CoreShop\Component\Pimcore\DataObject\ObjectDataFactoryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Store\Repository\StoreRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class StorePriceFactory implements ObjectDataFactoryInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;

    /**
     * @var ProductStorePriceRepositoryInterface
     */
    private $productStorePriceRepository;

    /**
     * @param EntityManagerInterface               $entityManager
     * @param FactoryInterface                     $factory
     * @param StoreRepositoryInterface             $storeRepository
     * @param ProductStorePriceRepositoryInterface $productStorePriceRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        FactoryInterface $factory,
        StoreRepositoryInterface $storeRepository,
        ProductStorePriceRepositoryInterface $productStorePriceRepository
    ) {
        $this->entityManager = $entityManager;
        $this->factory = $factory;
        $this->storeRepository = $storeRepository;
        $this->productStorePriceRepository = $productStorePriceRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function create($type, $params)
    {
        return new StorePrice(
            $this->entityManager,
            $this->factory,
            $this->storeRepository,
            $this->productStorePriceRepository
        );
    }
}
