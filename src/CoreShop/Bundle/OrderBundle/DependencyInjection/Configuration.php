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

namespace CoreShop\Bundle\OrderBundle\DependencyInjection;

use CoreShop\Bundle\OrderBundle\Controller\CartPriceRuleController;
use CoreShop\Bundle\OrderBundle\Controller\OrderCommentController;
use CoreShop\Bundle\OrderBundle\Controller\OrderController;
use CoreShop\Bundle\OrderBundle\Controller\OrderCreationController;
use CoreShop\Bundle\OrderBundle\Controller\OrderInvoiceController;
use CoreShop\Bundle\OrderBundle\Controller\OrderPaymentController;
use CoreShop\Bundle\OrderBundle\Controller\OrderShipmentController;
use CoreShop\Bundle\OrderBundle\Controller\QuoteController;
use CoreShop\Bundle\OrderBundle\Controller\QuoteCreationController;
use CoreShop\Bundle\OrderBundle\Doctrine\ORM\CartPriceRuleRepository;
use CoreShop\Bundle\OrderBundle\Doctrine\ORM\CartPriceRuleVoucherRepository;
use CoreShop\Bundle\OrderBundle\Form\Type\CartPriceRuleType;
use CoreShop\Bundle\OrderBundle\Pimcore\Repository\CartRepository;
use CoreShop\Bundle\OrderBundle\Pimcore\Repository\CartItemRepository;
use CoreShop\Bundle\OrderBundle\Pimcore\Repository\OrderInvoiceRepository;
use CoreShop\Bundle\OrderBundle\Pimcore\Repository\OrderRepository;
use CoreShop\Bundle\OrderBundle\Pimcore\Repository\OrderShipmentRepository;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartItemInterface;
use CoreShop\Component\Order\Model\CartPriceRule;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCode;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderInvoiceInterface;
use CoreShop\Component\Order\Model\OrderInvoiceItemInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Model\OrderShipmentInterface;
use CoreShop\Component\Order\Model\OrderShipmentItemInterface;
use CoreShop\Component\Order\Model\ProposalCartPriceRuleItemInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Order\Model\QuoteInterface;
use CoreShop\Component\Order\Model\QuoteItemInterface;
use CoreShop\Component\Resource\Factory\Factory;
use CoreShop\Component\Resource\Factory\PimcoreFactory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('coreshop_order');

        $rootNode
            ->children()
                ->scalarNode('driver')->defaultValue(CoreShopResourceBundle::DRIVER_DOCTRINE_ORM)->end()
            ->end()
        ;
        $this->addModelsSection($rootNode);
        $this->addPimcoreResourcesSection($rootNode);
        $this->addCartCleanupSection($rootNode);
        $this->addStack($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addCartCleanupSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('expiration')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('cart')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->integerNode('days')->defaultValue(0)->end()
                                ->booleanNode('anonymous')->defaultValue(true)->end()
                                ->booleanNode('customer')->defaultValue(true)->end()
                            ->end()
                        ->end()
                            ->arrayNode('order')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->integerNode('days')->defaultValue(20)->end()
                            ->end()
                        ->end()
                    ->end()
                ->end();
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addStack(ArrayNodeDefinition $node)
    {
        $node->children()
            ->arrayNode('stack')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('purchasable')->defaultValue(PurchasableInterface::class)->cannotBeEmpty()->end()

                ->end()
            ->end()
        ->end();
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addModelsSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('resources')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('cart_price_rule')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->scalarNode('permission')->defaultValue('cart_price_rule')->cannotBeOverwritten()->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(CartPriceRule::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(CartPriceRuleInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('admin_controller')->defaultValue(CartPriceRuleController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(CartPriceRuleRepository::class)->end()
                                        ->scalarNode('form')->defaultValue(CartPriceRuleType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('cart_price_rule_voucher_code')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(CartPriceRuleVoucherCode::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(CartPriceRuleVoucherCodeInterface::class)->cannotBeEmpty()->end()
                                        //->scalarNode('admin_controller')->defaultValue(CartPriceRuleController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(CartPriceRuleVoucherRepository::class)->end()
                                        //TODO: ->scalarNode('form')->defaultValue(CartPriceRuleType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('pimcore')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('cart')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->scalarNode('path')->defaultValue('carts')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue('Pimcore\Model\DataObject\CoreShopCart')->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(CartInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(PimcoreFactory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(CartRepository::class)->cannotBeEmpty()->end()
                                        ->scalarNode('install_file')->defaultValue('@CoreShopOrderBundle/Resources/install/pimcore/classes/CoreShopCart.json')->end()
                                        ->scalarNode('type')->defaultValue(CoreShopResourceBundle::PIMCORE_MODEL_TYPE_OBJECT)->cannotBeOverwritten(true)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('cart_item')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->scalarNode('path')->defaultValue('items')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue('Pimcore\Model\DataObject\CoreShopCartItem')->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(CartItemInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(PimcoreFactory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(CartItemRepository::class)->cannotBeEmpty()->end()
                                        ->scalarNode('install_file')->defaultValue('@CoreShopOrderBundle/Resources/install/pimcore/classes/CoreShopCartItem.json')->end()
                                        ->scalarNode('type')->defaultValue(CoreShopResourceBundle::PIMCORE_MODEL_TYPE_OBJECT)->cannotBeOverwritten(true)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('order')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->scalarNode('path')->defaultValue('orders')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue('Pimcore\Model\DataObject\CoreShopOrder')->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(OrderInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(PimcoreFactory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(OrderRepository::class)->end()
                                        ->scalarNode('install_file')->defaultValue('@CoreShopOrderBundle/Resources/install/pimcore/classes/CoreShopOrder.json')->end()
                                        ->scalarNode('type')->defaultValue(CoreShopResourceBundle::PIMCORE_MODEL_TYPE_OBJECT)->cannotBeOverwritten(true)->end()
                                        ->arrayNode('pimcore_controller')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('default')->defaultValue(OrderController::class)->end()
                                                ->scalarNode('creation')->defaultValue(OrderCreationController::class)->end()
                                                ->scalarNode('payment')->defaultValue(OrderPaymentController::class)->end()
                                                ->scalarNode('comment')->defaultValue(OrderCommentController::class)->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('order_item')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->scalarNode('path')->defaultValue('items')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue('Pimcore\Model\DataObject\CoreShopOrderItem')->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(OrderItemInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(PimcoreFactory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('install_file')->defaultValue('@CoreShopOrderBundle/Resources/install/pimcore/classes/CoreShopOrderItem.json')->end()
                                        ->scalarNode('type')->defaultValue(CoreShopResourceBundle::PIMCORE_MODEL_TYPE_OBJECT)->cannotBeOverwritten(true)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('order_invoice')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->scalarNode('path')->defaultValue('invoices')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue('Pimcore\Model\DataObject\CoreShopOrderInvoice')->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(OrderInvoiceInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(PimcoreFactory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(OrderInvoiceRepository::class)->end()
                                        ->scalarNode('pimcore_controller')->defaultValue(OrderInvoiceController::class)->end()
                                        ->scalarNode('install_file')->defaultValue('@CoreShopOrderBundle/Resources/install/pimcore/classes/CoreShopOrderInvoice.json')->end()
                                        ->scalarNode('type')->defaultValue(CoreShopResourceBundle::PIMCORE_MODEL_TYPE_OBJECT)->cannotBeOverwritten(true)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('order_invoice_item')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->scalarNode('path')->defaultValue('items')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue('Pimcore\Model\DataObject\CoreShopOrderInvoiceItem')->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(OrderInvoiceItemInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(PimcoreFactory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('install_file')->defaultValue('@CoreShopOrderBundle/Resources/install/pimcore/classes/CoreShopOrderInvoiceItem.json')->end()
                                        ->scalarNode('type')->defaultValue(CoreShopResourceBundle::PIMCORE_MODEL_TYPE_OBJECT)->cannotBeOverwritten(true)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('order_shipment')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->scalarNode('path')->defaultValue('shipments')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue('Pimcore\Model\DataObject\CoreShopOrderShipment')->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(OrderShipmentInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(PimcoreFactory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(OrderShipmentRepository::class)->end()
                                        ->scalarNode('pimcore_controller')->defaultValue(OrderShipmentController::class)->end()
                                        ->scalarNode('install_file')->defaultValue('@CoreShopOrderBundle/Resources/install/pimcore/classes/CoreShopOrderShipment.json')->end()
                                        ->scalarNode('type')->defaultValue(CoreShopResourceBundle::PIMCORE_MODEL_TYPE_OBJECT)->cannotBeOverwritten(true)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('order_shipment_item')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->scalarNode('path')->defaultValue('items')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue('Pimcore\Model\DataObject\CoreShopOrderShipmentItem')->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(OrderShipmentItemInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(PimcoreFactory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('install_file')->defaultValue('@CoreShopOrderBundle/Resources/install/pimcore/classes/CoreShopOrderShipmentItem.json')->end()
                                        ->scalarNode('type')->defaultValue(CoreShopResourceBundle::PIMCORE_MODEL_TYPE_OBJECT)->cannotBeOverwritten(true)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('cart_price_rule_item')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue('Pimcore\Model\DataObject\Fieldcollection\Data\CoreShopProposalCartPriceRuleItem')->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(ProposalCartPriceRuleItemInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(PimcoreFactory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('install_file')->defaultValue('@CoreShopOrderBundle/Resources/install/pimcore/fieldcollections/CoreShopProposalCartPriceRuleItem.json')->end()
                                        ->scalarNode('type')->defaultValue(CoreShopResourceBundle::PIMCORE_MODEL_TYPE_FIELD_COLLECTION)->cannotBeOverwritten(true)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('quote')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->scalarNode('path')->defaultValue('quotes')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue('Pimcore\Model\DataObject\CoreShopQuote')->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(QuoteInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(PimcoreFactory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('install_file')->defaultValue('@CoreShopOrderBundle/Resources/install/pimcore/classes/CoreShopQuote.json')->end()
                                        ->scalarNode('type')->defaultValue(CoreShopResourceBundle::PIMCORE_MODEL_TYPE_OBJECT)->cannotBeOverwritten(true)->end()
                                        ->arrayNode('pimcore_controller')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('default')->defaultValue(QuoteController::class)->end()
                                                ->scalarNode('creation')->defaultValue(QuoteCreationController::class)->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('quote_item')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->scalarNode('path')->defaultValue('items')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue('Pimcore\Model\DataObject\CoreShopQuoteItem')->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(QuoteItemInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(PimcoreFactory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('install_file')->defaultValue('@CoreShopOrderBundle/Resources/install/pimcore/classes/CoreShopQuoteItem.json')->end()
                                        ->scalarNode('type')->defaultValue(CoreShopResourceBundle::PIMCORE_MODEL_TYPE_OBJECT)->cannotBeOverwritten(true)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addPimcoreResourcesSection(ArrayNodeDefinition $node)
    {
        $node->children()
            ->arrayNode('pimcore_admin')
                ->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('js')
                        ->addDefaultsIfNotSet()
                        ->ignoreExtraKeys(false)
                        ->children()
                            ->scalarNode('resource')->defaultValue('/bundles/coreshoporder/pimcore/js/resource.js')->end()
                            ->scalarNode('cart_pricerule_panel')->defaultValue('/bundles/coreshoporder/pimcore/js/cart/pricerules/panel.js')->end()
                            ->scalarNode('cart_pricerule_item')->defaultValue('/bundles/coreshoporder/pimcore/js/cart/pricerules/item.js')->end()
                            ->scalarNode('cart_pricerule_action')->defaultValue('/bundles/coreshoporder/pimcore/js/cart/pricerules/action.js')->end()
                            ->scalarNode('cart_pricerule_condition')->defaultValue('/bundles/coreshoporder/pimcore/js/cart/pricerules/condition.js')->end()
                            ->scalarNode('cart_pricerule_action_discount_amount')->defaultValue('/bundles/coreshoporder/pimcore/js/cart/pricerules/actions/discountAmount.js')->end()
                            ->scalarNode('cart_pricerule_action_discount_percent')->defaultValue('/bundles/coreshoporder/pimcore/js/cart/pricerules/actions/discountPercent.js')->end()
                            ->scalarNode('cart_pricerule_condition_amount')->defaultValue('/bundles/coreshoporder/pimcore/js/cart/pricerules/conditions/amount.js')->end()
                            ->scalarNode('cart_pricerule_condition_nested')->defaultValue('/bundles/coreshoporder/pimcore/js/cart/pricerules/conditions/nested.js')->end()
                            ->scalarNode('cart_pricerule_condition_timespan')->defaultValue('/bundles/coreshoporder/pimcore/js/cart/pricerules/conditions/timespan.js')->end()
                            ->scalarNode('cart_pricerule_condition_voucher')->defaultValue('/bundles/coreshoporder/pimcore/js/cart/pricerules/conditions/voucher.js')->end()
                            ->scalarNode('sale_detail_panel')->defaultValue('/bundles/coreshoporder/pimcore/js/sale/detail/panel.js')->end()
                            ->scalarNode('sale_detail_abstract_block')->defaultValue('/bundles/coreshoporder/pimcore/js/sale/detail/abstractBlock.js')->end()
                            ->scalarNode('sale_detail_abstract_block_header')->defaultValue('/bundles/coreshoporder/pimcore/js/sale/detail/blocks/header.js')->end()
                            ->scalarNode('sale_detail_abstract_block_info')->defaultValue('/bundles/coreshoporder/pimcore/js/sale/detail/blocks/info.js')->end()
                            ->scalarNode('sale_detail_abstract_block_correspondence')->defaultValue('/bundles/coreshoporder/pimcore/js/sale/detail/blocks/correspondence.js')->end()
                            ->scalarNode('sale_detail_abstract_block_customer')->defaultValue('/bundles/coreshoporder/pimcore/js/sale/detail/blocks/customer.js')->end()
                            ->scalarNode('sale_detail_abstract_block_detail')->defaultValue('/bundles/coreshoporder/pimcore/js/sale/detail/blocks/detail.js')->end()
                            ->scalarNode('sale_list')->defaultValue('/bundles/coreshoporder/pimcore/js/sale/list.js')->end()
                            ->scalarNode('sale_creation_panel')->defaultValue('/bundles/coreshoporder/pimcore/js/sale/create/panel.js')->end()
                            ->scalarNode('sale_creation_abstract_step')->defaultValue('/bundles/coreshoporder/pimcore/js/sale/create/abstractStep.js')->end()
                            ->scalarNode('sale_creation_step_preparation')->defaultValue('/bundles/coreshoporder/pimcore/js/sale/create/step/preparation.js')->end()
                            ->scalarNode('sale_creation_step_base')->defaultValue('/bundles/coreshoporder/pimcore/js/sale/create/step/base.js')->end()
                            ->scalarNode('sale_creation_step_products')->defaultValue('/bundles/coreshoporder/pimcore/js/sale/create/step/products.js')->end()
                            ->scalarNode('sale_creation_step_address')->defaultValue('/bundles/coreshoporder/pimcore/js/sale/create/step/address.js')->end()
                            ->scalarNode('sale_creation_step_rules')->defaultValue('/bundles/coreshoporder/pimcore/js/sale/create/step/rules.js')->end()
                            ->scalarNode('sale_creation_step_payment')->defaultValue('/bundles/coreshoporder/pimcore/js/sale/create/step/payment.js')->end()
                            ->scalarNode('sale_creation_step_totals')->defaultValue('/bundles/coreshoporder/pimcore/js/sale/create/step/totals.js')->end()
                            ->scalarNode('order_list')->defaultValue('/bundles/coreshoporder/pimcore/js/order/list.js')->end()
                            ->scalarNode('order_creation')->defaultValue('/bundles/coreshoporder/pimcore/js/order/create/panel.js')->end()
                            ->scalarNode('order_helper')->defaultValue('/bundles/coreshoporder/pimcore/js/helper.js')->end()
                            ->scalarNode('order_detail_panel')->defaultValue('/bundles/coreshoporder/pimcore/js/order/detail/panel.js')->end()
                            ->scalarNode('order_detail_block_header')->defaultValue('/bundles/coreshoporder/pimcore/js/order/detail/blocks/header.js')->end()
                            ->scalarNode('order_detail_block_info')->defaultValue('/bundles/coreshoporder/pimcore/js/order/detail/blocks/info.js')->end()
                            ->scalarNode('order_detail_block_shipment')->defaultValue('/bundles/coreshoporder/pimcore/js/order/detail/blocks/shipment.js')->end()
                            ->scalarNode('order_detail_block_invoice')->defaultValue('/bundles/coreshoporder/pimcore/js/order/detail/blocks/invoice.js')->end()
                            ->scalarNode('order_detail_block_payment')->defaultValue('/bundles/coreshoporder/pimcore/js/order/detail/blocks/payment.js')->end()
                            ->scalarNode('order_detail_block_correspondence')->defaultValue('/bundles/coreshoporder/pimcore/js/order/detail/blocks/correspondence.js')->end()
                            ->scalarNode('order_detail_block_customer')->defaultValue('/bundles/coreshoporder/pimcore/js/order/detail/blocks/customer.js')->end()
                            ->scalarNode('order_detail_block_detail')->defaultValue('/bundles/coreshoporder/pimcore/js/order/detail/blocks/detail.js')->end()
                            ->scalarNode('order_detail_block_comments')->defaultValue('/bundles/coreshoporder/pimcore/js/order/detail/blocks/comments.js')->end()
                            ->scalarNode('order_invoice')->defaultValue('/bundles/coreshoporder/pimcore/js/order/invoice.js')->end()
                            ->scalarNode('order_shipment')->defaultValue('/bundles/coreshoporder/pimcore/js/order/shipment.js')->end()
                            ->scalarNode('order_create_payment')->defaultValue('/bundles/coreshoporder/pimcore/js/order/createPayment.js')->end()
                            ->scalarNode('order_edit_payment')->defaultValue('/bundles/coreshoporder/pimcore/js/order/editPayment.js')->end()
                            ->scalarNode('order_edit_shipment')->defaultValue('/bundles/coreshoporder/pimcore/js/order/editShipment.js')->end()
                            ->scalarNode('order_edit_invoice')->defaultValue('/bundles/coreshoporder/pimcore/js/order/editInvoice.js')->end()
                            ->scalarNode('order_invoice_render')->defaultValue('/bundles/coreshoporder/pimcore/js/order/invoice/render.js')->end()
                            ->scalarNode('order_shipment_render')->defaultValue('/bundles/coreshoporder/pimcore/js/order/shipment/render.js')->end()
                            ->scalarNode('order_change_state')->defaultValue('/bundles/coreshoporder/pimcore/js/order/state/changeState.js')->end()
                            ->scalarNode('quote_list')->defaultValue('/bundles/coreshoporder/pimcore/js/quote/list.js')->end()
                            ->scalarNode('quote_detail_panel')->defaultValue('/bundles/coreshoporder/pimcore/js/quote/detail/panel.js')->end()
                            ->scalarNode('quote_create')->defaultValue('/bundles/coreshoporder/pimcore/js/quote/create/panel.js')->end()
                            ->scalarNode('core_extension_data_cart_price_rule')->defaultValue('/bundles/coreshoporder/pimcore/js/coreExtension/data/coreShopCartPriceRule.js')->end()
                            ->scalarNode('core_extension_tag_cart_price_rule')->defaultValue('/bundles/coreshoporder/pimcore/js/coreExtension/tags/coreShopCartPriceRule.js')->end()
                            ->scalarNode('object_grid_column_order_state')->defaultValue('/bundles/coreshoporder/pimcore/js/object/gridcolumn/operator/OrderState.js')->end()
                            ->scalarNode('object_grid_column_price_formatter')->defaultValue('/bundles/coreshoporder/pimcore/js/object/gridcolumn/operator/PriceFormatter.js')->end()
                        ->end()
                    ->end()
                    ->arrayNode('css')
                        ->addDefaultsIfNotSet()
                        ->ignoreExtraKeys(false)
                        ->children()
                            ->scalarNode('order')->defaultValue('/bundles/coreshoporder/pimcore/css/order.css')->end()
                        ->end()
                    ->end()
                    ->scalarNode('permissions')
                        ->cannotBeOverwritten()
                        ->defaultValue(['cart_price_rule', 'order_list', 'order_detail', 'order_create', 'quote_list', 'quote_detail', 'quote_create'])
                    ->end()
                    ->arrayNode('install')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('admin_translations')->defaultValue(['@CoreShopOrderBundle/Resources/install/pimcore/admin-translations.yml'])->end()
                            ->scalarNode('grid_config')->defaultValue(['@CoreShopOrderBundle/Resources/install/pimcore/grid-config.yml'])->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();
    }
}
