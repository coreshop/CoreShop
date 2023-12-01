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

    protected int $customerId;

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

    public function decrementUses(): void
    {
        $this->uses--;
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function setCustomerId(int $customerId): void
    {
        $this->customerId = $customerId;
    }
}
