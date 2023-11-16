<?php declare(strict_types=1);


namespace CoreShop\Component\Order\Model;


use CoreShop\Component\Resource\Model\SetValuesTrait;

class CartPriceRuleVoucherCodeUser implements CartPriceRuleVoucherCodeUserInterface
{
    use SetValuesTrait;

    /**
     * @var int
     */
    protected $id;

    /** @var \CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface */
    protected $voucherCode;

    protected int $userId;

    protected int $uses;

    public function getId()
    {
        return $this->id;
    }

    public function getVoucherCode(): CartPriceRuleVoucherCodeInterface
    {
        return $this->voucherCode;
    }

    public function setVoucherCode($voucherCode): void
    {
        $this->voucherCode = $voucherCode;
    }

    public function getUses(): int
    {
        return $this->uses;
    }

    public function setUses(int $uses): int
    {
        return $this->uses = $uses;
    }

    public function incrementUses(): void
    {
        $this->uses++;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

}
