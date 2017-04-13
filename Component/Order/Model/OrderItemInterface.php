<?php

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;

interface OrderItemInterface extends ProposalItemInterface, PimcoreModelInterface
{
    /**
     * @return bool
     */
    public function getIsGiftItem();

    /**
     * @param bool $isGiftItem
     */
    public function setIsGiftItem($isGiftItem);

    /**
     * @return OrderInterface
     */
    public function getOrder();

    /**
     * @param $itemPrice
     * @param bool $withTax
     * @return mixed
     */
    public function setItemPrice($itemPrice, $withTax = true);

    /**
     * @param $itemRetailPrice
     * @param bool $withTax
     * @return mixed
     */
    public function setItemRetailPrice($itemRetailPrice, $withTax = true);

    /**
     * @param $wholesalePrice
     * @return mixed
     */
    public function setItemWholesalePrice($wholesalePrice);

    /**
     * @param $itemTax
     * @return mixed
     */
    public function setItemTax($itemTax);

    /**
     * @param $taxes
     * @return mixed
     */
    public function setTaxes($taxes);

    /**
     * @param $total
     * @param bool $withTax
     * @return mixed
     */
    public function setTotal($total, $withTax = true);

    /**
     * @param $totalTax
     * @return mixed
     */
    public function setTotalTax($totalTax);
}
