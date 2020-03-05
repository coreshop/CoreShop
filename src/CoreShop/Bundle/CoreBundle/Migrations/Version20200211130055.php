<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Core\Model\CartItemInterface;
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Core\Model\CartInterface;
use CoreShop\Component\Core\Model\OrderItemInterface;
use CoreShop\Component\Order\Manager\CartManagerInterface;
use CoreShop\Component\Order\OrderSaleStates;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Pimcore\Model\DataObject\Fieldcollection;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20200211130055 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $originalCartClass = 'Pimcore\\Model\\DataObject\\CoreShopCart';
        $originalCartListClass = 'Pimcore\\Model\\DataObject\\CoreShopCart\\Listing';

        if (!class_exists($originalCartClass)) {
            $this->writeMessage(
                sprintf(
                    'Cart Class not found, you must have changed it, please copy this class (%s) and change it to work with your cart Class',
                    __FILE__
                )
            );

            return;
        }

        /**
         * @var CartManagerInterface $cartManager
         */
        $cartManager = $this->container->get('coreshop.cart.manager');

        /**
         * @var FactoryInterface $orderFactory
         */
        $orderFactory = $this->container->get('coreshop.factory.order');

        /**
         * @var FactoryInterface $orderItemFactory
         */
        $orderItemFactory = $this->container->get('coreshop.factory.order_item');

        $list = new $originalCartListClass();
        $list->setCondition('order__id IS NULL');
        $list->load();

        /**
         * @var CartInterface $cart
         */
        foreach ($list as $cart) {
            /**
             * @var OrderInterface $order
             */
            $order = $orderFactory->createNew();

            $order->setSaleState(OrderSaleStates::STATE_CART);
            $order->setPriceRuleItems($cart->getPriceRuleItems());
            $order->setPaymentSettings($cart->getPaymentSettings());
            $order->setPaymentProvider($cart->getPaymentProvider());
            $order->setCarrier($cart->getCarrier());
            $order->setCustomer($order->getCustomer());
            $order->setCurrency($cart->getCurrency());
            $order->setTotal($cart->getTotal(true), true);
            $order->setTotal($cart->getTotal(false), false);
            $order->setSubtotal($cart->getSubtotal(true), true);
            $order->setSubtotal($cart->getSubtotal(false), false);
            $order->setTaxes($cart->getTaxes() ? clone $cart->getTaxes() : new Fieldcollection());

            /**
             * @var CartItemInterface $item
             */
            foreach ($cart->getItems() as $item) {
                /**
                 * @var OrderItemInterface $orderItem
                 */
                $orderItem = $orderItemFactory->createNew();

                $orderItem->setProduct($item->getProduct());
                $orderItem->setItemRetailPrice($item->getItemRetailPrice(true), true);
                $orderItem->setItemRetailPrice($item->getItemRetailPrice(false), false);
                $orderItem->setItemDiscountPrice($item->getItemDiscountPrice(true), true);
                $orderItem->setItemDiscountPrice($item->getItemDiscountPrice(false), false);
                $orderItem->setItemDiscount($item->getItemDiscount(true), true);
                $orderItem->setItemDiscount($item->getItemDiscount(false), false);
                $orderItem->setItemPrice($item->getItemPrice(true), true);
                $orderItem->setItemPrice($item->getItemPrice(false), false);
                $orderItem->setTotal($item->getTotal(true), true);
                $orderItem->setTotal($item->getTotal(false), false);
                $orderItem->setItemTax($item->getItemTax());
                $orderItem->setTaxes($item->getTaxes() ? clone $item->getTaxes() : new Fieldcollection());
                $orderItem->setItemWholesalePrice($item->getItemWholesalePrice());
                $orderItem->setUnitDefinition($item->getUnitDefinition());
                $orderItem->setDigitalProduct($item->getDigitalProduct());
                $orderItem->setIsGiftItem($item->getIsGiftItem());
                $orderItem->setQuantity($item->getQuantity());
                $orderItem->setDefaultUnitQuantity($item->getDefaultUnitQuantity());

                foreach ($item->getAdjustments() as $adjustment) {
                    $orderItemAdj = clone $adjustment;

                    $orderItem->addAdjustment($orderItemAdj);
                }

                $order->addItem($orderItem);
            }

            foreach ($cart->getAdjustments() as $adjustment) {
                $orderAdj = clone $adjustment;

                $order->addAdjustment($orderAdj);
            }

            $cartManager->persistCart($order);
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
