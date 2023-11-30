<?php declare(strict_types=1);


namespace CoreShop\Bundle\OrderBundle\EventListener;


use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Order\Factory\CartPriceRuleVoucherCodeUserFactoryInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeUserInterface;
use CoreShop\Component\Order\Repository\CartPriceRuleVoucherCodeUserRepositoryInterface;
use CoreShop\Component\Order\Repository\CartPriceRuleVoucherRepositoryInterface;
use Pimcore\Model\DataObject\CoreShopCustomer;
use Pimcore\Model\DataObject\CoreShopOrder;
use Pimcore\Model\DataObject\CoreShopUser;
use Symfony\Component\Workflow\Event\Event;

class VoucherCodeUserListener
{
    private const CONDITION_CONFIG_PER_USER_KEY = 'maxUsagePerUser';

    public function __construct(
        private CartPriceRuleVoucherCodeUserRepositoryInterface $codePerUserRepository,
        private CartPriceRuleVoucherRepositoryInterface $voucherCodeRepository,
        private CartPriceRuleVoucherCodeUserFactoryInterface $voucherCodeUserFactory

    ) {
    }

    public function addUsageOfVoucherCoderPerUser(Event $event): void
    {
        /** @var \Pimcore\Model\DataObject\CoreShopOrder $order */
        $order = $event->getSubject();

        if (!$order instanceof CoreShopOrder) {
            return;
        }

        $customer = $order->getCustomer();

        if (!$customer instanceof CustomerInterface) {
            return;
        }

        $priceRuleItems = $order->getPriceRuleItems()->getItems();
        $voucherCodeObjects = $this->getVoucherCodesWithPerUserCondition($priceRuleItems);

        $this->handleVoucherCodeUsage($customer, $voucherCodeObjects);
    }

    /**
     * @param \CoreShop\Component\Order\Model\ProposalCartPriceRuleItem[] $rules
     *
     * @return array
     */
    private function getVoucherCodesWithPerUserCondition(array $priceRuleItems): array
    {
        $voucherCodeObjects = [];


        foreach ($priceRuleItems as $priceRuleItem){
            $voucherCodeString = $priceRuleItem->getVoucherCode();
            $condition = $priceRuleItem->getCartPriceRule()->getConditions();

            $maxUsagePerUser = $condition[0]->getConfiguration()[self::CONDITION_CONFIG_PER_USER_KEY];

            if ($maxUsagePerUser !== null){
                $voucherCode =  $this->voucherCodeRepository->findByCode($voucherCodeString);
                $voucherCodeObjects[] = $voucherCode;
            }
        }

        return $voucherCodeObjects;
    }

    private function handleVoucherCodeUsage(CustomerInterface $customer, array $voucherCodes): void
    {
        foreach ($voucherCodes as $voucherCode){
            $perUserEntry = $this->codePerUserRepository->findUsesById($customer, $voucherCode->getId());

            if ($perUserEntry instanceof CartPriceRuleVoucherCodeUserInterface){
                $this->increaseVoucherCodeUsageByOne($perUserEntry->getId());
            }

            if ($perUserEntry === null){
                $voucherCodeUser = $this->voucherCodeUserFactory->createWithInitialData($customer,$voucherCode);
                $this->createNewVoucherCodePerUserEntry($voucherCodeUser);
            }
        }
    }

    private function increaseVoucherCodeUsageByOne(int $perUserEntryId): void
    {
        $this->codePerUserRepository->updateCodeUserUsage($perUserEntryId);
    }

    private function createNewVoucherCodePerUserEntry(CartPriceRuleVoucherCodeUserInterface $voucherCodeUser): void
    {
        $this->codePerUserRepository->addCodeUserUsage($voucherCodeUser);
    }
}
