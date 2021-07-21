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

declare(strict_types=1);

namespace CoreShop\Bundle\OrderBundle\DependencyInjection;

use CoreShop\Bundle\OrderBundle\Controller\AddressCreationController;
use CoreShop\Bundle\OrderBundle\Controller\CartPriceRuleController;
use CoreShop\Bundle\OrderBundle\Controller\CustomerCreationController;
use CoreShop\Bundle\OrderBundle\Controller\OrderCommentController;
use CoreShop\Bundle\OrderBundle\Controller\OrderController;
use CoreShop\Bundle\OrderBundle\Controller\OrderCreationController;
use CoreShop\Bundle\OrderBundle\Controller\OrderEditController;
use CoreShop\Bundle\OrderBundle\Controller\OrderInvoiceController;
use CoreShop\Bundle\OrderBundle\Controller\OrderPaymentController;
use CoreShop\Bundle\OrderBundle\Controller\OrderShipmentController;
use CoreShop\Bundle\OrderBundle\Doctrine\ORM\CartPriceRuleRepository;
use CoreShop\Bundle\OrderBundle\Doctrine\ORM\CartPriceRuleVoucherRepository;
use CoreShop\Bundle\OrderBundle\Form\Type\CartPriceRuleTranslationType;
use CoreShop\Bundle\OrderBundle\Form\Type\CartPriceRuleType;
use CoreShop\Bundle\OrderBundle\Pimcore\Repository\OrderInvoiceRepository;
use CoreShop\Bundle\OrderBundle\Pimcore\Repository\OrderItemRepository;
use CoreShop\Bundle\OrderBundle\Pimcore\Repository\OrderRepository;
use CoreShop\Bundle\OrderBundle\Pimcore\Repository\OrderShipmentRepository;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Component\Order\Factory\OrderItemUnitFactory;
use CoreShop\Component\Order\Model\AdjustmentInterface;
use CoreShop\Component\Order\Model\CartPriceRule;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Model\CartPriceRuleTranslation;
use CoreShop\Component\Order\Model\CartPriceRuleTranslationInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCode;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderInvoiceInterface;
use CoreShop\Component\Order\Model\OrderInvoiceItemInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Model\OrderItemUnitInterface;
use CoreShop\Component\Order\Model\OrderShipmentInterface;
use CoreShop\Component\Order\Model\OrderShipmentItemInterface;
use CoreShop\Component\Order\Model\ProposalCartPriceRuleItemInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Resource\Factory\Factory;
use CoreShop\Component\Resource\Factory\PimcoreFactory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('core_shop_order');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->booleanNode('legacy_serialization')->defaultTrue()->end()
            ->end();
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
                    ->scalarNode('order')->defaultValue(OrderInterface::class)->cannotBeEmpty()->end()
                    ->scalarNode('order_item')->defaultValue(OrderItemInterface::class)->cannotBeEmpty()->end()
                    ->scalarNode('order_item_unit')->defaultValue(OrderItemUnitInterface::class)->cannotBeEmpty()->end()
                    ->scalarNode('order_invoice')->defaultValue(OrderInvoiceInterface::class)->cannotBeEmpty()->end()
                    ->scalarNode('order_invoice_item')->defaultValue(OrderInvoiceItemInterface::class)->cannotBeEmpty()->end()
                    ->scalarNode('order_shipment')->defaultValue(OrderShipmentInterface::class)->cannotBeEmpty()->end()
                    ->scalarNode('order_shipment_item')->defaultValue(OrderShipmentItemInterface::class)->cannotBeEmpty()->end()
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
                                ->arrayNode('translation')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->variableNode('options')->end()
                                        ->arrayNode('classes')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->defaultValue(CartPriceRuleTranslation::class)->cannotBeEmpty()->end()
                                                ->scalarNode('interface')->defaultValue(CartPriceRuleTranslationInterface::class)->cannotBeEmpty()->end()
                                                ->scalarNode('repository')->cannotBeEmpty()->end()
                                                ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                                ->scalarNode('form')->defaultValue(CartPriceRuleTranslationType::class)->cannotBeEmpty()->end()
                                            ->end()
                                        ->end()
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
                        ->arrayNode('order')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('path')
                                    ->children()
                                        ->scalarNode('order')->defaultValue('orders')->end()
                                        ->scalarNode('quote')->defaultValue('quotes')->end()
                                        ->scalarNode('cart')->defaultValue('carts')->end()
                                    ->end()
                                ->end()
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
                                                ->scalarNode('edit')->defaultValue(OrderEditController::class)->end()
                                                ->scalarNode('payment')->defaultValue(OrderPaymentController::class)->end()
                                                ->scalarNode('comment')->defaultValue(OrderCommentController::class)->end()
                                                ->scalarNode('customer_creation')->defaultValue(CustomerCreationController::class)->end()
                                                ->scalarNode('address_creation')->defaultValue(AddressCreationController::class)->end()
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
                                        ->scalarNode('repository')->defaultValue(OrderItemRepository::class)->cannotBeEmpty()->end()
                                        ->scalarNode('install_file')->defaultValue('@CoreShopOrderBundle/Resources/install/pimcore/classes/CoreShopOrderItem.json')->end()
                                        ->scalarNode('type')->defaultValue(CoreShopResourceBundle::PIMCORE_MODEL_TYPE_OBJECT)->cannotBeOverwritten(true)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('order_item_unit')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->scalarNode('path')->defaultValue('items')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue('Pimcore\Model\DataObject\CoreShopOrderItemUnit')->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(OrderItemUnitInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(OrderItemUnitFactory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('install_file')->defaultValue('@CoreShopOrderBundle/Resources/install/pimcore/classes/CoreShopOrderItemUnit.json')->end()
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
                        ->arrayNode('adjustment')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue('Pimcore\Model\DataObject\Fieldcollection\Data\CoreShopAdjustment')->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(AdjustmentInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(PimcoreFactory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('install_file')->defaultValue('@CoreShopOrderBundle/Resources/install/pimcore/fieldcollections/CoreShopAdjustment.json')->end()
                                        ->scalarNode('type')->defaultValue(CoreShopResourceBundle::PIMCORE_MODEL_TYPE_FIELD_COLLECTION)->cannotBeOverwritten(true)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
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
                        ->useAttributeAsKey('name')
                        ->prototype('scalar')->end()
                    ->end()
                    ->arrayNode('css')
                        ->useAttributeAsKey('name')
                        ->prototype('scalar')->end()
                    ->end()
                    ->arrayNode('editmode_js')
                        ->useAttributeAsKey('name')
                        ->prototype('scalar')->end()
                    ->end()
                    ->arrayNode('editmode_css')
                        ->useAttributeAsKey('name')
                        ->prototype('scalar')->end()
                    ->end()
                    ->scalarNode('permissions')
                        ->cannotBeOverwritten()
                        ->defaultValue([
                            'cart_price_rule',
                            'order_list',
                            'order_detail',
                            'order_create',
                            'quote_list',
                            'quote_detail',
                            'quote_create',
                            'cart_list',
                            'cart_detail',
                            'cart_create',
                        ])
                    ->end()
                    ->arrayNode('install')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->arrayNode('admin_translations')
                                ->treatNullLike([])
                                ->scalarPrototype()->end()
                                ->defaultValue(['@CoreShopOrderBundle/Resources/install/pimcore/admin-translations.yml'])
                            ->end()
                            ->arrayNode('grid_config')
                                ->treatNullLike([])
                                ->scalarPrototype()->end()
                                ->defaultValue(['@CoreShopOrderBundle/Resources/install/pimcore/grid-config.yml'])
                            ->end()
                            ->arrayNode('translations')
                                ->treatNullLike([])
                                ->scalarPrototype()->end()
                                ->defaultValue(['@CoreShopOrderBundle/Resources/install/pimcore/translations.yml'])
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();
    }
}
