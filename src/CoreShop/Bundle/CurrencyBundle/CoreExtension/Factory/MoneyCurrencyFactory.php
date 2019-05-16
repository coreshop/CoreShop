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

namespace CoreShop\Bundle\CurrencyBundle\CoreExtension\Factory;

use CoreShop\Bundle\CurrencyBundle\CoreExtension\MoneyCurrency;
use CoreShop\Component\Currency\Repository\CurrencyRepositoryInterface;
use CoreShop\Component\Pimcore\DataObject\ObjectDataFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class MoneyCurrencyFactory implements ObjectDataFactoryInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var CurrencyRepositoryInterface
     */
    private $repository;

    /**
     * @param EntityManagerInterface      $entityManager
     * @param CurrencyRepositoryInterface $repository
     */
    public function __construct(EntityManagerInterface $entityManager, CurrencyRepositoryInterface $repository)
    {
        $this->entityManager = $entityManager;
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function create($type, $params)
    {
        return new MoneyCurrency(
            $this->entityManager,
            $this->repository
        );
    }
}
