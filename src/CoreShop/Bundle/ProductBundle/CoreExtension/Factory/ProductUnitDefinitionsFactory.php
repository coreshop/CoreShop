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

namespace CoreShop\Bundle\ProductBundle\CoreExtension\Factory;

use CoreShop\Bundle\ProductBundle\CoreExtension\ProductSpecificPriceRules;
use CoreShop\Bundle\ProductBundle\CoreExtension\ProductUnitDefinitions;
use CoreShop\Component\Pimcore\DataObject\ObjectDataFactoryInterface;
use CoreShop\Component\Product\Repository\ProductSpecificPriceRuleRepositoryInterface;
use CoreShop\Component\Product\Repository\ProductUnitDefinitionsRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Form\FormFactoryInterface;

class ProductUnitDefinitionsFactory implements ObjectDataFactoryInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var ProductUnitDefinitionsRepositoryInterface
     */
    private $repository;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param EntityManagerInterface                      $entityManager
     * @param FormFactoryInterface                        $formFactory
     * @param ProductUnitDefinitionsRepositoryInterface $repository
     * @param SerializerInterface                         $serializer
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        FormFactoryInterface $formFactory,
        ProductUnitDefinitionsRepositoryInterface $repository,
        SerializerInterface $serializer
    ) {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->repository = $repository;
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function create($type, $params)
    {
        return new ProductUnitDefinitions(
            $this->entityManager,
            $this->formFactory,
            $this->repository,
            $this->serializer
        );
    }
}
