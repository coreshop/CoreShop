<?php

namespace CoreShop\Component\Order\Cart\Rule;

use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\ProposalCartPriceRuleItemInterface;
use CoreShop\Component\Order\Repository\CartPriceRuleVoucherRepositoryInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Rule\Model\ActionInterface;
use Doctrine\ORM\EntityManagerInterface;

class CartPriceRuleOrderProcessor implements CartPriceRuleOrderProcessorInterface
{
    /**
     * @var CartPriceRuleVoucherRepositoryInterface
     */
    private $voucherCodeRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ServiceRegistryInterface
     */
    private $actionServiceRegistry;

    /**
     * @var FactoryInterface
     */
    private $cartPriceRuleItemFactory;

    /**
     * @param CartPriceRuleVoucherRepositoryInterface $voucherCodeRepository
     * @param EntityManagerInterface $entityManager
     * @param ServiceRegistryInterface $actionServiceRegistry
     * @param FactoryInterface $cartPriceRuleItemFactory
     */
    public function __construct(
        CartPriceRuleVoucherRepositoryInterface $voucherCodeRepository,
        EntityManagerInterface $entityManager,
        ServiceRegistryInterface $actionServiceRegistry,
        FactoryInterface $cartPriceRuleItemFactory
    )
    {
        $this->voucherCodeRepository = $voucherCodeRepository;
        $this->entityManager = $entityManager;
        $this->actionServiceRegistry = $actionServiceRegistry;
        $this->cartPriceRuleItemFactory = $cartPriceRuleItemFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function process(CartPriceRuleInterface $cartPriceRule, $usedCode, CartInterface $cart, OrderInterface $order)
    {
        $voucherCode = $this->voucherCodeRepository->findByCode($usedCode);

        if ($voucherCode instanceof CartPriceRuleVoucherCodeInterface) {
            $voucherCode->setUses($voucherCode->getUses() + 1);
            $voucherCode->setUsed(true);

            $this->entityManager->persist($voucherCode);
            $this->entityManager->flush();

            $discountNet = 0;
            $discountGross = 0;

            foreach ($cartPriceRule->getActions() as $action) {
                if ($action instanceof ActionInterface) {
                    $actionCommand = $this->actionServiceRegistry->get($action->getType());

                    $discountNet += $actionCommand->getDiscount($cart, false, $action->getConfiguration());
                    $discountGross += $actionCommand->getDiscount($cart, true, $action->getConfiguration());
                }
            }

            /**
             * @var $priceRuleItem ProposalCartPriceRuleItemInterface
             */
            if ($priceRuleItem === null) {
                $priceRuleItem = $this->cartPriceRuleItemFactory->createNew();
            }

            $priceRuleItem->setCartPriceRule($cartPriceRule);
            $priceRuleItem->setVoucherCode($usedCode);
            $priceRuleItem->setDiscount($discountNet, false);
            $priceRuleItem->setDiscount($discountGross, true);

            $order->addPriceRule($priceRuleItem);

            return true;
        }
    }
}