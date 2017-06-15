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
use CoreShop\Bundle\OrderBundle\Controller\OrderController;
use CoreShop\Bundle\OrderBundle\Controller\OrderInvoiceController;
use CoreShop\Bundle\OrderBundle\Controller\OrderShipmentController;
use CoreShop\Bundle\OrderBundle\Doctrine\ORM\CartPriceRuleVoucherRepository;
use CoreShop\Bundle\OrderBundle\Form\Type\CartPriceRuleType;
use CoreShop\Bundle\OrderBundle\Pimcore\Repository\CartRepository;
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
        $this->addPimcoreJsSection($rootNode);

        return $treeBuilder;
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
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(CartPriceRule::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(CartPriceRuleInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('admin_controller')->defaultValue(CartPriceRuleController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
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
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue('Pimcore\Model\Object\CoreShopCart')->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(CartInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(PimcoreFactory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('admin_controller')->cannotBeEmpty()->end()
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
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue('Pimcore\Model\Object\CoreShopCartItem')->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(CartItemInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(PimcoreFactory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('admin_controller')->cannotBeEmpty()->end()
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
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue('Pimcore\Model\Object\CoreShopOrder')->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(OrderInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(PimcoreFactory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(OrderRepository::class)->end()
                                        ->scalarNode('admin_controller')->defaultValue(OrderController::class)->end()
                                        ->scalarNode('install_file')->defaultValue('@CoreShopOrderBundle/Resources/install/pimcore/classes/CoreShopOrder.json')->end()
                                        ->scalarNode('type')->defaultValue(CoreShopResourceBundle::PIMCORE_MODEL_TYPE_OBJECT)->cannotBeOverwritten(true)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('order_item')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue('Pimcore\Model\Object\CoreShopOrderItem')->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(OrderItemInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(PimcoreFactory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('admin_controller')->cannotBeEmpty()->end()
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
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue('Pimcore\Model\Object\CoreShopOrderInvoice')->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(OrderInvoiceInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(PimcoreFactory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(OrderInvoiceRepository::class)->end()
                                        ->scalarNode('admin_controller')->defaultValue(OrderInvoiceController::class)->end()
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
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue('Pimcore\Model\Object\CoreShopOrderInvoiceItem')->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(OrderInvoiceItemInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(PimcoreFactory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('admin_controller')->cannotBeEmpty()->end()
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
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue('Pimcore\Model\Object\CoreShopOrderShipment')->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(OrderShipmentInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(PimcoreFactory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(OrderShipmentRepository::class)->end()
                                        ->scalarNode('admin_controller')->defaultValue(OrderShipmentController::class)->end()
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
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue('Pimcore\Model\Object\CoreShopOrderShipmentItem')->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(OrderShipmentItemInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(PimcoreFactory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('admin_controller')->cannotBeEmpty()->end()
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
                                        ->scalarNode('model')->defaultValue('Pimcore\Model\Object\Fieldcollection\Data\CoreShopProposalCartPriceRuleItem')->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(ProposalCartPriceRuleItemInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(PimcoreFactory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('admin_controller')->cannotBeEmpty()->end()
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
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue('Pimcore\Model\Object\CoreShopQuote')->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(QuoteInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(PimcoreFactory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('admin_controller')->cannotBeEmpty()->end()
                                        ->scalarNode('install_file')->defaultValue('@CoreShopOrderBundle/Resources/install/pimcore/classes/CoreShopQuote.json')->end()
                                        ->scalarNode('type')->defaultValue(CoreShopResourceBundle::PIMCORE_MODEL_TYPE_OBJECT)->cannotBeOverwritten(true)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('quote_item')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue('Pimcore\Model\Object\CoreShopQuoteItem')->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(QuoteItemInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(PimcoreFactory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('admin_controller')->cannotBeEmpty()->end()
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
    private function addPimcoreJsSection(ArrayNodeDefinition $node)
    {
        $node->children()
            ->arrayNode('pimcore_admin')
                ->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('js')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('cart_pricerule_panel')->defaultValue('/bundles/coreshoporder/pimcore/js/cart/pricerules/panel.js')->end()
                            ->scalarNode('cart_pricerule_item')->defaultValue('/bundles/coreshoporder/pimcore/js/cart/pricerules/item.js')->end()
                            ->scalarNode('cart_pricerule_action')->defaultValue('/bundles/coreshoporder/pimcore/js/cart/pricerules/action.js')->end()
                            ->scalarNode('cart_pricerule_condition')->defaultValue('/bundles/coreshoporder/pimcore/js/cart/pricerules/condition.js')->end()
                            ->scalarNode('cart_pricerule_action_discount_amount')->defaultValue('/bundles/coreshoporder/pimcore/js/cart/pricerules/actions/discountAmount.js')->end()
                            ->scalarNode('cart_pricerule_action_discount_percent')->defaultValue('/bundles/coreshoporder/pimcore/js/cart/pricerules/actions/discountPercent.js')->end()
                            ->scalarNode('cart_pricerule_condition_amount')->defaultValue('/bundles/coreshoporder/pimcore/js/cart/pricerules/conditions/amount.js')->end()
                            ->scalarNode('cart_pricerule_condition_nested')->defaultValue('/bundles/coreshoporder/pimcore/js/cart/pricerules/conditions/nested.js')->end()
                            ->scalarNode('cart_pricerule_condition_timespan')->defaultValue('/bundles/coreshoporder/pimcore/js/cart/pricerules/conditions/timespan.js')->end()
                            ->scalarNode('sale_detail')->defaultValue('/bundles/coreshoporder/pimcore/js/sale/detail.js')->end()
                            ->scalarNode('sale_list')->defaultValue('/bundles/coreshoporder/pimcore/js/sale/list.js')->end()
                            ->scalarNode('order_list')->defaultValue('/bundles/coreshoporder/pimcore/js/order/list.js')->end()
                            ->scalarNode('quote_list')->defaultValue('/bundles/coreshoporder/pimcore/js/quote/list.js')->end()
                            ->scalarNode('order_helper')->defaultValue('/bundles/coreshoporder/pimcore/js/helper.js')->end()
                            ->scalarNode('order_detail')->defaultValue('/bundles/coreshoporder/pimcore/js/order/detail.js')->end()
                            ->scalarNode('order_invoice')->defaultValue('/bundles/coreshoporder/pimcore/js/order/invoice.js')->end()
                            ->scalarNode('order_shipment')->defaultValue('/bundles/coreshoporder/pimcore/js/order/shipment.js')->end()
                            ->scalarNode('order_address')->defaultValue('/bundles/coreshoporder/pimcore/js/order/address.js')->end()
                            ->scalarNode('order_create_payment')->defaultValue('/bundles/coreshoporder/pimcore/js/order/createPayment.js')->end()
                            ->scalarNode('order_edit_payment')->defaultValue('/bundles/coreshoporder/pimcore/js/order/editPayment.js')->end()
                            ->scalarNode('order_create_order')->defaultValue('/bundles/coreshoporder/pimcore/js/order/create/order.js')->end()
                            ->scalarNode('order_invoice_render')->defaultValue('/bundles/coreshoporder/pimcore/js/order/invoice/render.js')->end()
                            ->scalarNode('order_shipment_render')->defaultValue('/bundles/coreshoporder/pimcore/js/order/shipment/render.js')->end()
                            ->scalarNode('core_extension_data_cart_price_rule')->defaultValue('/bundles/coreshoporder/pimcore/js/coreExtension/data/coreShopCartPriceRule.js')->end()
                            ->scalarNode('core_extension_tag_cart_price_rule')->defaultValue('/bundles/coreshoporder/pimcore/js/coreExtension/tags/coreShopCartPriceRule.js')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();
    }
}
