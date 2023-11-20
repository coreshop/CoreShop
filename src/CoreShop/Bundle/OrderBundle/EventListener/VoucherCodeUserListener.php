<?php declare(strict_types=1);


namespace CoreShop\Bundle\OrderBundle\EventListener;


use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeUserInterface;
use CoreShop\Component\Order\Repository\CartPriceRuleVoucherCodeUserRepositoryInterface;
use CoreShop\Component\Order\Repository\CartPriceRuleVoucherRepositoryInterface;
use Pimcore\Model\DataObject\CoreShopOrder;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Workflow\Event\Event;

class VoucherCodeUserListener
{
    private const CONDITION_CONFIG_PER_USER_KEY = 'maxUsagePerUser';

    public function __construct(
        private CartPriceRuleVoucherCodeUserRepositoryInterface $codePerUserRepository,
        private CartPriceRuleVoucherRepositoryInterface $voucherCodeRepository,
        private Security $security,
    ) {
    }

    public function increaseUsageOfVoucherCoderFromUser(Event $event)
    {
        /** @var \Pimcore\Model\DataObject\CoreShopOrder $order */
        $order = $event->getSubject();

        if (!$order instanceof CoreShopOrder) {
            return;
        }

        $userId = $this->security->getUser()->getId();
        $priceRuleItems = $order->getPriceRuleItems()->getItems();
        $voucherCodeObjects = $this->getVoucherCodesWithPerUserCondition($priceRuleItems);

        $this->handleVoucherCodeUsage($userId, $voucherCodeObjects);
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



    private function handleVoucherCodeUsage(int $userId, array $voucherCodes): void
    {
        foreach ($voucherCodes as $voucherCode){
            $perUserEntry = $this->codePerUserRepository->findByUsesById($userId, $voucherCode->getId());

            if ($perUserEntry instanceof CartPriceRuleVoucherCodeUserInterface){
                $this->increaseVoucherCodeUsageByOne($perUserEntry->getId());
            }

            if ($perUserEntry === null){
                $this->createNewVoucherCodePerUserEntry($userId, $voucherCode);
            }
        }
    }

    private function increaseVoucherCodeUsageByOne(int $perUserEntryId): void
    {
        $this->codePerUserRepository->updateCodeUserUsage($perUserEntryId);
    }

    private function createNewVoucherCodePerUserEntry(int $userId, CartPriceRuleVoucherCodeInterface $voucherCode): void
    {
            $this->codePerUserRepository->addCodeUserUsage($userId, $voucherCode);
    }
}
