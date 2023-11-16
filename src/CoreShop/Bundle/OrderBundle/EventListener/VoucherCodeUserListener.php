<?php declare(strict_types=1);


namespace CoreShop\Bundle\OrderBundle\EventListener;


use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeUserInterface;
use CoreShop\Component\Order\Repository\CartPriceRuleVoucherCodeUserRepositoryInterface;
use Pimcore\Model\DataObject\CoreShopOrder;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Workflow\Event\Event;

class VoucherCodeUserListener
{
    private const CONDITION_CONFIG_PER_USER_KEY = 'maxUsagePerUser';

    public function __construct(
        private CartPriceRuleVoucherCodeUserRepositoryInterface $codePerUserRepository,
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

        $adjustmentItems = $order->getAdjustmentItems();
        foreach ($adjustmentItems as $adjustmentItem) {
        }


        $userId = $this->security->getUser()->getId();

        /** @var  $rules */
        $rules = $order->getPriceRules();

        $voucherCodes = $this->getvoucherCodesWithPerUserCondition($rules);

     //   $this->handleVoucherCodeUsage($userId, $voucherCodeIds);
    }

    /**
     * @param \CoreShop\Component\Order\Model\PriceRuleItemInterface[] $rules
     *
     * @return array
     */
    private function getvoucherCodesWithPerUserCondition(array $rules): array
    {
        $voucherCodes = [];

        foreach ($rules as $rule){
            $conditions = $rule->getConditions();

            foreach ($conditions as $condition){
                $maxUsagePerUser = $condition->getConfiguration()[self::CONDITION_CONFIG_PER_USER_KEY];
                $af = 12;

                if ($maxUsagePerUser !== null){
                    $codes = $rule->getVoucherCodes();
                    $asfc = 123;
                    //add voucher code in array
                }
            }
        }

        return $voucherCodes;
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
        $this->codePerUserRepository->updateCodeUserUsage($userId);
    }

    private function createNewVoucherCodePerUserEntry(int $userId, array $voucherCodeIds): void
    {
        $this->codePerUserRepository->addCodeUserUsage();
    }



}
