<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Core\Model\CartInterface;
use CoreShop\Component\Order\Model\AdjustmentInterface;
use CoreShop\Component\Order\Model\ProposalCartPriceRuleItem;
use CoreShop\Component\Order\Model\SaleInterface;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Fieldcollection;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20180825163827 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $cartRepo = $this->container->get('coreshop.repository.cart');
        $orderRepo = $this->container->get('coreshop.repository.order');
        $quoteRepo = $this->container->get('coreshop.repository.quote');

        $adjustmentFactory = $this->container->get('coreshop.factory.adjustment');

        foreach ([$orderRepo, $quoteRepo, $cartRepo] as $repo) {
            $list = $repo->getList();
            $list->setUnpublished(true);
            $sales = $list->load();

            $definition = ClassDefinition::getById($repo->getClassId());
            $fieldDefinitions = $definition->fieldDefinitions;

            /**
             * @var $sale SaleInterface
             */
            foreach ($sales as $sale) {
                $sale->removeAdjustments(AdjustmentInterface::SHIPPING);
                $sale->removeAdjustments(AdjustmentInterface::CART_PRICE_RULE);

                if (array_key_exists('shippingNet', $fieldDefinitions) &&
                    array_key_exists('shippingGross', $fieldDefinitions)) {
                    if ($sale->getShippingNet() > 0) {
                        $sale->addAdjustment(
                            $adjustmentFactory->createWithData(
                                AdjustmentInterface::SHIPPING,
                                '',
                                $sale->getShippingGross() ?: 0,
                                $sale->getShippingNet() ?: 0
                            )
                        );
                    }
                }

                if ($sale instanceof CartInterface) {
                    if ($sale->getPriceRuleItems() instanceof Fieldcollection) {
                        /**
                         * @var $priceRuleItem ProposalCartPriceRuleItem
                         */
                        foreach ($sale->getPriceRuleItems()->getItems() as $priceRuleItem) {
                            if ($priceRuleItem->getDiscount(false) > 0) {
                                $sale->addAdjustment(
                                    $adjustmentFactory->createWithData(
                                        AdjustmentInterface::CART_PRICE_RULE,
                                        $priceRuleItem->getCartPriceRule() ? $priceRuleItem->getCartPriceRule()->getName() : '',
                                        $priceRuleItem->getDiscount(true) ?: 0,
                                        $priceRuleItem->getDiscount(true) ?: 0
                                    )
                                );
                            }
                        }
                    }
                } elseif ($sale instanceof SaleInterface) {
                    $sale->removeBaseAdjustments(AdjustmentInterface::SHIPPING);
                    $sale->removeBaseAdjustments(AdjustmentInterface::CART_PRICE_RULE);

                    if (array_key_exists('discountNet', $fieldDefinitions) &&
                        array_key_exists('discountGross', $fieldDefinitions)) {
                        if ($sale->getDiscountNet() > 0) {
                            $sale->addAdjustment(
                                $adjustmentFactory->createWithData(
                                    AdjustmentInterface::CART_PRICE_RULE,
                                    '',
                                    $sale->getDiscountGross() ?: 0,
                                    $sale->getDiscountNet() ?: 0
                                )
                            );
                        }
                    }

                    if (array_key_exists('baseShippingNet', $fieldDefinitions) &&
                        array_key_exists('baseShippingGross', $fieldDefinitions)) {
                        if ($sale->getBaseShippingNet() > 0) {
                            $sale->addBaseAdjustment(
                                $adjustmentFactory->createWithData(
                                    AdjustmentInterface::SHIPPING,
                                    '',
                                    $sale->getBaseShippingGross() ?: 0,
                                    $sale->getBaseShippingNet() ?: 0
                                )
                            );
                        }
                    }

                    if (array_key_exists('baseDiscountNet', $fieldDefinitions) &&
                        array_key_exists('baseDiscountGross', $fieldDefinitions)) {
                        if ($sale->getBaseDiscountNet() > 0) {
                            $sale->addBaseAdjustment(
                                $adjustmentFactory->createWithData(
                                    AdjustmentInterface::CART_PRICE_RULE,
                                    '',
                                    $sale->getBaseDiscountGross() ?: 0,
                                    $sale->getBaseDiscountNet() ?: 0
                                )
                            );
                        }
                    }
                }

                $sale->save();
            }
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
