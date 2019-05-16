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
use CoreShop\Component\Pimcore\DataObject\ObjectDataFactoryInterface;
use CoreShop\Component\Product\Repository\ProductSpecificPriceRuleRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Form\FormFactoryInterface;

class ProductSpecificPriceRulesFactory implements ObjectDataFactoryInterface
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
     * @var ProductSpecificPriceRuleRepositoryInterface
     */
    private $repository;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var array
     */
    private $configConditions;

    /**
     * @var array
     */
    private $configActions;

    /**
     * @param EntityManagerInterface                      $entityManager
     * @param FormFactoryInterface                        $formFactory
     * @param ProductSpecificPriceRuleRepositoryInterface $repository
     * @param SerializerInterface                         $serializer
     * @param array                                       $configConditions
     * @param array                                       $configActions
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        FormFactoryInterface $formFactory,
        ProductSpecificPriceRuleRepositoryInterface $repository,
        SerializerInterface $serializer,
        array $configConditions,
        array $configActions
    ) {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->repository = $repository;
        $this->serializer = $serializer;
        $this->configConditions = $configConditions;
        $this->configActions = $configActions;
    }

    /**
     * {@inheritdoc}
     */
    public function create($type, $params)
    {
        return new ProductSpecificPriceRules(
            $this->entityManager,
            $this->formFactory,
            $this->repository,
            $this->serializer,
            $this->configActions,
            $this->configConditions
        );
    }
}
