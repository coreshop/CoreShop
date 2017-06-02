<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Migrations\Data\Demo\ORM;

use CoreShop\Component\Rule\Model\Action;
use CoreShop\Component\Rule\Model\Condition;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Okvpn\Bundle\MigrationBundle\Fixture\VersionedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ShippingRuleFixture extends AbstractFixture implements ContainerAwareInterface, VersionedFixtureInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return '2.0';
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        if (!count($this->container->get('coreshop.repository.shipping_rule')->findAll())) {
            $configuration = [
                [
                    'name' => 'demo1',
                    'conditions' => [
                        [
                            'type' => 'amount',
                            'config' => [
                                'minAmount' => 0,
                                'maxAmount' => 150
                            ]
                        ],
                        [
                            'type' => 'zone',
                            'config' => [
                                'zones' => [4]
                            ]
                        ]
                    ],
                    'actions' => [
                        [
                            'type' => 'price',
                            'config' => [
                                'price' => 5
                            ]
                        ]
                    ]
                ],
                [
                    'name' => 'demo2',
                    'conditions' => [
                        [
                            'type' => 'amount',
                            'config' => [
                                'minAmount' => 150,
                                'maxAmount' => 2000
                            ]
                        ],
                        [
                            'type' => 'zone',
                            'config' => [
                                'zones' => [4]
                            ]
                        ]
                    ],
                    'actions' => [
                        [
                            'type' => 'price',
                            'config' => [
                                'price' => 10
                            ]
                        ]
                    ]
                ],
                [
                    'name' => 'demo2',
                    'conditions' => [
                        [
                            'type' => 'amount',
                            'config' => [
                                'minAmount' => 2000,
                                'maxAmount' => 3000
                            ]
                        ],
                        [
                            'type' => 'zone',
                            'config' => [
                                'zones' => [4]
                            ]
                        ]
                    ],
                    'actions' => [
                        [
                            'type' => 'price',
                            'config' => [
                                'price' => 20
                            ]
                        ]
                    ]
                ]
            ];

            foreach ($configuration as $index => $config) {
                $rule = $this->container->get('coreshop.factory.shipping_rule')->createNew();
                $rule->setName($config['name']);

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

                $this->setReference('shippingRule' . $index, $rule);
            }

            $manager->flush();
        }
    }
}
