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

namespace CoreShop\Bundle\CoreBundle\Fixtures\Data\Demo;

use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\Rule\Model\Action;
use CoreShop\Component\Rule\Model\Condition;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class ShippingRuleFixture extends Fixture implements FixtureGroupInterface
{

    public function __construct(
        private RepositoryInterface $shippingRuleRepository,
        private RepositoryInterface $storeRepository,
        private FactoryInterface $shippingRuleFactory,
    ) {
    }

    public static function getGroups(): array
    {
        return ['demo'];
    }
    public function load(ObjectManager $manager): void
    {
        if (!count($this->shippingRuleRepository->findAll())) {
            $defaultStore = $this->storeRepository->findStandard();
            $currency = $defaultStore->getCurrency()->getId();

            $configuration = [
                [
                    'name' => 'demo1',
                    'conditions' => [
                        [
                            'type' => 'amount',
                            'config' => [
                                'minAmount' => 0,
                                'maxAmount' => 15000,
                            ],
                        ],
                    ],
                    'actions' => [
                        [
                            'type' => 'price',
                            'config' => [
                                'price' => 500,
                                'currency' => $currency,
                            ],
                        ],
                    ],
                ],
                [
                    'name' => 'demo2',
                    'conditions' => [
                        [
                            'type' => 'amount',
                            'config' => [
                                'minAmount' => 15000,
                                'maxAmount' => 200000,
                            ],
                        ],
                    ],
                    'actions' => [
                        [
                            'type' => 'price',
                            'config' => [
                                'price' => 1000,
                                'currency' => $currency,
                            ],
                        ],
                    ],
                ],
                [
                    'name' => 'demo3',
                    'conditions' => [
                        [
                            'type' => 'amount',
                            'config' => [
                                'minAmount' => 200000,
                                'maxAmount' => 300000,
                            ],
                        ],
                    ],
                    'actions' => [
                        [
                            'type' => 'price',
                            'config' => [
                                'price' => 2000,
                                'currency' => $currency,
                            ],
                        ],
                    ],
                ],
            ];

            foreach ($configuration as $index => $config) {
                $rule = $this->shippingRuleFactory->createNew();
                $rule->setName($config['name']);
                $rule->setActive(true);

                foreach ($config['conditions'] as $cond) {
                    $condition = new Condition();
                    $condition->setType($cond['type']);
                    $condition->setConfiguration($cond['config']);

                    $rule->addCondition($condition);
                }

                foreach ($config['actions'] as $act) {
                    $action = new Action();
                    $action->setType($act['type']);
                    $action->setConfiguration($act['config']);

                    $rule->addAction($action);
                }

                $manager->persist($rule);

                $this->setReference('shippingRule'.$index, $rule);
            }

            $manager->flush();
        }
    }
}
